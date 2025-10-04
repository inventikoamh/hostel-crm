<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Hostel CRM</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --bg-primary: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            --glass-bg: rgba(255, 255, 255, 0.8);
            --glass-border: rgba(255, 255, 255, 0.3);
            --input-bg: #ffffff;
            --text-primary: #1f2937;
            --text-secondary: #6b7280;
            --border-color: #e5e7eb;
        }

        body {
            background: var(--bg-primary);
            min-height: 100vh;
            overflow-y: auto;
        }

        .gradient-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: var(--bg-primary);
            z-index: -2;
        }

        .animated-shapes {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            overflow: hidden;
        }

        .shape-1, .shape-2, .shape-3, .shape-4, .shape-5, .shape-6, .shape-7, .shape-8 {
            position: absolute;
            border-radius: 50%;
            filter: blur(1px);
            animation: float 20s infinite linear;
        }

        .shape-1 {
            width: 80px;
            height: 80px;
            background: linear-gradient(45deg, rgba(0, 0, 0, 0.1), rgba(128, 128, 128, 0.1));
            top: 10%;
            left: 10%;
            animation-delay: 0s;
        }

        .shape-2 {
            width: 120px;
            height: 120px;
            background: linear-gradient(45deg, rgba(64, 64, 64, 0.08), rgba(255, 255, 255, 0.1));
            top: 20%;
            right: 15%;
            animation-delay: -5s;
        }

        .shape-3 {
            width: 60px;
            height: 60px;
            background: linear-gradient(45deg, rgba(32, 32, 32, 0.12), rgba(192, 192, 192, 0.08));
            bottom: 30%;
            left: 20%;
            animation-delay: -10s;
        }

        .shape-4 {
            width: 100px;
            height: 100px;
            background: linear-gradient(45deg, rgba(96, 96, 96, 0.09), rgba(224, 224, 224, 0.07));
            bottom: 20%;
            right: 25%;
            animation-delay: -15s;
        }

        .shape-5 {
            width: 140px;
            height: 140px;
            background: linear-gradient(45deg, rgba(16, 16, 16, 0.11), rgba(240, 240, 240, 0.09));
            top: 50%;
            left: 5%;
            animation-delay: -7s;
        }

        .shape-6 {
            width: 70px;
            height: 70px;
            background: linear-gradient(45deg, rgba(48, 48, 48, 0.1), rgba(208, 208, 208, 0.08));
            top: 70%;
            right: 10%;
            animation-delay: -12s;
        }

        .shape-7 {
            width: 90px;
            height: 90px;
            background: linear-gradient(45deg, rgba(80, 80, 80, 0.08), rgba(176, 176, 176, 0.1));
            top: 30%;
            left: 50%;
            animation-delay: -3s;
        }

        .shape-8 {
            width: 110px;
            height: 110px;
            background: linear-gradient(45deg, rgba(112, 112, 112, 0.07), rgba(160, 160, 160, 0.09));
            bottom: 10%;
            left: 40%;
            animation-delay: -18s;
        }

        @keyframes float {
            0%, 100% {
                transform: translateY(0px) rotate(0deg);
            }
            33% {
                transform: translateY(-30px) rotate(120deg);
            }
            66% {
                transform: translateY(20px) rotate(240deg);
            }
        }

        .glass-card {
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            border: 1px solid var(--glass-border);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }

        .form-input {
            background-color: var(--input-bg) !important;
            border-color: var(--border-color);
            color: var(--text-primary);
        }

        .form-input:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
    </style>
</head>
<body>
    <div class="gradient-bg"></div>
    <div class="animated-shapes">
        <div class="shape-1"></div>
        <div class="shape-2"></div>
        <div class="shape-3"></div>
        <div class="shape-4"></div>
        <div class="shape-5"></div>
        <div class="shape-6"></div>
        <div class="shape-7"></div>
        <div class="shape-8"></div>
    </div>

    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="w-full max-w-2xl">
            <!-- Header -->
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-800 rounded-2xl mb-4">
                    <i class="fas fa-building text-2xl text-white"></i>
                </div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Contact Our Hostel</h1>
                <p class="text-gray-600">We'd love to hear from you. Send us a message and we'll respond as soon as possible.</p>
            </div>

            <!-- Contact Form -->
            <div class="glass-card rounded-2xl p-8">
                <form action="{{ route('enquiry.store') }}" method="POST" class="space-y-6">
                    @csrf

                    <!-- Personal Information -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Full Name *</label>
                            <input type="text" id="name" name="name" value="{{ old('name') }}" required
                                   class="form-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            @error('name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address *</label>
                            <input type="email" id="email" name="email" value="{{ old('email') }}" required
                                   class="form-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            @error('email')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Phone Number *</label>
                            <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" required
                                   class="form-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            @error('phone')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="enquiry_type" class="block text-sm font-medium text-gray-700 mb-2">Enquiry Type *</label>
                            <select id="enquiry_type" name="enquiry_type" required
                                    class="form-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Select enquiry type</option>
                                <option value="room_booking" {{ old('enquiry_type') == 'room_booking' ? 'selected' : '' }}>Room Booking</option>
                                <option value="general_info" {{ old('enquiry_type') == 'general_info' ? 'selected' : '' }}>General Information</option>
                                <option value="pricing" {{ old('enquiry_type') == 'pricing' ? 'selected' : '' }}>Pricing Inquiry</option>
                                <option value="facilities" {{ old('enquiry_type') == 'facilities' ? 'selected' : '' }}>Facilities & Amenities</option>
                                <option value="other" {{ old('enquiry_type') == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('enquiry_type')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Additional Information (for room booking) -->
                    <div id="booking-details" class="grid grid-cols-1 md:grid-cols-3 gap-4 hidden">
                        <div>
                            <label for="preferred_checkin" class="block text-sm font-medium text-gray-700 mb-2">Preferred Check-in Date</label>
                            <input type="date" id="preferred_checkin" name="preferred_checkin" value="{{ old('preferred_checkin') }}"
                                   class="form-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        <div>
                            <label for="duration" class="block text-sm font-medium text-gray-700 mb-2">Duration of Stay</label>
                            <select id="duration" name="duration"
                                    class="form-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Select duration</option>
                                <option value="1-7 days" {{ old('duration') == '1-7 days' ? 'selected' : '' }}>1-7 days</option>
                                <option value="1-4 weeks" {{ old('duration') == '1-4 weeks' ? 'selected' : '' }}>1-4 weeks</option>
                                <option value="1-3 months" {{ old('duration') == '1-3 months' ? 'selected' : '' }}>1-3 months</option>
                                <option value="3-6 months" {{ old('duration') == '3-6 months' ? 'selected' : '' }}>3-6 months</option>
                                <option value="6+ months" {{ old('duration') == '6+ months' ? 'selected' : '' }}>6+ months</option>
                            </select>
                        </div>
                        <div>
                            <label for="budget_range" class="block text-sm font-medium text-gray-700 mb-2">Budget Range</label>
                            <select id="budget_range" name="budget_range"
                                    class="form-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Select budget</option>
                                <option value="Under ₹5,000" {{ old('budget_range') == 'Under ₹5,000' ? 'selected' : '' }}>Under ₹5,000</option>
                                <option value="₹5,000 - ₹10,000" {{ old('budget_range') == '₹5,000 - ₹10,000' ? 'selected' : '' }}>₹5,000 - ₹10,000</option>
                                <option value="₹10,000 - ₹20,000" {{ old('budget_range') == '₹10,000 - ₹20,000' ? 'selected' : '' }}>₹10,000 - ₹20,000</option>
                                <option value="₹20,000 - ₹30,000" {{ old('budget_range') == '₹20,000 - ₹30,000' ? 'selected' : '' }}>₹20,000 - ₹30,000</option>
                                <option value="Above ₹30,000" {{ old('budget_range') == 'Above ₹30,000' ? 'selected' : '' }}>Above ₹30,000</option>
                            </select>
                        </div>
                    </div>

                    <!-- Subject and Message -->
                    <div>
                        <label for="subject" class="block text-sm font-medium text-gray-700 mb-2">Subject *</label>
                        <input type="text" id="subject" name="subject" value="{{ old('subject') }}" required
                               class="form-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="Brief description of your enquiry">
                        @error('subject')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="message" class="block text-sm font-medium text-gray-700 mb-2">Message *</label>
                        <textarea id="message" name="message" rows="5" required
                                  class="form-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                  placeholder="Please provide details about your enquiry...">{{ old('message') }}</textarea>
                        @error('message')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <div class="flex items-center justify-between pt-4">
                        <p class="text-sm text-gray-600">
                            <i class="fas fa-info-circle mr-1"></i>
                            We'll respond within 24 hours
                        </p>
                        <button type="submit"
                                class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-8 py-3 rounded-lg transition-colors duration-200 flex items-center gap-2">
                            <i class="fas fa-paper-plane"></i>
                            Send Enquiry
                        </button>
                    </div>
                </form>
            </div>

            <!-- Contact Information -->
            <div class="mt-8 text-center">
                <p class="text-gray-600 mb-4">Or reach us directly:</p>
                <div class="flex flex-wrap justify-center gap-6 text-sm">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-phone text-blue-600"></i>
                        <span>+91 12345 67890</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fas fa-envelope text-blue-600"></i>
                        <span>info@hostel.com</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fas fa-map-marker-alt text-blue-600"></i>
                        <span>123 Hostel Street, City</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Show/hide booking details based on enquiry type
        document.getElementById('enquiry_type').addEventListener('change', function() {
            const bookingDetails = document.getElementById('booking-details');
            if (this.value === 'room_booking') {
                bookingDetails.classList.remove('hidden');
            } else {
                bookingDetails.classList.add('hidden');
            }
        });

        // Show booking details if room_booking is pre-selected (on validation error)
        if (document.getElementById('enquiry_type').value === 'room_booking') {
            document.getElementById('booking-details').classList.remove('hidden');
        }
    </script>
</body>
</html>
