@extends('layouts.app')

@section('title')
    Login | FundBridge
@endsection

@section('content')
    <section class="auth-page login-page">
        <div class="auth-left">
            <div class="brand">
                <span>
                    <i class="fa-solid fa-bridge"></i>
                </span>
                FundBridge
            </div>

            <p class="brand-text">
                Bridging Ideas, Capital & Opportunities
            </p>

            <div class="stats">
                <div>
                    Raised
                    <strong>$125M+</strong>
                </div>

                <div>
                    Startups
                    <strong>1500+</strong>
                </div>

                <div>
                    Investors
                    <strong>800+</strong>
                </div>

            </div>

        </div>

        <div class="auth-right">

            <div class="auth-tabs">
                <a href="{{route('register')}}">Register</a>
                <a class="active">Login</a>

            </div>

            <h1 id="loginTitle">Welcome Back</h1>

            <p class="login-subtitle">Login to your account</p>

            <div class="role-toggle">

                <button id="founderLogin" class="active" type="button">I'm a Founder</button>
                <button id="investorLogin" type="button" type="button">I'm an Investor</button>


            </div>

            <!-- Founder Login -->

            <form method="POST" action="{{ route('login.submit') }}" id="founderLoginForm">
                @csrf

                <label>Email Address</label>

                <input type="email" name="email" placeholder="Enter your email">

                <label>Password</label>

                <input type="password" name="password" placeholder="Enter your password">

                <u><a class="forgot">Forgot Password?</a></u>

                <button class="primary-btn">Login</button>
            </form>



            <!-- Investor Login -->

            <form method="post" id="investorLoginForm" style="display:none;" action="{{ route('login.submit') }}">
                @csrf
                <label>Email Address</label>

                <input type="email" name="email" placeholder="Enter investor email">
                <label>Password</label>

                <input type="password" name="password" placeholder="Enter password">

                <u><a class="forgot">Forgot Password?</a></u>

                <button class="primary-btn">Login as Investor</button>
            </form>
        </div>
    </section>
@endsection