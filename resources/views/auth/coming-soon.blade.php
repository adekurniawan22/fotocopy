<!DOCTYPE html>
<html lang="en">

<head>
    <base href="../../" />
    <title>Coming Soon</title>
    <meta charset="utf-8" />
    <meta name="description"
        content="The most advanced Tailwind CSS & Bootstrap 5 Admin Theme with 40 unique prebuilt layouts on Themeforest trusted by 100,000 beginners and professionals. Multi-demo, Dark Mode, RTL support and complete React, Angular, Vue, Asp.Net Core, Rails, Spring, Blazor, Django, Express.js, Node.js, Flask, Symfony & Laravel versions. Grab your copy now and get life-time updates for free." />
    <meta name="keywords"
        content="tailwind, tailwindcss, metronic, bootstrap, bootstrap 5, angular, VueJs, React, Asp.Net Core, Rails, Spring, Blazor, Django, Express.js, Node.js, Flask, Symfony & Laravel starter kits, admin themes, web design, figma, web development, free templates, free admin themes, bootstrap theme, bootstrap template, bootstrap dashboard, bootstrap dak mode, bootstrap button, bootstrap datepicker, bootstrap timepicker, fullcalendar, datatables, flaticon" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta property="og:locale" content="en_US" />
    <meta property="og:type" content="article" />
    <meta property="og:title"
        content="Metronic - The World's #1 Selling Tailwind CSS & Bootstrap Admin Template by KeenThemes" />
    <meta property="og:url" content="https://keenthemes.com/metronic" />
    <meta property="og:site_name" content="Metronic by Keenthemes" />
    <link rel="canonical" href="http://preview.keenthemes.comauthentication/general/coming-soon.html" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />
    <link href="{{ asset('assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/style.bundle.css') }}" rel="stylesheet" type="text/css" />
    <script></script>
</head>

<body id="kt_body" class="auth-bg bgi-size-cover bgi-position-center bgi-no-repeat">
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
        <style>
            body {
                background-image: url("assets/media/auth/bg9.jpg");
            }

            [data-bs-theme="dark"] body {
                background-image: url("assets/media/auth/bg9-dark.jpg");
            }
        </style>
        <div class="d-flex flex-column flex-center flex-column-fluid">
            <div class="d-flex flex-column flex-center text-center p-10">
                <div class="card card-flush w-lg-650px py-5">
                    <div class="card-body py-15 py-lg-20">
                        <div class="mb-13">
                            <a href="index.html" class="">
                                <img alt="Logo" src="{{ asset('assets/media/logos/custom-2.svg') }}"
                                    class="h-40px" />
                            </a>
                        </div>
                        <h1 class="fw-bolder text-gray-900 mb-3">Fitur Sedang Dalam Pengembangan</h1>
                        <p class="text-muted fs-5">
                            Kami sedang bekerja untuk menghadirkan fitur ini sesegera mungkin. Silakan periksa kembali
                            di lain waktu.
                        </p>

                        <div class="mb-10 mt-5">
                            <a href="javascript:history.back()" class="btn btn-primary fw-bolder">Kembali ke Halaman
                                Sebelumnya</a>
                        </div>
                        <div class="mb-n5">
                            <img src="{{ asset('assets/media/auth/chart-graph.png') }}"
                                class="mw-100 mh-300px theme-light-show" alt="" />
                            <img src="{{ asset('assets/media/auth/chart-graph-dark.png') }}"
                                class="mw-100 mh-300px theme-dark-show" alt="" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        var hostUrl = "assets/";
    </script>
    <script src="{{ asset('assets/plugins/global/plugins.bundle.js') }}"></script>
    <script src="{{ asset('assets/js/scripts.bundle.js') }}"></script>
    <script src="{{ asset('assets/js/custom/authentication/sign-up/coming-soon.js') }}"></script>
</body>

</html>
