"use strict";

// Handle Signin Form
var KTSigninGeneral = (function () {
    // Variabel
    var t, e, r;

    // Fungsi init
    return {
        init: function () {
            // 1. Definisikan Elemen
            t = document.querySelector("#kt_sign_in_form"); // Form Anda
            e = document.querySelector("#kt_sign_in_submit"); // Tombol Submit Anda

            // 2. Inisialisasi Validasi
            r = FormValidation.formValidation(t, {
                // Tentukan field yang akan divalidasi
                fields: {
                    // --- UBAH BAGIAN INI ---
                    login: {
                        // Gunakan 'login' (sesuai name="" di HTML)
                        validators: {
                            notEmpty: {
                                message: "NIP / No. HP wajib diisi", // Pesan error
                            },
                            digits: {
                                message: "NIP / No. HP harus berupa angka", // Validasi angka
                            },
                        },
                    },
                    // --- AKHIR PERUBAHAN ---

                    password: {
                        validators: {
                            notEmpty: {
                                message: "Password wajib diisi",
                            },
                        },
                    },
                },
                // Plugin FormValidation
                plugins: {
                    trigger: new FormValidation.plugins.Trigger(),
                    bootstrap: new FormValidation.plugins.Bootstrap5({
                        rowSelector: ".fv-row",
                        eleInvalidClass: "",
                        eleValidClass: "",
                    }),
                },
            });

            // 3. Handle Tombol Submit (INI BAGIAN PENTING)
            e.addEventListener("click", function (i) {
                i.preventDefault(); // Hentikan aksi default tombol

                // Validasi form
                r.validate().then(function (r) {
                    if (r == "Valid") {
                        // Jika form valid...

                        // Tampilkan loading di tombol
                        e.setAttribute("data-kt-indicator", "on");
                        e.disabled = !0;

                        // KIRIM FORM KE LARAVEL
                        // (Script demo aslinya tidak melakukan ini)
                        t.submit();
                    } else {
                        // Jika form tidak valid...

                        // Tampilkan popup error
                        Swal.fire({
                            text: "Maaf, pastikan NIP/No. HP dan Password sudah terisi dengan benar.",
                            icon: "error",
                            buttonsStyling: !1,
                            confirmButtonText: "Ok, saya mengerti!",
                            customClass: {
                                confirmButton: "btn btn-primary",
                            },
                        });
                    }
                });
            });
        },
    };
})();

// Inisialisasi saat dokumen siap
KTUtil.onDOMContentLoaded(function () {
    KTSigninGeneral.init();
});
