<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        @yield('title', 'FundBridge')
    </title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    @vite([
    'resources/css/app.css',
    'resources/js/fundbridge.js'
])
</head>
<body>
    <header class="header">
        <div class="navbar">
            <a href="{{route('home')}}" class="logo">
                <span>
                    <i class="fa-solid fa-bridge"></i>
                </span>
                FundBridge
            </a>
            <nav>
                <a href="{{route('home')}}">Home</a>
                <a href="#">Discover</a>
                <a href="#">How it Works</a>
                <a href="{{route('login')}}">Login</a>
            </nav>


            <div class="actions">
                <button id="themeToggle" class="theme-btn">
                    <i class="fa-solid fa-moon"></i>
                </button>
                <a href="{{route('register')}}">
                    <button class="primary-btn">Get Started</button>
                </a>
            </div>
        </div>
    </header>


    @yield('content')

    <footer class="footer container">
        <div>
            <h3>FundBridge</h3>
            <p>
                Connecting founders with investors
                to build a better future.
            </p>
        </div>

        <div>
            <h4>Platform</h4>
            <a>Home</a>
            <a>Discover</a>
            <a>How it Works</a>
        </div>

        <div>

            <h4>For Founders</h4>
            <a>Create Profile</a>
            <a>Pitch Guideline</a>
            <a>Success Stories</a>

        </div>

        <div>

            <h4>For Investors</h4>
            <a>Browse Startups</a>
            <a>Investment Guide</a>

        </div>

        <div>

            <h4>Newsletter</h4>
            <input placeholder="Enter email">

        </div>
    </footer>

    <div class="copyright">
        © 2026 FundBridge. All rights reserved.
    </div>

</body>

</html>