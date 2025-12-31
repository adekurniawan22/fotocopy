<!DOCTYPE html>
<html lang="en">

<head>
    <title>Sign In - SIMPATI PDKB</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta property="og:locale" content="en_US" />
    <meta property="og:type" content="article" />
    <meta property="og:title"
        content="Metronic - The World's #1 Selling Tailwind CSS & Bootstrap Admin Template by KeenThemes" />
    <meta property="og:url" content="https://keenthemes.com/metronic" />
    <meta property="og:site_name" content="Metronic by Keenthemes" />
    <link rel="canonical" href="http://preview.keenthemes.comauthentication/layouts/fancy/sign-in.html" />
    <link rel="shortcut icon" href="{{ asset('assets/media/logos/logo-simpati-pdkb.png') }}" type="image/png" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />
    <link href="assets/plugins/global/plugins.bundle.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/style.bundle.css" rel="stylesheet" type="text/css" />
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&display=swap" rel="stylesheet">
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
        <div class="d-flex flex-column flex-lg-row flex-column-fluid">
            <a href="#" class="d-block d-lg-none mx-auto py-20">
                <img alt="Logo" src="{{ asset('assets/media/logos/logo-simpati-pdkb.png') }}" class="h-80px" />
            </a>
            <div class="d-flex flex-column flex-column-fluid flex-center w-lg-50 p-10">
                <div class="d-flex justify-content-center flex-column-fluid flex-column w-100 mw-450px">
                    <div class="py-20">
                        <form class="form w-100" novalidate="novalidate" id="kt_sign_in_form" method="POST"
                            action="{{ route('login') }}">
                            @csrf

                            <div class="card-body">

                                <div class="text-start mb-5">
                                    <h2 class="text-dark fw-bolder fs-2" id="dynamic-greeting"></h2>

                                    <div class="text-gray-500 fw-semibold fs-6" data-kt-translate="sign-in-desc">
                                        Silakan masuk untuk melanjutkan ke SIMPATI PDKB.
                                    </div>
                                </div>
                                <div class="fv-row mb-5">
                                    <input type="text" placeholder="NIP / No. HP" name="login" autocomplete="off"
                                        class="form-control form-control-solid @error('login') is-invalid @enderror"
                                        value="{{ old('login') }}" required />
                                </div>

                                <div class="fv-row mb-5">
                                    <input type="password" placeholder="Password" name="password" autocomplete="off"
                                        class="form-control form-control-solid @error('password') is-invalid @enderror"
                                        required />
                                </div>

                                <div class="d-flex flex-stack flex-wrap gap-3 fs-base fw-semibold mb-10 d-none">
                                    <div></div>
                                    <a href="authentication/layouts/fancy/reset-password.html" class="link-primary"
                                        data-kt-translate="sign-in-forgot-password">Lupa Password ?</a>
                                </div>

                                <div class="d-flex flex-stack">
                                    <button id="kt_sign_in_submit" class="btn btn-primary me-2 flex-shrink-0">
                                        <span class="indicator-label" data-kt-translate="sign-in-submit">Sign In</span>
                                        <span class="indicator-progress">
                                            <span data-kt-translate="general-progress">Please wait...</span>
                                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                        </span>
                                    </button>
                                </div>
                            </div>
                        </form>

                        @if ($errors->any())
                            <script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    var errorMessage = @json($errors->all())[0];

                                    Swal.fire({
                                        text: errorMessage,
                                        icon: "error",
                                        buttonsStyling: !1,
                                        confirmButtonText: "Ok, saya mengerti!",
                                        customClass: {
                                            confirmButton: "btn btn-primary",
                                        },
                                    });
                                });
                            </script>
                        @endif
                    </div>
                </div>
            </div>

            <div class="d-none d-lg-flex flex-lg-row-fluid w-50 bgi-size-cover bgi-position-y-center bgi-position-x-start bgi-no-repeat justify-content-center align-items-center flex-column text-center"
                style="background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url(assets/media/auth/bg-pdkb.png);">

                <img src="https://simpati-pdkb.id/assets/img/logo-simpati-pdkb.png" width="150px"
                    style="filter: brightness(200%) drop-shadow(0px 0px 5px white);" class="mb-1">
                <h4 class="text-white"
                    style="font-family: 'Open Sans', sans-serif; font-weight: 600; line-height: 1.5;">
                    Sistem Manajemen dan Pemantauan Terintegrasi&nbsp;PDKB
                </h4>
            </div>
        </div>
    </div>
    <script>
        var hostUrl = "assets/";
    </script>
    <script src="assets/plugins/global/plugins.bundle.js"></script>
    <script src="assets/js/scripts.bundle.js"></script>
    <script src="assets/js/custom/authentication/sign-in/general.js"></script>
    <script src="assets/js/custom/authentication/sign-in/i18n.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const hour = new Date().getHours();
            let greeting = "";

            if (hour >= 5 && hour < 11) {
                greeting = "Selamat Pagi ☀️"; // 05.00 - 10.59
            } else if (hour >= 11 && hour < 15) {
                greeting = "Selamat Siang 🌤️"; // 11.00 - 14.59
            } else if (hour >= 15 && hour < 19) {
                greeting = "Selamat Sore 🌇"; // 15.00 - 18.59
            } else {
                greeting = "Selamat Malam 🌙"; // 19.00 - 04.59
            }

            const el = document.getElementById("dynamic-greeting");
            if (el) el.textContent = greeting;
        });
    </script>
</body>

</html>
