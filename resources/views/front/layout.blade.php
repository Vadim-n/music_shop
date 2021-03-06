<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">


    @yield('title')

<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="/js/front/jquery.min.js"></script>
    <!-- Custom Theme files -->

    <link href="{{ asset('css/front.css') }}" rel="stylesheet">
    <link href="{{ asset('css/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet">

    <!--//theme-style-->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

    <script type="application/x-javascript"> addEventListener("load", function () {
            setTimeout(hideURLbar, 0);
        }, false);

        function hideURLbar() {
            window.scrollTo(0, 1);
        } </script>
    <!--fonts-->
    <link href='http://fonts.googleapis.com/css?family=Lato:100,300,400,700,900' rel='stylesheet' type='text/css'>
    <link href='http://fonts.googleapis.com/css?family=Roboto:400,100,300,500,700,900' rel='stylesheet' type='text/css'>
    <!--//fonts-->
    <!-- start menu -->
    <script type="text/javascript" src="/js/front/memenu.js"></script>
    <script>$(document).ready(function () {
            $(".memenu").memenu();
        });</script>
    <script src="/js/front/simpleCart.min.js"></script>

</head>
<body>
<!--header-->
<div class="header">
    <div class="header-top">
        <div class="container">
            <div class="header-left">
                <i class="fa fa-phone" aria-hidden="true"></i> +7 (473) 222-22-22
                <div class="clearfix"></div>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
    <div class="container">
        <div class="head-top">
            <div class="logo">
                <a href="/"><img src="/images/front/guitar.png" alt=""></a>
            </div>
            <div class=" h_menu4">
                <ul class="memenu skyblue">
                    <li><a class="color1" href="/">Каталог</a></li>
                    <li><a class="color1" href="/about">О компании</a>
                    </li>
                    <li><a class="color1" href="/contacts">Контакты</a>
                    </li>
                </ul>
            </div>

            <div class="clearfix"></div>
        </div>
    </div>

</div>

@yield('banner')

<!--content-->
<div class="content">
    <div class="container">

        @yield('content')

    </div>
    <!---->
</div>
@yield('map')
<div class="footer">
    <div class="container">
        <div class="footer-top-at">

            <div class="col-md-6 amet-sed">
                <h4>MORE INFO</h4>
                <ul class="nav-bottom">
                    <li><a href="#">How to order</a></li>
                    <li><a href="#">FAQ</a></li>
                    <li><a href="contact.html">Location</a></li>
                    <li><a href="#">Shipping</a></li>
                    <li><a href="#">Membership</a></li>
                </ul>
            </div>
            <div class="col-md-6 amet-sed ">
                <h4>CONTACT US</h4>

                <p>
                    Contrary to popular belief</p>
                <p>The standard chunk</p>
                <p>office: +12 34 995 0792</p>
                <ul class="social">
                    <li><a href="#"><i> </i></a></li>
                    <li><a href="#"><i class="twitter"> </i></a></li>
                    <li><a href="#"><i class="rss"> </i></a></li>
                    <li><a href="#"><i class="gmail"> </i></a></li>

                </ul>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
</div>
<script src="{{ asset('js/front.js') }}"></script>
</body>
</html>

