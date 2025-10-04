<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thank You - Hostel CRM</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --bg-primary: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            --glass-bg: rgba(255, 255, 255, 0.8);
            --glass-border: rgba(255, 255, 255, 0.3);
        }

        body {
            background: var(--bg-primary);
            min-height: 100vh;
        }

        .glass-card {
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            border: 1px solid var(--glass-border);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }

        .success-animation {
            animation: successPulse 2s ease-in-out infinite;
        }

        @keyframes successPulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.05);
            }
        }
    </style>
</head>
<body>
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="w-full max-w-md">
            <div class="glass-card rounded-2xl p-8 text-center">
                <!-- Success Icon -->
                <div class="success-animation inline-flex items-center justify-center w-20 h-20 bg-green-100 rounded-full mb-6">
                    <i class="fas fa-check text-3xl text-green-600"></i>
                </div>

                <!-- Success Message -->
                <h1 class="text-2xl font-bold text-gray-900 mb-4">Thank You!</h1>
                <p class="text-gray-600 mb-6">
                    Your enquiry has been successfully submitted. We have received your message and will get back to you within 24 hours.
                </p>

                <!-- Reference Info -->
                <div class="bg-blue-50 rounded-lg p-4 mb-6">
                    <p class="text-sm text-blue-800">
                        <i class="fas fa-info-circle mr-2"></i>
                        We'll send a confirmation email to the address you provided.
                    </p>
                </div>

                <!-- Action Buttons -->
                <div class="space-y-3">
                    <a href="{{ route('enquiry.form') }}"
                       class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium px-6 py-3 rounded-lg transition-colors duration-200 inline-flex items-center justify-center gap-2">
                        <i class="fas fa-plus"></i>
                        Submit Another Enquiry
                    </a>
                    <a href="/"
                       class="w-full bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium px-6 py-3 rounded-lg transition-colors duration-200 inline-flex items-center justify-center gap-2">
                        <i class="fas fa-home"></i>
                        Back to Homepage
                    </a>
                </div>

                <!-- Contact Info -->
                <div class="mt-8 pt-6 border-t border-gray-200">
                    <p class="text-sm text-gray-500 mb-3">Need immediate assistance?</p>
                    <div class="flex justify-center gap-4 text-sm">
                        <div class="flex items-center gap-1">
                            <i class="fas fa-phone text-blue-600"></i>
                            <span>+91 12345 67890</span>
                        </div>
                        <div class="flex items-center gap-1">
                            <i class="fas fa-envelope text-blue-600"></i>
                            <span>info@hostel.com</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
