<?php
session_start();
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact</title>
    <link rel="stylesheet" href="../public/CSS/contact.css">

    <link rel="shortcut icon" href="../data/logod.png" type="image/x-icon">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
        integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

</head>

<body>
    <!-- ==========  NAVBAR Section ========== -->
    <header class="nav-section">
        <div class="nav">
            <a href="../index.php" class="logo">
                <img src="../data/logod.png" alt="logo">
                NepTours</a>

            <ul class="navbar">
                <li><a href="../index.php">home</a></li>
                <li><a href="../index.php#2">Packages</a></li>
                <li><a href="../index.php#3">Services</a></li>
                <li><a href="#">contact</a></li>
                <?php if (isset($_SESSION['username'])) { ?>
                    <li><a href="mybookings.php">My Bookings</a></li>
                <?php } ?>
            </ul>

            <ul class="inout">
                <?php if (isset($_SESSION['username'])) { ?>
                    <li style="font-size: 1.6rem; font-weight: 600;color:#fc7c12;"><?php echo $_SESSION['username']; ?></li>
                    <li><a href="../controller/logout.php">Logout</a></li>
                <?php } else { ?>
                    <li><a href="../controller/login.php">Login</a></li>
                    <li><a href="../controller/register1.php">Signup</a></li>
                <?php } ?>
            </ul>
            </ul>

        </div>
    </header>

    <!-- ========== End NAVBAR Section ========== -->

    <section class="container">
        <div class="Location">
            <iframe
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d17691.487260828464!2d144.86979331462544!3d-38.38501300400106!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x6ad5c5be847c6443%3A0xc871ff58d2728e7d!2sNeptours!5e0!3m2!1sne!2snp!4v1710004032016!5m2!1sne!2snp"
                width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"
                referrerpolicy="no-referrer-when-downgrade">
            </iframe>
        </div>


        <div class="contact">
            <div class="contact-col">
                <div class="cons">
                    <div class="title">
                        <h2>MEET US</h2>
                        <p>If you are looking for some help don't hesitate to pay us a visit.We will clarify you about
                            your
                            quries.Or if you can't make our office just fill in the contact form or call us we'd love to
                            to hear about
                            your thoughts.
                        </p>
                    </div>

                    <div class="number">
                        <div class="contact-section">
                            <div class="icon-container">
                                <i class="fa-solid fa-mobile-screen"></i>
                            </div>
                            <div class="text-container">
                                <h2>Give us a call</h2>
                                <a href="tel:+977 98********">+977 98********</a>
                            </div>
                        </div>
                        <div class="contact-section">
                            <div class="icon-container">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div class="text-container">
                                <h2>Send us an email</h2>
                                <a href="mailto:gitesholi111@gmail.com">gitesholi111@gmail.com</a>
                            </div>
                        </div>
                    </div>

                </div>
            </div>


        </div>

    </section>

</body>

</html>