<?php
/*
|--------------------------------------------------------------------------
| CORE LTD - ABOUT US PAGE
|--------------------------------------------------------------------------
*/

require_once __DIR__ . '/includes/db.php';

$pageTitle = 'About Us | CORE LTD';

include __DIR__ . '/header.php';
?>

<!-- =========================================================
     FONT AWESOME
========================================================= -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">


<!-- =========================================================
     BREADCRUMB
========================================================= -->

<div class="breadcrumb-area">

    <div class="container-fluid">

        <div class="breadcrumb-content">

            <ul>

                <li>
                    <a href="index.php">
                        Home
                    </a>
                </li>

                <li class="active">
                    About Us
                </li>

            </ul>

        </div>

    </div>

</div>


<!-- =========================================================
     ABOUT HERO
========================================================= -->

<section class="core-about-hero">

    <div class="container-fluid">

        <div class="row align-items-center">

            <!-- LEFT -->

            <div class="col-lg-6 col-md-12">

                <div class="core-about-hero-content">

                    <span class="core-about-label">
                        ABOUT CORE LTD
                    </span>

                    <h1>
                        Connecting People,
                        <span>Businesses & Technology</span>
                    </h1>

                    <p>
                        CORE LTD is a technology and networking solutions
                        company dedicated to providing reliable networking,
                        internet, CCTV, fiber optic and ICT solutions to
                        homes, businesses and organizations.
                    </p>

                    <p>
                        We combine quality products, professional technical
                        services and dependable customer support to help our
                        customers stay connected and secure.
                    </p>

                    <div class="core-about-buttons">

                        <a href="contact.php" class="core-about-btn">
                            Contact Us
                            <i class="fa fa-arrow-right"></i>
                        </a>

                        <a href="index.php" class="core-about-btn-outline">
                            Shop Products
                        </a>

                    </div>

                </div>

            </div>


            <!-- RIGHT -->

            <div class="col-lg-6 col-md-12">

                <div class="core-about-hero-image">

                    <div class="core-about-tiktok">

                        <blockquote class="tiktok-embed"
                            cite="https://www.tiktok.com/@core_ltd/video/7642787180821138696"
                            data-video-id="7642787180821138696" style="max-width: 605px; min-width: 325px;">

                            <section>
                                <a target="_blank" href="https://www.tiktok.com/@core_ltd/video/7642787180821138696">
                                    @core_ltd
                                </a>
                            </section>

                        </blockquote>

                    </div>


                    <div class="core-about-floating-card">

                        <div class="core-about-floating-icon">
                            <i class="fa fa-network-wired"></i>
                        </div>

                        <div>
                            <strong>
                                Reliable Technology
                            </strong>

                            <span>
                                Solutions you can trust
                            </span>
                        </div>

                    </div>

                </div>

            </div>

            <script async src="https://www.tiktok.com/embed.js"></script>
            <style>
                .core-about-tiktok {
                    width: 100%;
                    height: 430px;
                    overflow: hidden;
                    border-radius: 8px;
                    position: relative;
                    background: #f8f8f8;
                    display: flex;
                    justify-content: center;
                }

                .core-about-tiktok .tiktok-embed {
                    margin: 0 !important;
                    width: 100% !important;
                    max-width: 100% !important;
                    min-width: 0 !important;
                }
            </style>
        </div>

    </div>

</section>



<!-- =========================================================
     COMPANY INTRODUCTION
========================================================= -->

<section class="core-about-introduction">

    <div class="container-fluid">

        <div class="row align-items-center">

            <!-- IMAGE -->

            <div class="col-lg-5 col-md-12">

                <div class="core-about-intro-image">
                    <a href="index.php">
                        <img src="images/about-company.jpg" alt="CORE LTD"
                            onerror="this.src='images/banner/core-cctv.png';">
                    </a>
                </div>

            </div>


            <!-- CONTENT -->

            <div class="col-lg-7 col-md-12">

                <div class="core-about-intro-content">

                    <span class="core-section-label">
                        WHO WE ARE
                    </span>

                    <h2>
                        Your Trusted Technology & Networking Partner
                    </h2>

                    <p>
                        At <strong>CORE LTD</strong>, we believe that
                        technology should make life easier, businesses
                        more productive and communities more connected.
                    </p>

                    <p>
                        Our goal is to provide practical and affordable
                        technology solutions while maintaining high
                        standards of quality, professionalism and customer
                        service.
                    </p>

                    <p>
                        From networking equipment and fiber optic products
                        to CCTV systems, routers, switches, access points
                        and other ICT accessories, we provide products and
                        services designed to meet different customer needs.
                    </p>

                    <div class="core-about-check-list">

                        <div>
                            <i class="fa fa-check-circle"></i>
                            Quality Networking Products
                        </div>

                        <div>
                            <i class="fa fa-check-circle"></i>
                            Professional Installation Services
                        </div>

                        <div>
                            <i class="fa fa-check-circle"></i>
                            CCTV & Security Solutions
                        </div>

                        <div>
                            <i class="fa fa-check-circle"></i>
                            Fiber Optic Solutions
                        </div>

                        <div>
                            <i class="fa fa-check-circle"></i>
                            Reliable Customer Support
                        </div>

                        <div>
                            <i class="fa fa-check-circle"></i>
                            Affordable Technology Solutions
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>



<!-- =========================================================
     WHAT WE DO
========================================================= -->

<section class="core-about-services">

    <div class="container-fluid">

        <div class="li-section-title core-section-heading text-center">

            <h2>
                <span>What We Do</span>
            </h2>

            <p>
                Technology solutions designed around your needs.
            </p>

        </div>


        <div class="row">


            <!-- NETWORKING -->

            <div class="col-lg-4 col-md-6 mb-30">
                <a href="services.php">
                    <div class="core-about-service-card">

                        <div class="core-about-service-icon">

                            <i class="fa fa-network-wired"></i>

                        </div>

                        <h3>
                            Networking Solutions
                        </h3>

                        <p>
                            We provide networking equipment and solutions
                            including routers, switches, access points,
                            wireless networks and structured networking
                            solutions.
                        </p>

                    </div>
                </a>
            </div>


            <!-- INTERNET -->

            <div class="col-lg-4 col-md-6 mb-30">

                <a href="services.php" class="core-about-service-card">

                    <div class="core-about-service-icon">
                        <i class="fa fa-wifi"></i>
                    </div>

                    <h3>
                        Internet Solutions
                    </h3>

                    <p>
                        We help homes and businesses establish reliable
                        internet connectivity through professional
                        installation, configuration and networking
                        services.
                    </p>

                </a>

            </div>


            <!-- CCTV -->

            <div class="col-lg-4 col-md-6 mb-30">
                <a href="services.php">
                    <div class="core-about-service-card">

                        <div class="core-about-service-icon">

                            <i class="fa fa-video"></i>

                        </div>

                        <h3>
                            CCTV & Security
                        </h3>

                        <p>
                            We provide CCTV and security solutions designed
                            to help customers monitor, protect and secure
                            their homes, businesses and other properties.
                        </p>

                    </div>
                </a>
            </div>


            <!-- FIBER -->

            <div class="col-lg-4 col-md-6 mb-30">
                <a href="index.php">
                    <div class="core-about-service-card">

                        <div class="core-about-service-icon">

                            <i class="fa fa-sitemap"></i>

                        </div>

                        <h3>
                            Fiber Optics
                        </h3>

                        <p>
                            We supply fiber optic equipment and accessories
                            and support fiber networking and connectivity
                            requirements.
                        </p>

                    </div>
                </a>
            </div>


            <!-- EQUIPMENT -->

            <div class="col-lg-4 col-md-6 mb-30">
                <a href="index.php">
                    <div class="core-about-service-card">

                        <div class="core-about-service-icon">

                            <i class="fa fa-server"></i>

                        </div>

                        <h3>
                            ICT Equipment
                        </h3>

                        <p>
                            Our product range includes networking equipment,
                            tools, cables, accessories and other technology
                            products for different applications.
                        </p>

                    </div>
                </a>
            </div>


            <!-- TECHNICAL -->

            <div class="col-lg-4 col-md-6 mb-30">
                <a href="services.php">
                    <div class="core-about-service-card">

                        <div class="core-about-service-icon">

                            <i class="fa fa-tools"></i>

                        </div>

                        <h3>
                            Technical Services
                        </h3>

                        <p>
                            Our technical team provides installation,
                            configuration, troubleshooting and maintenance
                            services for networking and security systems.
                        </p>

                    </div>
                </a>
            </div>

        </div>

    </div>

</section>



<!-- =========================================================
     WHY CHOOSE US
========================================================= -->

<section class="core-about-why">

    <div class="container-fluid">

        <div class="row align-items-center">

            <div class="col-lg-6">

                <div class="core-about-why-content">

                    <span class="core-section-label">
                        WHY CHOOSE CORE LTD
                    </span>

                    <h2>
                        More Than Products.
                        <span>We Provide Solutions.</span>
                    </h2>

                    <p>
                        Choosing the right technology equipment is only
                        part of the solution. At CORE LTD, we focus on
                        understanding what our customers need and helping
                        them find the right solution.
                    </p>

                    <div class="core-about-why-item">

                        <div class="core-about-why-icon">
                            <i class="fa fa-check"></i>
                        </div>

                        <div>

                            <h4>
                                Quality Products
                            </h4>

                            <p>
                                We strive to provide reliable and
                                dependable technology products.
                            </p>

                        </div>

                    </div>


                    <div class="core-about-why-item">

                        <div class="core-about-why-icon">
                            <i class="fa fa-user-tie"></i>
                        </div>

                        <div>

                            <h4>
                                Professional Service
                            </h4>

                            <p>
                                Our technical team is committed to
                                professional installation and support.
                            </p>

                        </div>

                    </div>


                    <div class="core-about-why-item">

                        <div class="core-about-why-icon">
                            <i class="fa fa-headset"></i>
                        </div>

                        <div>

                            <h4>
                                Customer Support
                            </h4>

                            <p>
                                We remain available to help our customers
                                before and after purchase.
                            </p>

                        </div>

                    </div>

                </div>

            </div>


            <div class="col-lg-6">

                <div class="core-about-why-box">

                    <div class="core-about-stat">

                        <i class="fa fa-network-wired"></i>

                        <strong>
                            Networking
                        </strong>

                        <span>
                            Solutions
                        </span>

                    </div>


                    <div class="core-about-stat">

                        <i class="fa fa-shield-halved"></i>

                        <strong>
                            Security
                        </strong>

                        <span>
                            Solutions
                        </span>

                    </div>


                    <div class="core-about-stat">

                        <i class="fa fa-globe"></i>

                        <strong>
                            Connectivity
                        </strong>

                        <span>
                            Solutions
                        </span>

                    </div>


                    <div class="core-about-stat">

                        <i class="fa fa-tools"></i>

                        <strong>
                            Technical
                        </strong>

                        <span>
                            Support
                        </span>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>



<!-- =========================================================
     MISSION & VISION
========================================================= -->

<section class="core-about-mission">

    <div class="container-fluid">

        <div class="row">


            <!-- MISSION -->

            <div class="col-lg-6 col-md-6 mb-30">

                <div class="core-about-mission-card">

                    <div class="core-about-mission-icon">

                        <i class="fa fa-bullseye"></i>

                    </div>

                    <span>
                        OUR MISSION
                    </span>

                    <h2>
                        To Connect, Empower & Secure
                    </h2>

                    <p>
                        To provide accessible, reliable and professional
                        technology solutions that help individuals,
                        businesses and organizations connect, communicate,
                        operate and grow.
                    </p>

                </div>

            </div>


            <!-- VISION -->

            <div class="col-lg-6 col-md-6 mb-30">

                <div class="core-about-mission-card">

                    <div class="core-about-mission-icon">

                        <i class="fa fa-eye"></i>

                    </div>

                    <span>
                        OUR VISION
                    </span>

                    <h2>
                        A More Connected Future
                    </h2>

                    <p>
                        To become a trusted technology partner known for
                        quality products, professional services,
                        innovation and exceptional customer experience.
                    </p>

                </div>

            </div>

        </div>

    </div>

</section>



<!-- =========================================================
     CTA
========================================================= -->

<!-- <section class="core-about-cta">

    <div class="container-fluid">

        <div class="core-about-cta-content">

            <span>
                LET'S BUILD SOMETHING BETTER
            </span>

            <h2>
                Looking for the Right Technology Solution?
            </h2>

            <p>
                Whether you need networking equipment, internet
                connectivity, CCTV, fiber optic products or technical
                support, CORE LTD is ready to help.
            </p>

            <div class="core-about-cta-buttons">

                <a href="contact.php">
                    Contact Us
                    <i class="fa fa-arrow-right"></i>
                </a>

                <a href="index.php">
                    Browse Products
                </a>

            </div>

        </div>

    </div>

</section> -->



<!-- =========================================================
     ABOUT PAGE CSS
========================================================= -->

<style>
    .core-about-hero {
        padding: 80px 0;
        background: #f7faff;
    }

    .core-about-hero-content {
        padding: 30px 60px 30px 30px;
    }

    .core-about-label,
    .core-section-label {
        display: inline-block;
        color: #1769aa;
        font-size: 14px;
        font-weight: 700;
        letter-spacing: 2px;
        margin-bottom: 15px;
    }

    .core-about-hero h1 {
        font-size: 48px;
        line-height: 1.15;
        font-weight: 700;
        color: #222;
        margin-bottom: 25px;
    }

    .core-about-hero h1 span,
    .core-about-why h2 span {
        color: #1769aa;
    }

    .core-about-hero p {
        color: #666;
        font-size: 16px;
        line-height: 1.8;
        margin-bottom: 15px;
    }

    .core-about-buttons {
        display: flex;
        gap: 15px;
        margin-top: 30px;
        flex-wrap: wrap;
    }

    .core-about-btn,
    .core-about-btn-outline {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 13px 24px;
        border-radius: 4px;
        font-weight: 600;
        text-decoration: none;
        transition: .3s;
    }

    .core-about-btn {
        background: #1769aa;
        color: #fff;
    }

    .core-about-btn:hover {
        background: #0d4f85;
        color: #fff;
    }

    .core-about-btn-outline {
        border: 1px solid #1769aa;
        color: #1769aa;
        background: #fff;
    }

    .core-about-btn-outline:hover {
        background: #1769aa;
        color: #fff;
    }

    .core-about-hero-image {
        position: relative;
        padding: 20px;
    }

    .core-about-hero-image img {
        width: 100%;
        height: 420px;
        object-fit: cover;
        border-radius: 8px;
        display: block;
    }

    .core-about-floating-card {
        position: absolute;
        left: 0;
        bottom: 0;
        background: #fff;
        padding: 18px 25px;
        display: flex;
        align-items: center;
        gap: 15px;
        box-shadow: 0 10px 35px rgba(0, 0, 0, .12);
        border-radius: 6px;
    }

    .core-about-floating-icon {
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #1769aa;
        color: #fff;
        border-radius: 50%;
    }

    .core-about-floating-card strong,
    .core-about-floating-card span {
        display: block;
    }

    .core-about-floating-card strong {
        color: #222;
    }

    .core-about-floating-card span {
        color: #777;
        font-size: 13px;
        margin-top: 3px;
    }

    .core-about-introduction {
        padding: 80px 0;
        background: #fff;
    }

    .core-about-intro-image img {
        width: 100%;
        height: 420px;
        object-fit: cover;
        border-radius: 8px;
    }

    .core-about-intro-content {
        padding: 20px 40px;
    }

    .core-about-intro-content h2,
    .core-about-why h2 {
        font-size: 36px;
        line-height: 1.3;
        color: #222;
        margin-bottom: 20px;
        font-weight: 700;
    }

    .core-about-intro-content p {
        color: #666;
        line-height: 1.8;
        font-size: 15px;
    }

    .core-about-check-list {
        margin-top: 25px;
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
    }

    .core-about-check-list div {
        color: #444;
        font-size: 14px;
        font-weight: 600;
    }

    .core-about-check-list i {
        color: #1769aa;
        margin-right: 7px;
    }

    .core-about-services {
        padding: 80px 0;
        background: #f7faff;
    }

    .core-about-services .core-section-heading p {
        color: #777;
        margin-top: 10px;
    }

    .core-about-service-card {
        height: 100%;
        padding: 35px 30px;
        background: #fff;
        border-radius: 6px;
        border: 1px solid #eee;
        transition: .3s;
    }

    .core-about-service-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0, 0, 0, .08);
    }

    .core-about-service-icon {
        width: 60px;
        height: 60px;
        background: #eef6fd;
        color: #1769aa;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        margin-bottom: 20px;
    }

    .core-about-service-card h3 {
        color: #222;
        font-size: 20px;
        margin-bottom: 12px;
    }

    .core-about-service-card p {
        color: #777;
        font-size: 14px;
        line-height: 1.8;
        margin: 0;
    }

    .core-about-why {
        padding: 80px 0;
        background: #fff;
    }

    .core-about-why-content {
        padding-right: 40px;
    }

    .core-about-why-content>p {
        color: #666;
        line-height: 1.8;
    }

    .core-about-why-item {
        display: flex;
        gap: 15px;
        margin-top: 25px;
    }

    .core-about-why-icon {
        min-width: 45px;
        height: 45px;
        background: #1769aa;
        color: #fff;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .core-about-why-item h4 {
        margin: 0 0 5px;
        color: #222;
    }

    .core-about-why-item p {
        margin: 0;
        color: #777;
        font-size: 14px;
    }

    .core-about-why-box {
        background: #1769aa;
        padding: 35px;
        border-radius: 8px;
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }

    .core-about-stat {
        background: rgba(255, 255, 255, .1);
        padding: 30px 20px;
        text-align: center;
        color: #fff;
        border-radius: 6px;
    }

    .core-about-stat i {
        font-size: 28px;
        margin-bottom: 15px;
    }

    .core-about-stat strong,
    .core-about-stat span {
        display: block;
    }

    .core-about-stat strong {
        font-size: 18px;
    }

    .core-about-stat span {
        opacity: .8;
        font-size: 13px;
        margin-top: 4px;
    }

    .core-about-mission {
        padding: 70px 0;
        background: #f7faff;
    }

    .core-about-mission-card {
        background: #fff;
        padding: 40px;
        height: 100%;
        border-radius: 8px;
        border: 1px solid #eee;
    }

    .core-about-mission-icon {
        width: 60px;
        height: 60px;
        background: #1769aa;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        font-size: 24px;
        margin-bottom: 20px;
    }

    .core-about-mission-card>span {
        color: #1769aa;
        font-size: 13px;
        font-weight: 700;
        letter-spacing: 2px;
    }

    .core-about-mission-card h2 {
        color: #222;
        font-size: 27px;
        margin: 12px 0 15px;
    }

    .core-about-mission-card p {
        color: #777;
        line-height: 1.8;
        margin: 0;
    }

    .core-about-cta {
        padding: 80px 0;
        background: #1769aa;
        text-align: center;
    }

    .core-about-cta-content {
        max-width: 850px;
        margin: auto;
        padding: 0 20px;
    }

    .core-about-cta-content>span {
        color: rgba(255, 255, 255, .8);
        font-size: 13px;
        font-weight: 700;
        letter-spacing: 2px;
    }

    .core-about-cta h2 {
        color: #fff;
        font-size: 38px;
        margin: 15px 0;
    }

    .core-about-cta p {
        color: rgba(255, 255, 255, .85);
        line-height: 1.8;
        max-width: 700px;
        margin: 0 auto;
    }

    .core-about-cta-buttons {
        display: flex;
        justify-content: center;
        gap: 15px;
        margin-top: 30px;
        flex-wrap: wrap;
    }

    .core-about-cta-buttons a {
        padding: 13px 25px;
        border-radius: 4px;
        text-decoration: none;
        font-weight: 600;
        transition: .3s;
    }

    .core-about-cta-buttons a:first-child {
        background: #fff;
        color: #1769aa;
    }

    .core-about-cta-buttons a:last-child {
        border: 1px solid #fff;
        color: #fff;
    }

    .core-about-cta-buttons a:last-child:hover {
        background: #fff;
        color: #1769aa;
    }


    /* =========================================================
   RESPONSIVE
========================================================= */

    @media (max-width: 991px) {

        .core-about-hero-content {
            padding: 20px 10px 40px;
        }

        .core-about-hero h1 {
            font-size: 38px;
        }

        .core-about-intro-content {
            padding: 40px 10px 10px;
        }

        .core-about-why-content {
            padding-right: 10px;
            margin-bottom: 40px;
        }

    }

    @media (max-width: 767px) {

        .core-about-hero {
            padding: 50px 0;
        }

        .core-about-hero h1 {
            font-size: 32px;
        }

        .core-about-hero-image img {
            height: 300px;
        }

        .core-about-floating-card {
            position: relative;
            left: auto;
            bottom: auto;
            margin-top: -20px;
            width: 90%;
            margin-left: auto;
            margin-right: auto;
        }

        .core-about-introduction,
        .core-about-services,
        .core-about-why {
            padding: 55px 0;
        }

        .core-about-intro-content h2,
        .core-about-why h2 {
            font-size: 29px;
        }

        .core-about-check-list {
            grid-template-columns: 1fr;
        }

        .core-about-why-box {
            grid-template-columns: 1fr 1fr;
            padding: 20px;
        }

        .core-about-mission-card {
            padding: 30px;
        }

        .core-about-cta h2 {
            font-size: 30px;
        }

    }

    @media (max-width: 480px) {

        .core-about-why-box {
            grid-template-columns: 1fr;
        }

        .core-about-buttons,
        .core-about-cta-buttons {
            flex-direction: column;
        }

        .core-about-btn,
        .core-about-btn-outline,
        .core-about-cta-buttons a {
            width: 100%;
            justify-content: center;
            text-align: center;
        }

    }
</style>


<?php
/*
|--------------------------------------------------------------------------
| FOOTER
|--------------------------------------------------------------------------
*/

include __DIR__ . '/footer.php';
?>