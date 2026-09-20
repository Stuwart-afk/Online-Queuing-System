<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome - Queue Registration</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>
        .form-card {
            background: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            margin: 40px auto;
        }

        .form-card h2 {
            margin-bottom: 20px;
            color: #333;
            text-align: center;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #666;
            font-size: 14px;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }

        .submit-btn {
            width: 100%;
            padding: 12px;
            background-color: #4f46e5;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }

        .submit-btn:hover {
            background-color: #4338ca;
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen font-sans antialiased text-gray-900" x-data="deviceManager()" x-init="initDevice()">

    <!-- Inject the Livewire Component -->
    <livewire:student-kiosk />

    @livewireScripts
    
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('deviceManager', () => ({
                initDevice() {
                    let deviceId = localStorage.getItem('device_id');

                    if (!deviceId) {
                        deviceId = this.generateUUID();
                        localStorage.setItem('device_id', deviceId);
                    }

                    let platform = 'other';
                    if (/Android/i.test(navigator.userAgent)) {
                        platform = 'android';
                    } else if (/iPhone|iPad|ipod/i.test(navigator.userAgent)) {
                        platform = 'ios';
                    } else if (/Windows|Macintosh|Linux/i.test(navigator.userAgent)) {
                        platform = 'desktop';
                    }

                    // Tell the Livewire component our device ID and platform
                    Livewire.dispatch('set-device', { id: deviceId, platform: platform });
                },
                
                generateUUID() {
                    if (typeof crypto !== 'undefined' && crypto.randomUUID) {
                        return crypto.randomUUID();
                    }
                    return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
                        var r = Math.random() * 16 | 0, v = c == 'x' ? r : (r & 0x3 | 0x8);
                        return v.toString(16);
                    });
                }
            }))
        })
    </script>
</body>
</html>