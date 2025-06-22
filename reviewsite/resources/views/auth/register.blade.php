<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - ReviewSite</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Share+Tech+Mono:wght@400;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #0F0F0F;
            color: #FFFFFF;
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .register-container {
            background: #1A1A1A;
            border: 2px solid #E53E3E;
            border-radius: 8px;
            padding: 3rem;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 0 20px rgba(229, 62, 62, 0.3);
        }

        .register-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .register-header h1 {
            font-family: 'Share Tech Mono', monospace;
            color: #E53E3E;
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .register-header p {
            color: #A0AEC0;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #FFFFFF;
        }

        .form-input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #333;
            border-radius: 4px;
            background: #2A2A2A;
            color: #FFFFFF;
            font-family: 'Inter', sans-serif;
        }

        .form-input:focus {
            outline: none;
            border-color: #E53E3E;
        }

        .btn {
            width: 100%;
            padding: 0.75rem;
            border: none;
            border-radius: 4px;
            font-family: 'Share Tech Mono', monospace;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: #E53E3E;
            color: #FFFFFF;
        }

        .btn-primary:hover {
            background: #C53030;
        }

        .error {
            color: #E53E3E;
            font-size: 0.875rem;
            margin-top: 0.5rem;
        }

        .back-link {
            text-align: center;
            margin-top: 1.5rem;
        }

        .back-link a {
            color: #A0AEC0;
            text-decoration: none;
        }

        .back-link a:hover {
            color: #E53E3E;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-header">
            <h1>REGISTER</h1>
            <p>Create your ReviewSite account</p>
        </div>

        @if($errors->any())
            <div class="error">
                @foreach($errors->all() as $error)
                    {{ $error }}<br>
                @endforeach
            </div>
        @endif

        <form action="{{ route('register.post') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="name" class="form-label">Name</label>
                <input type="text" id="name" name="name" class="form-input" value="{{ old('name') }}" required>
            </div>

            <div class="form-group">
                <label for="email" class="form-label">Email</label>
                <input type="email" id="email" name="email" class="form-input" value="{{ old('email') }}" required>
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Password</label>
                <input type="password" id="password" name="password" class="form-input" required>
            </div>

            <div class="form-group">
                <label for="password_confirmation" class="form-label">Confirm Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" class="form-input" required>
            </div>

            <button type="submit" class="btn btn-primary">Register</button>
        </form>

        <div class="back-link">
            <a href="{{ route('login') }}">Already have an account? Login</a>
        </div>
        
        <div class="back-link">
            <a href="{{ route('home') }}">← Back to Home</a>
        </div>
    </div>
</body>
</html> 