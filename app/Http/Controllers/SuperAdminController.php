<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\SystemSetting;
use App\Models\Hostel;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Password;

class SuperAdminController extends Controller
{
    // Middleware is applied at route level

    /**
     * Display the super admin dashboard
     */
    public function dashboard()
    {
        $stats = [
            'total_users' => User::count(),
            'total_super_admins' => User::superAdmins()->count(),
            'total_hostels' => Hostel::count(),
            'total_rooms' => Room::count(),
            'demo_mode' => SystemSetting::getValue('demo_mode', false),
            'max_hostels' => SystemSetting::getValue('max_hostels', 10),
            'max_floors_per_hostel' => SystemSetting::getValue('max_floors_per_hostel', 5),
        ];

        $recentUsers = User::latest()->take(5)->get();
        $systemSettings = SystemSetting::orderBy('key')->get();

        return view('super-admin.dashboard', compact('stats', 'recentUsers', 'systemSettings'));
    }

    /**
     * Display system settings
     */
    public function settings()
    {
        $settings = SystemSetting::orderBy('key')->get();

        // Create a key-value array for easy access in the view
        $settingsArray = [];
        foreach ($settings as $setting) {
            $settingsArray[$setting->key] = $setting->value;
        }

        return view('super-admin.settings', compact('settings', 'settingsArray'));
    }

    /**
     * Update system settings
     */
    public function updateSettings(Request $request)
    {
        $request->validate([
            'demo_mode' => 'boolean',
            'tenant_login_maintenance' => 'boolean',
            'max_hostels' => 'integer|min:1|max:100',
            'max_floors_per_hostel' => 'integer|min:1|max:20',
            'max_rooms_per_floor' => 'integer|min:1|max:50',
            'max_beds_per_room' => 'integer|min:1|max:20',
            'app_name' => 'string|max:255',
            'app_logo' => 'nullable|string|max:255',
            'app_logo_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'primary_color' => 'string|max:7',
            'secondary_color' => 'string|max:7',
            'favicon_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,ico|max:1024',
        ]);

        // Handle file uploads
        $appLogoUrl = $request->app_logo ?? '';
        if ($request->hasFile('app_logo_file')) {
            $logoFile = $request->file('app_logo_file');
            $logoPath = $logoFile->store('uploads/logos', 'public');
            $appLogoUrl = asset('storage/' . $logoPath);
        }

        $faviconUrl = $request->favicon ?? '';
        if ($request->hasFile('favicon_file')) {
            $faviconFile = $request->file('favicon_file');
            $faviconPath = $faviconFile->store('uploads/favicons', 'public');
            $faviconUrl = asset('storage/' . $faviconPath);
        }

        $settings = [
            'demo_mode' => ['value' => $request->demo_mode ?? false, 'type' => 'boolean', 'description' => 'Enable demo mode'],
            'tenant_login_maintenance' => ['value' => $request->tenant_login_maintenance ?? false, 'type' => 'boolean', 'description' => 'Enable tenant login maintenance mode'],
            'max_hostels' => ['value' => $request->max_hostels ?? 10, 'type' => 'integer', 'description' => 'Maximum number of hostels allowed'],
            'max_floors_per_hostel' => ['value' => $request->max_floors_per_hostel ?? 5, 'type' => 'integer', 'description' => 'Maximum floors per hostel'],
            'max_rooms_per_floor' => ['value' => $request->max_rooms_per_floor ?? 20, 'type' => 'integer', 'description' => 'Maximum rooms per floor'],
            'max_beds_per_room' => ['value' => $request->max_beds_per_room ?? 10, 'type' => 'integer', 'description' => 'Maximum beds per room'],
            'app_name' => ['value' => $request->app_name ?? 'Hostel CRM', 'type' => 'string', 'description' => 'Application name', 'is_public' => true],
            'app_logo' => ['value' => $appLogoUrl, 'type' => 'string', 'description' => 'Application logo URL', 'is_public' => true],
            'primary_color' => ['value' => $request->primary_color ?? '#3B82F6', 'type' => 'string', 'description' => 'Primary theme color', 'is_public' => true],
            'secondary_color' => ['value' => $request->secondary_color ?? '#6B7280', 'type' => 'string', 'description' => 'Secondary theme color', 'is_public' => true],
            'favicon' => ['value' => $faviconUrl, 'type' => 'string', 'description' => 'Favicon URL', 'is_public' => true],
        ];


        DB::transaction(function () use ($settings) {
            foreach ($settings as $key => $config) {
                SystemSetting::setValue(
                    $key,
                    $config['value'],
                    $config['type'],
                    $config['description'],
                    $config['is_public'] ?? false
                );
            }
        });

        return redirect()->route('super-admin.settings')
            ->with('success', 'System settings updated successfully.');
    }

    /**
     * Display user management
     */
    public function users()
    {
        $users = User::visibleTo(auth()->user())
            ->with('tenantProfile')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('super-admin.users', compact('users'));
    }

    /**
     * Promote user to super admin
     */
    public function promoteToSuperAdmin(User $user)
    {
        if ($user->isSuperAdmin()) {
            return redirect()->back()->with('error', 'User is already a super admin.');
        }

        $user->update(['is_super_admin' => true]);

        return redirect()->back()
            ->with('success', "User {$user->name} has been promoted to super admin.");
    }

    /**
     * Demote super admin to regular user
     */
    public function demoteFromSuperAdmin(User $user)
    {
        if (!$user->isSuperAdmin()) {
            return redirect()->back()->with('error', 'User is not a super admin.');
        }

        // Prevent demoting the last super admin
        if (User::superAdmins()->count() <= 1) {
            return redirect()->back()->with('error', 'Cannot demote the last super admin.');
        }

        $user->update(['is_super_admin' => false]);

        return redirect()->back()
            ->with('success', "User {$user->name} has been demoted from super admin.");
    }

    /**
     * Create a new super admin user
     */
    public function createSuperAdmin(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'is_super_admin' => true,
            'is_tenant' => false,
        ]);

        return redirect()->route('super-admin.users')
            ->with('success', "Super admin user {$user->name} created successfully.");
    }

    /**
     * System health check
     */
    public function systemHealth()
    {
        $health = [
            'database' => $this->checkDatabase(),
            'storage' => $this->checkStorage(),
            'cache' => $this->checkCache(),
            'mail' => $this->checkMail(),
        ];

        return view('super-admin.system-health', compact('health'));
    }

    /**
     * Check database connectivity
     */
    private function checkDatabase()
    {
        try {
            DB::connection()->getPdo();
            return ['status' => 'healthy', 'message' => 'Database connection successful'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Database connection failed: ' . $e->getMessage()];
        }
    }

    /**
     * Check storage permissions
     */
    private function checkStorage()
    {
        $storagePath = storage_path();
        if (is_writable($storagePath)) {
            return ['status' => 'healthy', 'message' => 'Storage is writable'];
        }
        return ['status' => 'warning', 'message' => 'Storage is not writable'];
    }

    /**
     * Check cache functionality
     */
    private function checkCache()
    {
        try {
            cache()->put('health_check', 'test', 60);
            $value = cache()->get('health_check');
            if ($value === 'test') {
                return ['status' => 'healthy', 'message' => 'Cache is working'];
            }
            return ['status' => 'warning', 'message' => 'Cache test failed'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Cache error: ' . $e->getMessage()];
        }
    }

    /**
     * Check mail configuration
     */
    private function checkMail()
    {
        $demoMode = SystemSetting::getValue('demo_mode', false);
        if ($demoMode) {
            return ['status' => 'info', 'message' => 'Mail disabled in demo mode'];
        }

        try {
            // Basic mail configuration check - using Laravel's default mail config
            $mailDriver = config('mail.default');
            if (empty($mailDriver) || $mailDriver === 'log') {
                return ['status' => 'warning', 'message' => 'Mail using log driver'];
            }
            return ['status' => 'healthy', 'message' => 'Mail configured'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Mail error: ' . $e->getMessage()];
        }
    }

    /**
     * Clear application cache
     */
    public function clearCache()
    {
        try {
            \Artisan::call('cache:clear');
            return response()->json(['success' => true, 'message' => 'Application cache cleared successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Clear configuration cache
     */
    public function clearConfig()
    {
        try {
            \Artisan::call('config:clear');
            return response()->json(['success' => true, 'message' => 'Configuration cache cleared successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Clear route cache
     */
    public function clearRoutes()
    {
        try {
            \Artisan::call('route:clear');
            return response()->json(['success' => true, 'message' => 'Route cache cleared successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Clear view cache
     */
    public function clearViews()
    {
        try {
            \Artisan::call('view:clear');
            return response()->json(['success' => true, 'message' => 'View cache cleared successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
