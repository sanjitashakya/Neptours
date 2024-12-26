<?php
session_start();

// Check if the user is logged in
if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];

}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>navbar</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@500&display=swap');

        /* Base Rule */

        * {
            font-family: 'Poppins', sans-serif;
            padding: 0;
            margin: 0;
            box-sizing: border-box;
            outline: none;
            border: none;
            transition: all.2s linear;
            box-sizing: border-box;
        }

        html {
            font-size: 62.5%;
            scroll-behavior: smooth;
            font-family: 'arial', sans-serif;
        }

        body {
            color: var(--text-color);
        }

        a {
            text-decoration: none;
        }

        li {
            list-style: none;
        }

        img {
            width: 100%;
        }

        /** SCROLLBAR **/

        ::-webkit-scrollbar {
            width: .9rem;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: var(--maincolor);
            border-radius: 1rem;

        }       

        /* THEME */
        :root {
            --maincolor: #fc7c12;
            --secondarycolor: #d66500;
        }

        /** SCROLLBAR **/

        ::-webkit-scrollbar {
            width: .9rem;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: var(--maincolor);
            border-radius: 1rem;

        }

        /* layout */
       

        section {
            padding: 7rem 0rem 3rem 5rem;
            margin: 0 auto;
        }


        /** ========= Navbar ================== **/


        header .sticky {
            background: white;
            box-shadow: 1px 1px 18px #808080;

        }

        header {
            display: block;
            width: 100%;
            z-index: 100;
            transition: .3s linear;


        }

        .nav-section.shadow {
            box-shadow: var(--box-shadow);
        }

        .nav {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1.5rem 0;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: .8rem;
            font-size: 2.5rem;
            color: var(--text-color);
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: .15rem;
            margin-left: 3rem;

            & img {
                width: 3.8rem;
            }
        }

        .navbar {
            display: flex;
            gap: 4rem;

            & a {
                color: var(--text-color);
                font-size: 1.6rem;
                text-transform: uppercase;
                font-weight: 600;
                position: relative;
                transition: .3s linear;

                &::after {
                    position: absolute;
                    content: '';
                    background: var(--secondarycolor);
                    height: .2rem;
                    width: 100%;
                    bottom: -.2rem;
                    left: 0;
                    transform: scaleX(0);
                    transition: .3s linear;
                }
            }
        }

        .navbar a:hover::after {
            transform: scale(1);

        }

        .navbar a:hover {
            color: var(--maincolor);
        }

        .inout {
            display: flex;
            gap: 2rem;
            margin-right: 3rem;

            & a {
                color: var(--text-color);
                font-size: 1.6rem;
                text-transform: uppercase;
                font-weight: 600;
                position: relative;
                transition: .3s linear;
            }
        }



        .inout a:hover {
            color: var(--maincolor);
        }
    </style>
</head>

<body>
    <header class="nav-section">
        <div class="nav ">
            <a href="#" class="logo">
                <img src="data/logod.png" alt="logo">
                NepTours
            </a>

            <ul class="navbar">
                <li><a href="#">home</a></li>
                <li><a href="#2">Packages</a></li>
                <li><a href="#3">Services</a></li>
                <li><a href="#4">Review</a></li>
                <li><a href="routes/contact.php">contact</a></li>
            </ul>



            <ul class="inout">
                <?php if (isset($_SESSION['username'])) { ?>
                    <li style="font-size: 1.6rem; font-weight: 600;color:#fc7c12;">
                        <?php echo $_SESSION['username']; ?>
                    </li>
                    <li><a href="controller/logout.php">Logout</a></li>
                <?php } else { ?>
                    <li><a href="controller/login.php">Login</a></li>
                    <li><a href="controller/register1.php">Signup</a></li>
                <?php } ?>
            </ul>

        </div>
    </header>

</body>

</html>