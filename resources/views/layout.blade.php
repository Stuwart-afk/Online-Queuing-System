<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>ACLC Queuing System</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@700;800&display=swap"
        rel="stylesheet">

    <style>

        * {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            min-height: 100%;
        }

        body {
            font-family: 'Inter', sans-serif;
        }


        /* =========================================
           SHARED NAVIGATION
        ========================================== */

        .site-nav {
            position: fixed;

            top: 25px;
            right: 50px;

            display: flex;
            align-items: center;

            gap: 35px;

            z-index: 9999;
        }


        .site-nav a {
            color: white;

            text-decoration: none;

            font-family: 'Inter', sans-serif;

            font-size: 16px;

            font-weight: 600;

            transition:
                color 0.25s ease,
                transform 0.25s ease;
        }


        .site-nav a:hover {
            color: #159cff;

            transform: translateY(-1px);
        }


        .site-nav a.active {
            color: #159cff;
        }


        /* =========================================
           PAGE CONTENT
        ========================================== */

        .page-content {
            min-height: 100vh;
        }


        /* =========================================
           MOBILE
        ========================================== */

        @media (max-width: 700px) {

            .site-nav {
                top: 20px;
                right: 20px;

                gap: 15px;
            }

            .site-nav a {
                font-size: 13px;
            }

        }

    </style>

</head>


<body>


    <!-- =========================================
         SHARED NAVIGATION
    ========================================== -->

    <nav class="site-nav">

        <a
            href="{{ url('/') }}"
            class="{{ request()->is('/') ? 'active' : '' }}"
        >
            Home
        </a>


        <a
            href="{{ url('/contact') }}"
            class="{{ request()->is('contact') ? 'active' : '' }}"
        >
            Contact
        </a> 



        <a
            href="{{ url('/about') }}"
            class="{{ request()->is('about') ? 'active' : '' }}"
        >
            About
        </a>


        

    </nav>


    <!-- =========================================
         PAGE CONTENT
    ========================================== -->

    <main class="page-content">

        @yield('content')

    </main>


</body>

</html>