<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Hisab Kitab</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: system-ui, -apple-system, sans-serif;
            color: #f8fafc;
            padding: 1.5rem;
        }
        .login-card {
            background: rgba(30, 41, 59, 0.7);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 16px;
            padding: 2.5rem 2rem;
            width: 100%;
            max-width: 440px;
            box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.5);
        }
        .brand-logo {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #3b82f6;
            color: #ffffff;
            width: 48px;
            height: 48px;
            font-size: 1.5rem;
            font-weight: 700;
            border-radius: 12px;
            margin-bottom: 1.25rem;
            box-shadow: 0 8px 16px -4px rgba(59, 130, 246, 0.5);
        }
        .form-control {
            background-color: rgba(15, 23, 42, 0.6) !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
            color: #f8fafc !important;
            padding: 0.75rem 1rem;
            border-radius: 8px;
            transition: all 0.2s ease;
        }
        .form-control:focus {
            border-color: #3b82f6 !important;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.15) !important;
        }
        .btn-login {
            background-color: #3b82f6;
            border: none;
            color: #ffffff;
            padding: 0.75rem;
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.2s ease;
            box-shadow: 0 4px 12px -2px rgba(59, 130, 246, 0.3);
        }
        .btn-login:hover {
            background-color: #2563eb;
            transform: translateY(-1px);
            box-shadow: 0 6px 16px -2px rgba(59, 130, 246, 0.4);
        }
        .btn-login:active {
            transform: translateY(0);
        }
        .form-check-input {
            background-color: rgba(15, 23, 42, 0.6);
            border-color: rgba(255, 255, 255, 0.15);
        }
        .form-check-input:checked {
            background-color: #3b82f6;
            border-color: #3b82f6;
        }
        .alert-custom {
            background-color: rgba(239, 68, 68, 0.15);
            border: 1px solid rgba(239, 68, 68, 0.25);
            color: #fca5a5;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>

    <div class="login-card text-center">
        <div class="brand-logo">HK</div>
        <h3 class="fw-bold mb-1">Welcome Back</h3>
        <p class="text-secondary mb-4" style="color: #94a3b8 !important;">Sign in to access your Accounting System</p>


        <form action="{{ route('login.post') }}" method="POST" class="text-start">
            @csrf
            
            <div class="mb-3">
                <label for="email" class="form-label text-light small fw-semibold">Email Address</label>
                <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" placeholder="admin@example.com" required autofocus>
            </div>
            
            <div class="mb-4">
                <label for="password" class="form-label text-light small fw-semibold">Password</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="••••••••" required>
            </div>

            <div class="d-flex align-items-center justify-content-between mb-4">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember" value="1">
                    <label class="form-check-label text-secondary small" for="remember" style="color: #94a3b8 !important;">
                        Remember me
                    </label>
                </div>
            </div>

            <button type="submit" class="btn btn-login w-100 mb-2">Sign In</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 4000,
                timerProgressBar: true,
                background: '#1e293b',
                color: '#f8fafc',
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });

            @if (session('success'))
                Toast.fire({
                    icon: 'success',
                    title: {!! json_encode(session('success')) !!}
                });
            @endif

            @if (session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: {!! json_encode(session('error')) !!},
                    background: '#1e293b',
                    color: '#f8fafc',
                    confirmButtonColor: '#3b82f6',
                    confirmButtonText: 'OK'
                });
            @endif

            @if ($errors->any())
                @php
                    $errorHtml = '<ul class="text-start mb-0" style="list-style-type: disc; padding-left: 15px;">';
                    foreach ($errors->all() as $error) {
                        $errorHtml .= '<li>' . e($error) . '</li>';
                    }
                    $errorHtml .= '</ul>';
                @endphp
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Errors',
                    html: '{!! $errorHtml !!}',
                    background: '#1e293b',
                    color: '#f8fafc',
                    confirmButtonColor: '#3b82f6',
                    confirmButtonText: 'OK'
                });
            @endif
        });
    </script>
</body>
</html>
