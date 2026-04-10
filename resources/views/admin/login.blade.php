<!DOCTYPE html>
<html>

<head>
    <title>Login Admin Puskesmas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        body {
            background-color: #f4f6f9;
            display: flex;
            align-items: center;
            height: 100vh;
            justify-content: center;
        }

        .login-card {
            width: 400px;
            box-shadow: 0 4px 0px rgba(0, 0, 0, 0.1);
        }

        /* Style tambahan agar cursor pointer saat hover di icon mata */
        #togglePassword {
            cursor: pointer;
        }
    </style>
</head>

<body>
    <div class="card login-card">
        <div class="card-header text-center bg-primary text-white py-3">
            <h4>Login Admin</h4>
        </div>
        <div class="card-body p-4">
            <form action="{{ url('/login') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                        value="{{ old('email') }}" placeholder="Masukkan Email">

                    @error('email')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <div class="input-group">
                        <input type="password" name="password"
                            class="form-control @error('password') is-invalid @enderror" id="passwordInput" placeholder="Masukkan Password">

                        <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                            <i class="bi bi-eye-slash" id="toggleIcon"></i>
                        </button>

                        @error('password')
                            <div class="invalid-feedback d-block">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100">LOGIN</button>
            </form>
        </div>
    </div>

    <script>
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#passwordInput');
        const icon = document.querySelector('#toggleIcon');

        togglePassword.addEventListener('click', function(e) {
            // Toggle tipe attribut antara password dan text
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);

            // Toggle icon mata (antara mata terbuka dan mata dicoret)
            if (type === 'text') {
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            } else {
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            }
        });
    </script>
</body>

</html>
