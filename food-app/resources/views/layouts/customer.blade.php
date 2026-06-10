<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Food Delivery</title>
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/all.min.css') }}" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body class="bg-light">

    <nav class="navbar navbar-light bg-white shadow-sm p-3">
        <div class="container d-flex justify-content-between align-items-center">

            <a class="navbar-brand text-danger fw-bold m-0" href="{{ url('/') }}">
                🍔 Foodie
            </a>

            <div class="d-flex align-items-center gap-3">
                <a class="text-decoration-none text-dark" href="{{ url('/') }}">Home</a>

                @guest
                <a class="text-decoration-none text-dark" href="{{ route('login') }}">Login</a>
                <a class="btn btn-danger btn-sm" href="{{ route('register') }}">Sign Up</a>
                @else
                <a class="text-decoration-none text-dark" href="{{route('cart.index')}}">
                Cart
                </a>

                <a class="text-decoration-none text-dark" href="{{ route('dashboard') }}">Dashboard</a>

                <a class="text-decoration-none text-dark" href="{{ route('queries.create') }}">Contact Support</a>

                <form action="{{ route('logout') }}" method="POST" class="m-0">
                    @csrf
                    <button type="submit" class="btn btn-link text-dark text-decoration-none p-0">Logout</button>
                </form>
                @endguest
            </div>

        </div>
    </nav>

    <main class="container py-4">
        @yield('content')
    </main>

</body>
<script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
</html>