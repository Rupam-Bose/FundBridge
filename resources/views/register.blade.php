@extends('layouts.app')


@section('title')

    Register | FundBridge

@endsection


@section('content')

    <section class="auth-page">

        <div class="auth-left">

            <div class="brand">
            <span>
                <i class="fa-solid fa-bridge"></i>
            </span>

                FundBridge

            </div>



            <div class="stats">


                <div>

                    Raised:

                    <strong>$125M+</strong>

                </div>

                <div>

                    Startups:

                    <strong>1500+</strong>

                </div>

                <div>

                    Investors:

                    <strong>800+</strong>
                </div>
            </div>
        </div>

        <div class="auth-right">

            <div class="auth-tabs">

                <span class="active">Register</span>
                <span>Sign In</span>

            </div>

            <h1>Create Account</h1>

            <div class="role-toggle">

                <button class="active">I'm a Founder</button>
                <button>I'm an Investor</button>

            </div>

            <form>

                <label>Full Name</label>
                <input placeholder="Full Name">
                <label>Work Email</label>
                <input placeholder="Work Email">
                <label>Password</label>
                <input placeholder="Password">
                <label>Company Name</label>
                <input placeholder="Company Name">

                <div class="social">

                    <button>Google</button>
                    <button>LinkedIn</button>

                </div>

                <button class="primary-btn">Register Now</button>

            </form>

        </div>

    </section>

@endsection