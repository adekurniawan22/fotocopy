<!DOCTYPE html>
<html lang="id">

<head>
    <title>Login</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />
    <link href="assets/plugins/global/plugins.bundle.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/style.bundle.css" rel="stylesheet" type="text/css" />

    <style>
        body {
            background-color: #f5f8fa;
        }
    </style>
</head>

<body id="kt_body" class="auth-bg">
    <script>
        var defaultThemeMode = "light";
        var themeMode;
        if (document.documentElement) {
            if (document.documentElement.hasAttribute("data-bs-theme-mode")) {
                themeMode = document.documentElement.getAttribute("data-bs-theme-mode");
            } else {
                if (localStorage.getItem("data-bs-theme") !== null) {
                    themeMode = localStorage.getItem("data-bs-theme");
                } else {
                    themeMode = defaultThemeMode;
                }
            }
            if (themeMode === "system") {
                themeMode = window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light";
            }
            document.documentElement.setAttribute("data-bs-theme", themeMode);
        }
    </script>

    <div class="d-flex flex-column flex-root">
        <div class="d-flex flex-column flex-column-fluid flex-center p-10">

            <a href="{{ route('home') }}" class="mb-12">
                <img alt="Logo" src="{{ asset('assets/media/logos/logo-simpati-pdkb.png') }}" class="h-60px" />
            </a>

            <div class="bg-body rounded-3 shadow-sm p-10 p-lg-15 mx-auto w-100 mw-450px">

                <form class="form w-100" novalidate="novalidate" id="kt_sign_in_form" method="POST"
                    action="{{ route('login') }}">
                    @csrf

                    <div class="text-center mb-10">
                        <h1 class="text-dark mb-3">Login</h1>
                        <div class="text-gray-400 fw-bold fs-4" id="dynamic-greeting">
                            Silakan masuk ke akun Anda
                        </div>
                    </div>

                    <div class="fv-row mb-10">
                        <label class="form-label fs-6 fw-bolder text-dark">Username</label>
                        <input
                            class="form-control form-control-lg form-control-solid @error('user_name') is-invalid @enderror"
                            type="text" name="user_name" placeholder="Masukkan Username" autocomplete="off"
                            value="{{ old('user_name') }}" required />
                    </div>

                    <div class="fv-row mb-10">
                        <div class="d-flex flex-stack mb-2">
                            <label class="form-label fw-bolder text-dark fs-6 mb-0">Password</label>
                        </div>
                        <input
                            class="form-control form-control-lg form-control-solid @error('password') is-invalid @enderror"
                            type="password" name="password" placeholder="Masukkan Password" autocomplete="off"
                            required />
                    </div>

                    <div class="text-center">
                        <button type="submit" id="kt_sign_in_submit" class="btn btn-lg btn-primary w-100 mb-5">
                            <span class="indicator-label">Masuk</span>
                            <span class="indicator-progress">Please wait...
                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                        </button>

                        <div class="text-center text-muted text-uppercase fw-bolder mb-5">atau</div>

                        <a href="{{ route('home') }}" class="btn btn-flex flex-center btn-light btn-lg w-100">
                            <i class="fas fa-arrow-left me-2"></i> Kembali ke Katalog
                        </a>
                    </div>
                </form>

                @if ($errors->any())
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            var errorMessage = @json($errors->all())[0];

                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    text: errorMessage,
                                    icon: "error",
                                    buttonsStyling: !1,
                                    confirmButtonText: "Ok, Coba Lagi!",
                                    customClass: {
                                        confirmButton: "btn btn-primary",
                                    },
                                });
                            } else {
                                alert(errorMessage);
                            }
                        });
                    </script>
                @endif
            </div>
        </div>
    </div>

    <script>
        var hostUrl = "assets/";
    </script>
    <script src="assets/plugins/global/plugins.bundle.js"></script>
    <script src="assets/js/scripts.bundle.js"></script>
    <script src="assets/js/custom/authentication/sign-in/general.js"></script>
</body>

</html>
