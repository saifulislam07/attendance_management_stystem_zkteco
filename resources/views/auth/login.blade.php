<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login | {{ \App\Models\Setting::get('site_name', 'School Attendance Pro') }}</title>
  
  <!-- Favicon -->
  <link rel="icon" type="image/png" href="{{ \App\Models\Setting::get('site_favicon', asset('logo/favicon.png')) }}">

  <!-- Google Font: Inter -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <!-- Bootstrap 4 -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
  
  <style>
    body {
      font-family: 'Inter', sans-serif;
      background: url('{{ asset("logo/bg.png") }}') no-repeat center center fixed;
      background-size: cover;
      height: 100vh;
      margin: 0;
      display: flex;
      align-items: center;
      justify-content: center;
      overflow: hidden;
    }

    /* Glassmorphism Effect */
    .login-container {
      background: rgba(255, 255, 255, 0.15);
      backdrop-filter: blur(15px);
      -webkit-backdrop-filter: blur(15px);
      border: 1px solid rgba(255, 255, 255, 0.2);
      border-radius: 24px;
      padding: 40px;
      width: 100%;
      max-width: 420px;
      box-shadow: 0 25px 45px rgba(0, 0, 0, 0.2);
      text-align: center;
      animation: fadeIn 1s ease-out;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .brand-logo {
      width: 110px;
      height: 110px;
      background: white;
      padding: 8px;
      border-radius: 50%;
      margin: 0 auto 25px;
      box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .brand-logo img {
      width: 100%;
      height: 100%;
      object-fit: contain;
      border-radius: 50%;
    }

    .login-container h2 {
      color: #fff;
      font-weight: 700;
      margin-bottom: 8px;
      letter-spacing: -0.5px;
    }

    .login-container p {
      color: rgba(255, 255, 255, 0.8);
      font-size: 14px;
      margin-bottom: 30px;
    }

    .form-control {
      background: rgba(255, 255, 255, 0.2);
      border: 1px solid rgba(255, 255, 255, 0.3);
      border-radius: 12px;
      color: #fff !important;
      padding: 12px 20px;
      height: auto;
      transition: all 0.3s ease;
    }

    .form-control::placeholder {
      color: rgba(255, 255, 255, 0.6);
    }

    .form-control:focus {
      background: rgba(255, 255, 255, 0.3);
      border-color: #fff;
      box-shadow: none;
    }

    .input-group-text {
      background: transparent;
      border: none;
      color: rgba(255, 255, 255, 0.8);
    }

    .btn-login {
      background: #fff;
      color: #1a2a6c;
      border: none;
      border-radius: 12px;
      padding: 14px;
      font-weight: 700;
      width: 100%;
      margin-top: 20px;
      transition: all 0.3s;
      text-transform: uppercase;
      letter-spacing: 1px;
    }

    .btn-login:hover {
      background: #f0f0f0;
      transform: scale(1.02);
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
    }

    .footer-links {
      margin-top: 25px;
      color: rgba(255, 255, 255, 0.7);
      font-size: 13px;
    }

    .footer-links a {
      color: #fff;
      text-decoration: none;
      font-weight: 600;
    }

    .error.invalid-feedback {
      color: #ff4d4d !important;
      display: block;
      text-align: left;
      margin-left: 5px;
      font-weight: 600;
    }
  </style>
</head>
<body>

  <div class="login-container">
    <div class="brand-logo">
      @if(\App\Models\Setting::get('site_logo'))
        <img src="{{ \App\Models\Setting::get('site_logo') }}" alt="Logo">
      @else
        <i class="fas fa-school fa-3x text-primary"></i>
      @endif
    </div>

    @if(!\App\Models\Setting::get('site_logo'))
      <h2>{{ \App\Models\Setting::get('site_name', 'School Attendance Pro') }}</h2>
    @endif

    <p>Welcome back! Please login to your account.</p>

    <form action="{{ route('authenticate') }}" method="post">
      @csrf
      <div class="input-group mb-3">
        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="Email Address" value="{{ old('email') }}" required autofocus>
      </div>
      @error('email')
        <span class="error invalid-feedback mb-2">{{ $message }}</span>
      @enderror

      <div class="input-group mb-4">
        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Password" required>
      </div>
      @error('password')
        <span class="error invalid-feedback mb-2">{{ $message }}</span>
      @enderror

      <button type="submit" class="btn-login shadow">
        Sign In <i class="fas fa-arrow-right ml-2"></i>
      </button>

      <div class="footer-links">
        &copy; {{ date('Y') }} {{ \App\Models\Setting::get('site_name', 'School Attendance Pro') }} <br>
        Crafted with <i class="fas fa-heart text-danger"></i> by Saiful Islam
      </div>
    </form>
  </div>

  <!-- jQuery -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <!-- Bootstrap 4 -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
