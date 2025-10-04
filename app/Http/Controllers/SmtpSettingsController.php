<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;

class SmtpSettingsController extends Controller
{
    /**
     * Display SMTP settings form
     */
    public function index()
    {
        $settings = [
            'mail_mailer' => env('MAIL_MAILER', 'smtp'),
            'mail_host' => env('MAIL_HOST', ''),
            'mail_port' => env('MAIL_PORT', '587'),
            'mail_username' => env('MAIL_USERNAME', ''),
            'mail_password' => env('MAIL_PASSWORD', ''),
            'mail_encryption' => env('MAIL_ENCRYPTION', 'tls'),
            'mail_from_address' => env('MAIL_FROM_ADDRESS', ''),
            'mail_from_name' => env('MAIL_FROM_NAME', config('app.name')),
        ];

        return view('config.smtp-settings', compact('settings'));
    }

    /**
     * Update SMTP settings
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mail_mailer' => 'required|in:smtp,sendmail,mailgun,ses,postmark,log,array',
            'mail_host' => 'required_if:mail_mailer,smtp|string|max:255',
            'mail_port' => 'required_if:mail_mailer,smtp|integer|min:1|max:65535',
            'mail_username' => 'nullable|string|max:255',
            'mail_password' => 'nullable|string|max:255',
            'mail_encryption' => 'nullable|in:tls,ssl',
            'mail_from_address' => 'required|email|max:255',
            'mail_from_name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                           ->withErrors($validator)
                           ->withInput();
        }

        try {
            // Update .env file
            $this->updateEnvFile([
                'MAIL_MAILER' => $request->mail_mailer,
                'MAIL_HOST' => $request->mail_host,
                'MAIL_PORT' => $request->mail_port,
                'MAIL_USERNAME' => $request->mail_username,
                'MAIL_PASSWORD' => $request->mail_password,
                'MAIL_ENCRYPTION' => $request->mail_encryption,
                'MAIL_FROM_ADDRESS' => $request->mail_from_address,
                'MAIL_FROM_NAME' => '"' . $request->mail_from_name . '"',
            ]);

            // Clear config cache to reload new settings
            Artisan::call('config:clear');

            return redirect()->back()
                           ->with('success', 'SMTP settings updated successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Failed to update SMTP settings: ' . $e->getMessage())
                           ->withInput();
        }
    }

    /**
     * Test SMTP connection
     */
    public function test(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'test_email' => 'required|email|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                           ->withErrors($validator)
                           ->withInput();
        }

        try {
            // Send test email
            Mail::raw('This is a test email to verify SMTP configuration.', function ($message) use ($request) {
                $message->to($request->test_email)
                        ->subject('SMTP Test Email - ' . config('app.name'));
            });

            return redirect()->back()
                           ->with('success', 'Test email sent successfully to ' . $request->test_email);

        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Failed to send test email: ' . $e->getMessage())
                           ->withInput();
        }
    }

    /**
     * Update .env file with new values
     */
    private function updateEnvFile(array $data)
    {
        $envFile = base_path('.env');
        $envContent = file_get_contents($envFile);

        foreach ($data as $key => $value) {
            // Escape special characters in value
            $escapedValue = $this->escapeEnvValue($value);

            // Check if key exists in .env file
            if (preg_match("/^{$key}=.*$/m", $envContent)) {
                // Update existing key
                $envContent = preg_replace("/^{$key}=.*$/m", "{$key}={$escapedValue}", $envContent);
            } else {
                // Add new key at the end
                $envContent .= "\n{$key}={$escapedValue}";
            }
        }

        file_put_contents($envFile, $envContent);
    }

    /**
     * Escape special characters in .env values
     */
    private function escapeEnvValue($value)
    {
        if (empty($value)) {
            return '""';
        }

        // If value contains spaces or special characters, wrap in quotes
        if (preg_match('/[\s#"\'\\\\]/', $value)) {
            return '"' . str_replace('"', '\\"', $value) . '"';
        }

        return $value;
    }
}
