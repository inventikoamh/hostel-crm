@extends('layouts.app')

@section('title', 'Enquiry #' . $enquiry->id)

@php
    $title = 'Enquiry #' . $enquiry->id;
    $subtitle = 'View and manage enquiry details';
    $showBackButton = true;
    $backUrl = route('enquiries.index');
    $users = \App\Models\User::all();
@endphp

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Enquiry Details -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" style="background-color: var(--card-bg); border-color: var(--border-color);">
                <div class="flex items-start justify-between mb-4">
                    <h3 class="text-lg font-semibold" style="color: var(--text-primary);">Enquiry Details</h3>
                    <div class="flex items-center gap-2">
                        <x-status-badge :status="$enquiry->status" />
                        <x-priority-badge :priority="$enquiry->priority" />
                        @if($enquiry->is_overdue)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                Overdue
                            </span>
                        @endif
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="text-sm font-medium text-gray-600" style="color: var(--text-secondary);">Name</label>
                        <p class="mt-1 text-sm font-medium" style="color: var(--text-primary);">{{ $enquiry->name }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600" style="color: var(--text-secondary);">Email</label>
                        <p class="mt-1 text-sm" style="color: var(--text-primary);">
                            <a href="mailto:{{ $enquiry->email }}" class="text-blue-600 hover:text-blue-800">{{ $enquiry->email }}</a>
                        </p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600" style="color: var(--text-secondary);">Phone</label>
                        <p class="mt-1 text-sm" style="color: var(--text-primary);">
                            <a href="tel:{{ $enquiry->phone }}" class="text-blue-600 hover:text-blue-800">{{ $enquiry->phone }}</a>
                        </p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600" style="color: var(--text-secondary);">Enquiry Type</label>
                        <p class="mt-1 text-sm" style="color: var(--text-primary);">{{ $enquiry->enquiry_type_display }}</p>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="text-sm font-medium text-gray-600" style="color: var(--text-secondary);">Subject</label>
                    <p class="mt-1 text-sm font-medium" style="color: var(--text-primary);">{{ $enquiry->subject }}</p>
                </div>

                <div class="mb-4">
                    <label class="text-sm font-medium text-gray-600" style="color: var(--text-secondary);">Message</label>
                    <div class="mt-1 p-3 bg-gray-50 rounded-lg" style="background-color: var(--bg-secondary);">
                        <p class="text-sm whitespace-pre-wrap" style="color: var(--text-primary);">{{ $enquiry->message }}</p>
                    </div>
                </div>

                <!-- Additional Information -->
                @if($enquiry->metadata && count($enquiry->metadata) > 0)
                    <div>
                        <label class="text-sm font-medium text-gray-600" style="color: var(--text-secondary);">Additional Information</label>
                        <div class="mt-1 grid grid-cols-1 md:grid-cols-3 gap-3">
                            @if(isset($enquiry->metadata['preferred_checkin']))
                                <div class="p-3 bg-blue-50 rounded-lg">
                                    <p class="text-xs font-medium text-blue-800">Preferred Check-in</p>
                                    <p class="text-sm text-blue-900">{{ \Carbon\Carbon::parse($enquiry->metadata['preferred_checkin'])->format('M j, Y') }}</p>
                                </div>
                            @endif
                            @if(isset($enquiry->metadata['duration']))
                                <div class="p-3 bg-green-50 rounded-lg">
                                    <p class="text-xs font-medium text-green-800">Duration</p>
                                    <p class="text-sm text-green-900">{{ $enquiry->metadata['duration'] }}</p>
                                </div>
                            @endif
                            @if(isset($enquiry->metadata['budget_range']))
                                <div class="p-3 bg-purple-50 rounded-lg">
                                    <p class="text-xs font-medium text-purple-800">Budget Range</p>
                                    <p class="text-sm text-purple-900">{{ $enquiry->metadata['budget_range'] }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            <!-- Management Form -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" style="background-color: var(--card-bg); border-color: var(--border-color);">
                <h3 class="text-lg font-semibold mb-4" style="color: var(--text-primary);">Manage Enquiry</h3>

                <form action="{{ route('enquiries.update', $enquiry->id) }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="status" class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Status</label>
                            <select id="status" name="status" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);">
                                <option value="new" {{ old('status', $enquiry->status) == 'new' ? 'selected' : '' }}>New</option>
                                <option value="in_progress" {{ old('status', $enquiry->status) == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="resolved" {{ old('status', $enquiry->status) == 'resolved' ? 'selected' : '' }}>Resolved</option>
                                <option value="closed" {{ old('status', $enquiry->status) == 'closed' ? 'selected' : '' }}>Closed</option>
                            </select>
                            @error('status')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="priority" class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Priority</label>
                            <select id="priority" name="priority" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);">
                                <option value="low" {{ old('priority', $enquiry->priority) == 'low' ? 'selected' : '' }}>Low</option>
                                <option value="medium" {{ old('priority', $enquiry->priority) == 'medium' ? 'selected' : '' }}>Medium</option>
                                <option value="high" {{ old('priority', $enquiry->priority) == 'high' ? 'selected' : '' }}>High</option>
                                <option value="urgent" {{ old('priority', $enquiry->priority) == 'urgent' ? 'selected' : '' }}>Urgent</option>
                            </select>
                            @error('priority')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="assigned_to" class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Assign To</label>
                            <select id="assigned_to" name="assigned_to"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);">
                                <option value="">Unassigned</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('assigned_to', $enquiry->assigned_to) == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('assigned_to')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="admin_notes" class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Admin Notes</label>
                        <textarea id="admin_notes" name="admin_notes" rows="4"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                  style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);"
                                  placeholder="Add internal notes about this enquiry, actions taken, or follow-up required...">{{ old('admin_notes', $enquiry->admin_notes) }}</textarea>
                        @error('admin_notes')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-end">
                        <button type="submit"
                                class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors duration-200 flex items-center gap-2">
                            <i class="fas fa-save"></i>
                            Update Enquiry
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Quick Actions -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" style="background-color: var(--card-bg); border-color: var(--border-color);">
                <h3 class="text-lg font-semibold mb-4" style="color: var(--text-primary);">Quick Actions</h3>
                <div class="space-y-3">
                    <a href="mailto:{{ $enquiry->email }}?subject=Re: {{ $enquiry->subject }}"
                       class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center justify-center gap-2">
                        <i class="fas fa-reply"></i>
                        Reply via Email
                    </a>
                    <a href="tel:{{ $enquiry->phone }}"
                       class="w-full bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center justify-center gap-2">
                        <i class="fas fa-phone"></i>
                        Call Customer
                    </a>
                    <button onclick="deleteEnquiry('{{ route('enquiries.destroy', $enquiry->id) }}')"
                            class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center justify-center gap-2">
                        <i class="fas fa-trash"></i>
                        Delete Enquiry
                    </button>
                </div>
            </div>

            <!-- Enquiry Information -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" style="background-color: var(--card-bg); border-color: var(--border-color);">
                <h3 class="text-lg font-semibold mb-4" style="color: var(--text-primary);">Enquiry Information</h3>
                <div class="space-y-3">
                    <div>
                        <label class="text-sm font-medium text-gray-600" style="color: var(--text-secondary);">Source</label>
                        <p class="text-sm capitalize" style="color: var(--text-primary);">{{ $enquiry->source }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600" style="color: var(--text-secondary);">Assigned To</label>
                        <p class="text-sm" style="color: var(--text-primary);">{{ $enquiry->assignedUser ? $enquiry->assignedUser->name : 'Unassigned' }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600" style="color: var(--text-secondary);">Received</label>
                        <p class="text-sm" style="color: var(--text-primary);">{{ $enquiry->created_at->format('M j, Y g:i A') }}</p>
                    </div>
                    @if($enquiry->responded_at)
                        <div>
                            <label class="text-sm font-medium text-gray-600" style="color: var(--text-secondary);">Responded</label>
                            <p class="text-sm" style="color: var(--text-primary);">{{ $enquiry->responded_at->format('M j, Y g:i A') }}</p>
                        </div>
                    @endif
                    @if($enquiry->updated_at && $enquiry->updated_at != $enquiry->created_at)
                        <div>
                            <label class="text-sm font-medium text-gray-600" style="color: var(--text-secondary);">Last Updated</label>
                            <p class="text-sm" style="color: var(--text-primary);">{{ $enquiry->updated_at->format('M j, Y g:i A') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        function deleteEnquiry(url) {
            if (confirm('Are you sure you want to delete this enquiry? This action cannot be undone.')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = url;

                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                form.appendChild(csrfToken);

                const methodField = document.createElement('input');
                methodField.type = 'hidden';
                methodField.name = '_method';
                methodField.value = 'DELETE';
                form.appendChild(methodField);

                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
@endsection
