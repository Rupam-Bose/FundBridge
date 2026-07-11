<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">


<title>
FundBridge Dashboard
</title>


<link 
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
rel="stylesheet">


@vite([
'resources/css/app.css',
'resources/css/dashboard-header.css',
'resources/css/dashboard-sidebar.css',
'resources/css/dashboard-footer.css',
'resources/css/dashboard.css',
'resources/js/fundbridge.js'
])


</head>


<body>


<div class="dashboard-wrapper">


@include('dashboard.components.sidebar')


<main class="dashboard-main">


<header class="dashboard-header">


<div>

<h1>
@yield('page-title')
</h1>


<p>
Manage your fundraising activities
</p>


</div>



<div class="header-actions">


<button class="notification-btn">

<i class="fa-regular fa-bell"></i>

<span>
3
</span>

</button>



<button 
id="themeToggle"
class="theme-btn">

<i class="fa-solid fa-moon"></i>

</button>



<img 
src="https://i.pravatar.cc/100"
class="profile-image"
>


</div>


</header>