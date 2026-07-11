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
                        <a class="active" >Register</a>
                        <a href="{{route('login')}}">Login</a>

                    </div>

                    <h1 id="accountTitle">
                        Create Founder Account
                    </h1>




                    <div class="role-toggle">

                        <button type="button" id="founderBtn" class="active">I'm a Founder</button>
                        <button type="button" id="investorBtn">I'm an Investor</button>

                    </div>


                    <!--FOUNDER FORM -->
                <form method="post" id="founderForm" action="{{ route('register.submit') }}">
                    @csrf

                    <input type="hidden" name="role" id="role" value="founder">

                        <label>Full Name</label>
                    <input type="text" name="name" placeholder="Full Name">

                        <label>Work Email</label>

                        <input type="email" name="email" placeholder="Work Email">

                        <label>Password</label>

                        <input type="password" name="password" placeholder="Password">

                        <label>Confirm password</label>

                        <input type="password" name="password_confirmation" placeholder="Confirm password">

                        <label>Company Name</label>
                        <input type="text" name="company_name" placeholder="Company Name">
                        <button class="primary-btn">Register as Founder</button>
                    </form>

                    <!-- INVESTOR FORM -->

                    <form method="POST" id="investorForm" style="display:none;" action="{{ route('register.submit') }}">
                            @csrf

                    <input type="hidden" name="role" value="investor">

                        <label>Full Name</label>

                    <input type="text" name="name" placeholder="Full Name">
                        <label>Work Email</label>

                        <input type="email" name="email" placeholder="Work Email">


                        <label>Password</label>
                        <input type="password" name="password" placeholder="Password">

                        <label>Confirm password</label>
                        <input type="password" name="password_confirmation" placeholder="Confirm password">

                        <label>Company Name</label>

                        <input input="text" placeholder="Company Name" name="company_name">

                        <button class="primary-btn">Register as Investor</button>

                    </form>
                </div>
            </section>
@endsection