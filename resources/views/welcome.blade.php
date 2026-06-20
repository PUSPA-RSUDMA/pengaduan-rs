<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SIM Pengaduan RSUD</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f0f2f5;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-container {
            max-width: 900px;
            width: 100%;
            background: white;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .left-side {
            background: url('https://images.unsplash.com/photo-1586773860418-d37222d8fce3?ixlib=rb-1.2.1&auto=format&fit=crop&w=1000&q=80') center/cover;
            min-height: 500px;
            position: relative;
        }
        .left-overlay {
            position: absolute; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(13, 110, 253, 0.7); /* Overlay Biru */
            display: flex; flex-direction: column; justify-content: center; padding: 40px; color: white;
        }
        .right-side { padding: 50px; }
        .form-control { padding: 12px; border-radius: 8px; border: 1px solid #e1e1e1; background: #f8f9fa; }
        .form-control:focus { border-color: #0d6efd; background: white; box-shadow: none; }
        .btn-login { padding: 12px; border-radius: 8px; font-weight: 600; letter-spacing: 0.5px; }
    </style>
</head>
<body>

    <div class="container p-3">
        <div class="login-container mx-auto">
            <div class="row g-0">
                <div class="col-lg-6 d-none d-lg-block left-side">
                    <div class="left-overlay">
                        <h2 class="fw-bold mb-3">Sistem Instalasi Pengaduan</h2>
                        <p class="mb-4 opacity-75">RSUD dr. H. Moh. Anwar Sumenep</p>
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-white bg-opacity-25 p-3 rounded text-center">
                                <h4 class="fw-bold m-0">24</h4>
                                <small>Jam</small>
                            </div>
                            <div class="bg-white bg-opacity-25 p-3 rounded text-center">
                                <h4 class="fw-bold m-0">100%</h4>
                                <small>Respon</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 right-side">
                    <div class="text-center mb-4">
                        <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill fw-bold mb-3">
                            <i class="bi bi-shield-lock"></i> LOGIN PETUGAS
                        </span>
                        <h4 class="fw-bold">Selamat Datang!</h4>
                        <p class="text-muted small">Silakan masukkan akun Anda untuk melanjutkan.</p>
                    </div>

                    @if ($errors->any())
                        <div class="alert alert-danger small p-2 text-center border-0 bg-danger bg-opacity-10 text-danger mb-3">
                            Email atau Password salah. Coba lagi.
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">Email Petugas</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-envelope text-muted"></i></span>
                                <input type="email" name="email" class="form-control border-start-0 ps-0" placeholder="admin@cerdas.com" required autofocus>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label small fw-bold text-muted">Kata Sandi</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-key text-muted"></i></span>
                                <input type="password" name="password" class="form-control border-start-0 ps-0" placeholder="••••••••" required>
                            </div>
                        </div>

                        <div class="d-grid mb-3">
                            <button type="submit" class="btn btn-primary btn-login shadow-sm">
                                MASUK APLIKASI <i class="bi bi-arrow-right ms-2"></i>
                            </button>
                        </div>

                        <div class="text-center">
                            <small class="text-muted">Lupa password? Hubungi Tim IT RSUD.</small>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="text-center mt-4 text-muted small">
            &copy; {{ date('Y') }} IPP-RSUD• Built for RSUD dr. H. Moh. Anwar Sumenep
        </div>
    </div>

</body>
</html>