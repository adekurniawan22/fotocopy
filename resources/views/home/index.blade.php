<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Katalog Barang</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f5f8fa;
            color: #181c32;
        }

        .navbar {
            background-color: #ffffff;
            box-shadow: 0px 10px 30px 0px rgba(82, 63, 105, 0.05);
            padding: 1rem 0;
        }

        .navbar-brand {
            font-weight: 700;
            color: #181c32;
            font-size: 1.5rem;
        }

        .search-container {
            position: relative;
            max-width: 600px;
            margin: 0 auto;
        }

        .form-control-solid {
            background-color: #f5f8fa;
            border-color: #f5f8fa;
            color: #5e6278;
            transition: color 0.2s ease, background-color 0.2s ease;
            padding: 1rem 1.5rem;
            border-radius: 0.85rem;
            font-weight: 500;
        }

        .form-control-solid:focus {
            background-color: #eef3f7;
            border-color: #eef3f7;
            color: #5e6278;
            box-shadow: none;
        }

        .search-icon {
            position: absolute;
            right: 1.5rem;
            top: 50%;
            transform: translateY(-50%);
            color: #a1a5b7;
        }

        .card-custom {
            border: 0;
            border-radius: 0.85rem;
            box-shadow: 0px 0px 20px 0px rgba(76, 87, 125, 0.02);
            background-color: #ffffff;
            transition: all 0.3s ease;
            height: 100%;
        }

        .card-custom:hover {
            transform: translateY(-5px);
            box-shadow: 0px 10px 40px 0px rgba(76, 87, 125, 0.1);
        }

        .card-img-wrapper {
            height: 200px;
            overflow: hidden;
            border-top-left-radius: 0.85rem;
            border-top-right-radius: 0.85rem;
            background-color: #f9f9f9;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .card-img-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .text-price {
            color: #009ef7;
            font-weight: 700;
            font-size: 1.1rem;
        }

        .badge-custom {
            background-color: #f1faff;
            color: #009ef7;
            padding: 0.5rem 0.75rem;
            border-radius: 0.45rem;
            font-weight: 600;
        }

        .pagination {
            justify-content: center;
            margin-top: 2rem;
        }

        .page-link {
            border: 0;
            border-radius: 0.5rem;
            margin: 0 3px;
            color: #5e6278;
            font-weight: 500;
            padding: 0.75rem 1rem;
        }

        .page-item.active .page-link {
            background-color: #009ef7;
            color: #fff;
        }

        #loading-spinner {
            display: none;
            text-align: center;
            padding: 2rem;
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-expand-lg sticky-top">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">
                <i class="fas fa-layer-group text-primary me-2"></i>FOTOCOPY
            </a>

            <div class="d-flex align-items-center">
                @auth
                    <div class="dropdown">
                        <button class="btn btn-light dropdown-toggle fw-bold text-dark" type="button"
                            data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle me-1"></i> {{ Auth::user()->name }}
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                            <li>
                                <form action="{{ route('dashboard.index') }}" method="GET">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-primary">Dashboard</button>
                                </form>
                            </li>
                            <li>
                                <form action="{{ route('logout.katalog') }}" method="GET">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">Logout</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="btn btn-primary fw-bold px-4">Login</a>
                @endauth
            </div>
        </div>
    </nav>

    <div class="bg-white pb-5 pt-5 mb-5 border-bottom">
        <div class="container text-center">
            <h1 class="fw-bolder mb-4 text-dark">Cari Barang</h1>

            <div class="search-container">
                <input type="text" id="search-input" class="form-control form-control-solid"
                    placeholder="Ketik nama barang...">
                <i class="fas fa-search search-icon"></i>
            </div>
        </div>
    </div>

    <div class="container pb-5">

        <div id="loading-spinner">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>

        <div id="items-container">
            @include('home.partials.item_grid')
        </div>

    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('assets/plugins/custom/fslightbox/fslightbox.bundle.js') }}"></script>

    <script>
        $(document).ready(function() {
            let timer;

            function fetchItems(page = 1) {
                let search = $('#search-input').val();
                let url = "{{ route('home') }}?page=" + page + "&search=" + search;

                $('#items-container').css('opacity', '0.5');
                $('#loading-spinner').show();

                $.ajax({
                    url: url,
                    type: "GET",
                    success: function(response) {
                        $('#items-container').html(response);
                        $('#items-container').css('opacity', '1');
                        $('#loading-spinner').hide();
                    },
                    error: function(xhr) {
                        console.log(xhr);
                        alert('Terjadi kesalahan memuat data.');
                    }
                });
            }

            $('#search-input').on('keyup', function() {
                clearTimeout(timer);
                timer = setTimeout(function() {
                    fetchItems(1);
                }, 500);
            });

            $(document).on('click', '.pagination a', function(event) {
                event.preventDefault();
                let page = $(this).attr('href').split('page=')[1];
                fetchItems(page);
            });
        });
    </script>
</body>

</html>
