@extends('layouts.header')


@section('page-title')

    Welcome back, Alex 👋

@endsection



@section('content')


    <div class="dashboard-cards">


        <div class="dashboard-card">

            <p>
                Total Raised
            </p>

            <h2>
                $1.2M ↑
            </h2>

        </div>



        <div class="dashboard-card">

            <p>
                Active Campaigns
            </p>

            <h2>
                3
            </h2>

        </div>



        <div class="dashboard-card">

            <p>
                Investor Views
            </p>

            <h2>
                847
            </h2>

        </div>



        <div class="dashboard-card">

            <p>
                Messages
            </p>

            <h2>
                12
            </h2>

        </div>


    </div>





    <div class="chart-card">


        <h3>
            Fundraising Growth
        </h3>



        <div class="fake-chart">

            <div class="chart-line"></div>


        </div>



    </div>







    <div class="dashboard-bottom">



        <div class="campaign-box">


            <h3>
                My Campaigns
            </h3>


            <div class="scroll-area">


                <table>


                    <tr>

                        <th>
                            Campaign
                        </th>

                        <th>
                            Goal
                        </th>

                        <th>
                            Raised
                        </th>

                        <th>
                            Status
                        </th>

                    </tr>



                    <tr>

                        <td>
                            Green Energy
                        </td>

                        <td>
                            $1.2M
                        </td>

                        <td>
                            $1.2M
                        </td>

                        <td>
                            Active
                        </td>

                    </tr>



                    <tr>

                        <td>
                            AI Platform
                        </td>

                        <td>
                            $500K
                        </td>

                        <td>
                            $300K
                        </td>

                        <td>
                            Completed
                        </td>

                    </tr>




                    <tr>

                        <td>
                            Health App
                        </td>

                        <td>
                            $800K
                        </td>

                        <td>
                            $400K
                        </td>

                        <td>
                            Active
                        </td>

                    </tr>




                    <tr>

                        <td>
                            Finance AI
                        </td>

                        <td>
                            $700K
                        </td>

                        <td>
                            $200K
                        </td>

                        <td>
                            Pending
                        </td>

                    </tr>



                </table>


            </div>


        </div>







        <div class="investor-box">


            <h3>
                Recent Investor Activity
            </h3>


            <div class="scroll-area">


                <p>
                    Alex Mander - High
                </p>


                <p>
                    Rachel Smith - Medium
                </p>


                <p>
                    Anna Royar - Medium
                </p>


                <p>
                    Alex Dong - Low
                </p>


            </div>



        </div>



    </div>



    @include('layouts.footer')

@endsection