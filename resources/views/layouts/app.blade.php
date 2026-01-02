<!DOCTYPE html>
<html lang="en">

<head>
    <title>@yield('title', 'Metronic Dashboard')</title>
    <meta charset="utf-8" />
    <meta name="description" content="..." />
    <meta name="keywords" content="..." />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />
    <link href="{{ asset('assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/style.bundle.css') }}" rel="stylesheet" type="text/css" />
    @stack('styles')
</head>

<body id="kt_body" class="header-fixed header-tablet-and-mobile-fixed aside-fixed aside-secondary-disabled">
    {{-- Theme --}}
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
        <div class="page d-flex flex-row flex-column-fluid">
            {{-- Aside --}}
            @include('layouts.partials.aside')
            <div class="wrapper d-flex flex-column flex-row-fluid" id="kt_wrapper">
                {{-- Header --}}
                @include('layouts.partials.header')
                <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
                    <div class="container-xxl" id="kt_content_container">
                        {{-- Content --}}
                        @yield('content')
                    </div>
                </div>
                {{-- Footer --}}
                @include('layouts.partials.footer')

            </div>
        </div>
    </div>
    <div id="kt_scrolltop" class="scrolltop" data-kt-scrolltop="true">
        <i class="ki-duotone ki-arrow-up">
            <span class="path1"></span>
            <span class="path2"></span>
        </i>
    </div>
    <script>
        var hostUrl = "assets/";

        function showNotification(text, icon = 'success', title = null) {
            const config = {
                html: text,
                icon: icon,
                buttonsStyling: false,
                confirmButtonText: "Ok, mengerti!",
                customClass: {
                    confirmButton: "btn btn-primary"
                }
            };

            if (title) config.title = title;
            if (icon === 'success') {
                config.timer = 2000;
                config.showConfirmButton = false;
            }

            Swal.fire(config);
        }

        function showConfirmation(text, icon = 'warning', title = null) {
            const config = {
                html: text,
                icon: icon,
                showCancelButton: true,
                buttonsStyling: false,
                confirmButtonText: "Ya, lanjutkan!",
                cancelButtonText: "Batal",
                customClass: {
                    confirmButton: "btn btn-primary",
                    cancelButton: "btn btn-light"
                }
            };

            if (icon === 'warning' && title && title.toLowerCase().includes('hapus')) {
                config.customClass.confirmButton = "btn btn-danger";
            }

            if (title) config.title = title;

            return Swal.fire(config);
        }

        function toggleButtonLoading(button, isLoading) {
            if (!button || button.length === 0) return;
            if (isLoading) {
                button.attr('data-kt-indicator', 'on');
                button.prop('disabled', true);
            } else {
                button.removeAttr('data-kt-indicator');
                button.prop('disabled', false);
            }
        }

        function clearFormErrors(form) {
            if (!form || form.length === 0) return;
            form.find('.form-control').removeClass('is-invalid');
            form.find('.select2-selection').removeClass('is-invalid');
            form.find('.invalid-feedback').text('').hide();
            form.find('#checklist-container').removeClass('border border-danger rounded');
        }

        function displayFormErrors(form, errors) {
            if (!form || !errors) return;

            $.each(errors, function(key, value) {

                let input, errorDisplay;
                if (key.includes('list_items')) {
                    errorDisplay = form.find('#list_items_error');
                    const checklistContainer = form.find('#checklist-container');

                    checklistContainer.addClass('border border-danger rounded');

                    if (errorDisplay.length) {
                        errorDisplay.text(value[0]);
                        errorDisplay.show();
                    }
                    return;
                }

                input = form.find(`[name="${key}"]`);
                errorDisplay = form.find(`#${key}_error`);

                if (input.length) {
                    input.addClass('is-invalid');

                    if (input.hasClass('select2-hidden-accessible')) {
                        input.next('.select2-container').find('.select2-selection').addClass('is-invalid');
                    }
                }

                if (errorDisplay.length) {
                    errorDisplay.text(value[0]);
                    errorDisplay.show();
                }
            });
        }
    </script>
    <script src="{{ asset('assets/plugins/global/plugins.bundle.js') }}"></script>
    <script src="{{ asset('assets/js/scripts.bundle.js') }}"></script>
    @stack('scripts')
</body>

</html>
