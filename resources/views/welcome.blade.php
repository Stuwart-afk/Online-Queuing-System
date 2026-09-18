<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome - Fill Up Form</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f6f9;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }

        .form-card {
            background: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
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

        .alert-success {
            background-color: #d1fae5;
            color: #065f46;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            text-align: center;
        }
    </style>
</head>

<body>

    <div class="form-card">
        <h2>Queue Registration Form</h2>

        @if(session('tracking_number'))
        <div style="background-color: #d1fae5; color: #065f46; padding: 20px; border-radius: 8px; margin-bottom: 20px; text-align: center; border: 2px solid #34d399;">
            <h3 style="margin: 0 0 10px 0; font-size: 18px;">Registration Successful!</h3>
            <p style="margin: 0; font-size: 14px;">Your tracking number is:</p>
            <div style="font-size: 36px; font-weight: 900; letter-spacing: 2px; margin-top: 5px;">{{ session('tracking_number') }}</div>
            <p style="margin: 10px 0 0 0; font-size: 12px; color: #047857;">Please wait for your number to be called.</p>
        </div>
        @elseif(session('success'))
        <div class="alert-success">
            {{ session('success') }}
        </div>
        @endif

        @if($errors->any())
        <div style="background-color: #fee2e2; color: #b91c1c; padding: 10px; border-radius: 5px; margin-bottom: 15px;">
            <ul style="margin: 0; padding-left: 20px; font-size: 14px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route('submit.form') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" placeholder="Enter your full name" required>
            </div>

            <div class="form-group" id="mobileGroup" style="display: none;">
                <label for="mobile_number">Mobile Number</label>
                <input
                    type="text"
                    id="mobile_number"
                    name="mobile_number"
                    placeholder="Enter your mobile number">
            </div>

            <input type="hidden" name="device_id" id="device_id">
            <input type="hidden" name="platform" id="platform">
            <button type="submit" class="submit-btn">Get Tracking Number</button>


        </form>
    </div>
    <script>
        function generateUUID() {
            if (typeof crypto !== 'undefined' && crypto.randomUUID) {
                return crypto.randomUUID();
            }
            return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
                var r = Math.random() * 16 | 0, v = c == 'x' ? r : (r & 0x3 | 0x8);
                return v.toString(16);
            });
        }

        let deviceId = localStorage.getItem('device_id');

        if (!deviceId) {
            deviceId = generateUUID();
            localStorage.setItem('device_id', deviceId);
        }

        let platform;

        if (/Android/i.test(navigator.userAgent)) {
            platform = 'android';
        } else if (/iPhone|iPad|ipod/i.test(navigator.userAgent)) {
            platform = 'ios';
        } else if (/Windows|Macintosh|Linux/i.test(navigator.userAgent)) {
            platform = 'desktop';
        } else {
            platform = 'other';
        }

        if (platform === 'ios') {
            document.getElementById('mobileGroup').style.display = 'block';
        }

        document.getElementById('device_id').value = deviceId;
        document.getElementById('platform').value = platform;

        console.log('Device ID:', deviceId);
        console.log('Platform:', platform);
    </script>
</body>

</html>