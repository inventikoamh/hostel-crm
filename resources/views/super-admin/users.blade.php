@extends('layouts.app')

@section('title', 'User Management')

@php
    $title = 'User Management';
    $subtitle = 'Manage users and super admin privileges';
@endphp

@section('content')
<div class="container mx-auto p-4">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold" style="color: var(--text-primary);">{{ $title }}</h1>
                <p class="text-sm mt-1" style="color: var(--text-secondary);">{{ $subtitle }}</p>
            </div>
            <button onclick="openCreateSuperAdminModal()" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors duration-200">
                <i class="fas fa-plus mr-2"></i>
                Create Super Admin
            </button>
        </div>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100" style="background-color: var(--card-bg); border-color: var(--border-color);">
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b" style="border-color: var(--border-color);">
                            <th class="text-left py-3 text-sm font-medium" style="color: var(--text-secondary);">User</th>
                            <th class="text-left py-3 text-sm font-medium" style="color: var(--text-secondary);">Email</th>
                            <th class="text-left py-3 text-sm font-medium" style="color: var(--text-secondary);">Type</th>
                            <th class="text-left py-3 text-sm font-medium" style="color: var(--text-secondary);">Created</th>
                            <th class="text-left py-3 text-sm font-medium" style="color: var(--text-secondary);">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                        <tr class="border-b" style="border-color: var(--border-color);">
                            <td class="py-4">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center mr-3">
                                        <i class="fas fa-user text-white text-sm"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium" style="color: var(--text-primary);">{{ $user->name }}</p>
                                        @if($user->is_super_admin)
                                            <i class="fas fa-crown text-yellow-500 text-xs" title="Super Admin"></i>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 text-sm" style="color: var(--text-secondary);">{{ $user->email }}</td>
                            <td class="py-4">
                                @if($user->is_super_admin)
                                    <span class="px-2 py-1 bg-purple-100 text-purple-800 text-xs font-medium rounded-full">Super Admin</span>
                                @elseif($user->is_tenant)
                                    <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-medium rounded-full">Tenant</span>
                                @else
                                    <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded-full">Admin</span>
                                @endif
                            </td>
                            <td class="py-4 text-sm" style="color: var(--text-secondary);">{{ $user->created_at->format('M j, Y') }}</td>
                            <td class="py-4">
                                <div class="flex items-center space-x-2">
                                    @if($user->is_super_admin)
                                        <form method="POST" action="{{ route('super-admin.users.demote', $user) }}" class="inline">
                                            @csrf
                                            <button type="submit" 
                                                    onclick="return confirm('Are you sure you want to demote this super admin?')"
                                                    class="px-3 py-1 bg-yellow-100 text-yellow-800 text-xs font-medium rounded hover:bg-yellow-200 transition-colors duration-200">
                                                <i class="fas fa-arrow-down mr-1"></i>
                                                Demote
                                            </button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('super-admin.users.promote', $user) }}" class="inline">
                                            @csrf
                                            <button type="submit" 
                                                    onclick="return confirm('Are you sure you want to promote this user to super admin?')"
                                                    class="px-3 py-1 bg-purple-100 text-purple-800 text-xs font-medium rounded hover:bg-purple-200 transition-colors duration-200">
                                                <i class="fas fa-arrow-up mr-1"></i>
                                                Promote
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-sm" style="color: var(--text-secondary);">No users found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($users->hasPages())
            <div class="mt-6">
                {{ $users->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Create Super Admin Modal -->
<div id="createSuperAdminModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white" style="background-color: var(--card-bg);">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium" style="color: var(--text-primary);">Create Super Admin</h3>
                <button onclick="closeCreateSuperAdminModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form method="POST" action="{{ route('super-admin.users.create') }}">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label for="name" class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Full Name *</label>
                        <input type="text" id="name" name="name" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);">
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Email Address *</label>
                        <input type="email" id="email" name="email" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);">
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Password *</label>
                        <input type="password" id="password" name="password" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);">
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Confirm Password *</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);">
                    </div>
                </div>

                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" onclick="closeCreateSuperAdminModal()" 
                            class="px-4 py-2 rounded-lg text-gray-600 hover:bg-gray-200 transition-colors duration-200" 
                            style="background-color: var(--bg-secondary); color: var(--text-primary);">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 rounded-lg bg-purple-600 text-white hover:bg-purple-700 transition-colors duration-200">
                        Create Super Admin
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function openCreateSuperAdminModal() {
        document.getElementById('createSuperAdminModal').classList.remove('hidden');
    }

    function closeCreateSuperAdminModal() {
        document.getElementById('createSuperAdminModal').classList.add('hidden');
    }

    // Close modal when clicking outside
    document.getElementById('createSuperAdminModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeCreateSuperAdminModal();
        }
    });
</script>
@endpush
