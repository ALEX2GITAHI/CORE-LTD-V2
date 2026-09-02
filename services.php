<?php
/*
|--------------------------------------------------------------------------
| CORE LTD - SERVICES PAGE
|--------------------------------------------------------------------------
| Internet | CCTV | Networking | Hotspot
|--------------------------------------------------------------------------
*/

require_once __DIR__ . '/includes/db.php';

$pageTitle = "Internet, CCTV & Networking Services | CORE LTD";

include __DIR__ . '/header.php';
?>

<!-- FONT AWESOME -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

<!-- =========================================================
     CORE SERVICES PAGE
========================================================= -->

<div class="core-services-page">

    <!-- =====================================================
         BREADCRUMB
    ====================================================== -->

    <div class="breadcrumb-area">
        <div class="container-fluid">
            <div class="breadcrumb-content">
                <ul>
                    <li>
                        <a href="index.php">Home</a>
                    </li>

                    <li class="active">
                        Services
                    </li>
                </ul>
            </div>
        </div>
    </div>


    <!-- =====================================================
         HERO
    ====================================================== -->

    <section class="core-service-hero">

        <div class="container-fluid">

            <div class="core-service-hero-inner">

                <div class="core-service-hero-content">

                    <span class="core-service-eyebrow">
                        <i class="fa-solid fa-bolt"></i>
                        CORE LTD SERVICES
                    </span>

                    <h1>
                        Internet.
                        <span>CCTV.</span>
                        Networking.
                    </h1>

                    <p>
                        Professional technology services for
                        homes, businesses, apartments and hotspots.
                    </p>

                    <div class="core-service-hero-actions">

                        <a href="#internet" class="core-service-btn core-service-btn-primary">

                            <i class="fa-solid fa-wifi"></i>
                            Internet Packages

                        </a>

                        <a href="https://wa.me/254757130000?text=Hello%20CORE%20LTD%2C%20I%20need%20your%20services.%20My%20location%20is%3A%20"
                            target="_blank" class="core-service-btn core-service-btn-whatsapp">

                            <i class="fa-brands fa-whatsapp"></i>
                            WhatsApp Us

                        </a>

                    </div>

                </div>


                <!-- HERO VISUAL -->

                <div class="core-service-hero-visual">

                    <div class="core-floating-card card-one">
                        <i class="fa-solid fa-wifi"></i>
                        <span>Internet</span>
                    </div>

                    <div class="core-floating-card card-two">
                        <i class="fa-solid fa-video"></i>
                        <span>CCTV</span>
                    </div>

                    <div class="core-floating-card card-three">
                        <i class="fa-solid fa-network-wired"></i>
                        <span>Networking</span>
                    </div>

                    <div class="core-hero-circle">

                        <i class="fa-solid fa-tower-broadcast"></i>

                    </div>

                </div>

            </div>

        </div>

    </section>


    <!-- =====================================================
         SERVICES
    ====================================================== -->

    <section class="core-service-list-section">

        <div class="container-fluid">

            <div class="core-service-heading">

                <span>
                    WHAT WE DO
                </span>

                <h2>
                    Our Services
                </h2>

            </div>


            <div class="row">


                <!-- INTERNET -->

                <div class="col-lg-4 col-md-6 mb-30">

                    <div class="core-modern-service-card internet-card">

                        <div class="core-modern-icon">
                            <i class="fa-solid fa-wifi"></i>
                        </div>

                        <div class="core-modern-number">
                            01
                        </div>

                        <h3>
                            Internet Services
                        </h3>

                        <p>
                            Fast and reliable internet for homes,
                            offices, apartments and hotspots.
                        </p>

                        <a href="#internet">
                            View Packages
                            <i class="fa-solid fa-arrow-right"></i>
                        </a>

                    </div>

                </div>


                <!-- CCTV -->

                <div class="col-lg-4 col-md-6 mb-30">

                    <div class="core-modern-service-card cctv-card">

                        <div class="core-modern-icon">
                            <i class="fa-solid fa-video"></i>
                        </div>

                        <div class="core-modern-number">
                            02
                        </div>

                        <h3>
                            CCTV Installation
                        </h3>

                        <p>
                            Professional CCTV installation,
                            configuration and remote viewing.
                        </p>

                        <a href="#survey">
                            Request Survey
                            <i class="fa-solid fa-arrow-right"></i>
                        </a>

                    </div>

                </div>


                <!-- CCTV MAINTENANCE -->

                <div class="col-lg-4 col-md-6 mb-30">

                    <div class="core-modern-service-card maintenance-card">

                        <div class="core-modern-icon">
                            <i class="fa-solid fa-screwdriver-wrench"></i>
                        </div>

                        <div class="core-modern-number">
                            03
                        </div>

                        <h3>
                            CCTV Maintenance
                        </h3>

                        <p>
                            Troubleshooting, repairs, camera replacement,
                            DVR/NVR and system support.
                        </p>

                        <a href="#whatsapp">
                            Get Assistance
                            <i class="fa-solid fa-arrow-right"></i>
                        </a>

                    </div>

                </div>


                <!-- NETWORKING -->

                <div class="col-lg-4 col-md-6 mb-30">

                    <div class="core-modern-service-card networking-card">

                        <div class="core-modern-icon">
                            <i class="fa-solid fa-network-wired"></i>
                        </div>

                        <div class="core-modern-number">
                            04
                        </div>

                        <h3>
                            Network Installation
                        </h3>

                        <p>
                            Structured networking for homes,
                            offices, shops and businesses.
                        </p>

                        <a href="#survey">
                            Request Survey
                            <i class="fa-solid fa-arrow-right"></i>
                        </a>

                    </div>

                </div>


                <!-- APARTMENT -->

                <div class="col-lg-4 col-md-6 mb-30">

                    <div class="core-modern-service-card apartment-card">

                        <div class="core-modern-icon">
                            <i class="fa-solid fa-building"></i>
                        </div>

                        <div class="core-modern-number">
                            05
                        </div>

                        <h3>
                            Apartment Networking
                        </h3>

                        <p>
                            Internet distribution, Wi-Fi coverage
                            and access-point deployment.
                        </p>

                        <a href="#survey">
                            Plan Your Network
                            <i class="fa-solid fa-arrow-right"></i>
                        </a>

                    </div>

                </div>


                <!-- HOTSPOT -->

                <div class="col-lg-4 col-md-6 mb-30">

                    <div class="core-modern-service-card hotspot-card">

                        <div class="core-modern-icon">
                            <i class="fa-solid fa-tower-broadcast"></i>
                        </div>

                        <div class="core-modern-number">
                            06
                        </div>

                        <h3>
                            Hotspot Setup
                        </h3>

                        <p>
                            Wi-Fi hotspot setup for businesses,
                            apartments and public spaces.
                        </p>

                        <a href="#survey">
                            Discuss Setup
                            <i class="fa-solid fa-arrow-right"></i>
                        </a>

                    </div>

                </div>

            </div>

        </div>

    </section>


    <!-- =====================================================
         INTERNET PACKAGES
    ====================================================== -->

    <section class="core-modern-internet" id="internet">

        <div class="container-fluid">

            <div class="core-service-heading light">

                <span>
                    HOMEFIBER
                </span>

                <h2>
                    Choose Your Internet
                </h2>

                <p>
                    Fast. Reliable. Unlimited.
                </p>

            </div>


            <div class="row">


                <!-- 15 MBPS -->

                <div class="col-lg-3 col-md-6 mb-30">

                    <div class="core-internet-card">

                        <div class="core-speed">
                            15
                            <small>MBPS</small>
                        </div>

                        <div class="core-speed-line"></div>

                        <div class="core-internet-price">
                            <span>Ksh.</span>
                            1,500
                        </div>

                        <small class="core-monthly">
                            PER MONTH
                        </small>

                        <ul>

                            <li>
                                <i class="fa-solid fa-check"></i>
                                Reliable Internet
                            </li>

                            <li>
                                <i class="fa-solid fa-check"></i>
                                Home Connectivity
                            </li>

                            <li>
                                <i class="fa-solid fa-check"></i>
                                Free Installation
                            </li>

                        </ul>

                        <a href="https://wa.me/254757130000?text=Hello%20CORE%20LTD%2C%20I%20want%20the%2015Mbps%20package.%20My%20location%20is%3A%20"
                            target="_blank">

                            Get Connected

                        </a>

                    </div>

                </div>


                <!-- 25 MBPS -->

                <div class="col-lg-3 col-md-6 mb-30">

                    <div class="core-internet-card featured">

                        <div class="core-popular">
                            POPULAR
                        </div>

                        <div class="core-speed">
                            25
                            <small>MBPS</small>
                        </div>

                        <div class="core-speed-line"></div>

                        <div class="core-internet-price">
                            <span>Ksh.</span>
                            2,500
                        </div>

                        <small class="core-monthly">
                            PER MONTH
                        </small>

                        <ul>

                            <li>
                                <i class="fa-solid fa-check"></i>
                                Fast Connectivity
                            </li>

                            <li>
                                <i class="fa-solid fa-check"></i>
                                Multiple Devices
                            </li>

                            <li>
                                <i class="fa-solid fa-check"></i>
                                Free Installation
                            </li>

                        </ul>

                        <a href="https://wa.me/254757130000?text=Hello%20CORE%20LTD%2C%20I%20want%20the%2025Mbps%20package.%20My%20location%20is%3A%20"
                            target="_blank">

                            Get Connected

                        </a>

                    </div>

                </div>


                <!-- 40 MBPS -->

                <div class="col-lg-3 col-md-6 mb-30">

                    <div class="core-internet-card">

                        <div class="core-speed">
                            40
                            <small>MBPS</small>
                        </div>

                        <div class="core-speed-line"></div>

                        <div class="core-internet-price">
                            <span>Ksh.</span>
                            3,500
                        </div>

                        <small class="core-monthly">
                            PER MONTH
                        </small>

                        <ul>

                            <li>
                                <i class="fa-solid fa-check"></i>
                                High-Speed Internet
                            </li>

                            <li>
                                <i class="fa-solid fa-check"></i>
                                Multiple Users
                            </li>

                            <li>
                                <i class="fa-solid fa-check"></i>
                                Free Installation
                            </li>

                        </ul>

                        <a href="https://wa.me/254757130000?text=Hello%20CORE%20LTD%2C%20I%20want%20the%2040Mbps%20package.%20My%20location%20is%3A%20"
                            target="_blank">

                            Get Connected

                        </a>

                    </div>

                </div>


                <!-- 60 MBPS -->

                <div class="col-lg-3 col-md-6 mb-30">

                    <div class="core-internet-card">

                        <div class="core-speed">
                            60
                            <small>MBPS</small>
                        </div>

                        <div class="core-speed-line"></div>

                        <div class="core-internet-price">
                            <span>Ksh.</span>
                            5,000
                        </div>

                        <small class="core-monthly">
                            PER MONTH
                        </small>

                        <ul>

                            <li>
                                <i class="fa-solid fa-check"></i>
                                Premium Speed
                            </li>

                            <li>
                                <i class="fa-solid fa-check"></i>
                                Heavy Usage
                            </li>

                            <li>
                                <i class="fa-solid fa-check"></i>
                                Free Installation
                            </li>

                        </ul>

                        <a href="https://wa.me/254757130000?text=Hello%20CORE%20LTD%2C%20I%20want%20the%2060Mbps%20package.%20My%20location%20is%3A%20"
                            target="_blank">

                            Get Connected

                        </a>

                    </div>

                </div>

            </div>


            <!-- INTERNET NOTE -->

            <div class="core-internet-note">

                <div>

                    <i class="fa-solid fa-router"></i>

                    <strong>
                        Router: Ksh. 2,000
                    </strong>

                </div>

                <div>

                    <i class="fa-solid fa-gift"></i>

                    <strong>
                        Free Installation
                    </strong>

                </div>

                <div>

                    <i class="fa-solid fa-location-dot"></i>

                    <strong>
                        Send Your Location
                    </strong>

                </div>

            </div>

        </div>

    </section>


    <!-- =====================================================
         SURVEY PROCESS
    ====================================================== -->

    <section class="core-simple-process" id="survey">

        <div class="container-fluid">

            <div class="core-service-heading">

                <span>
                    FOR PROJECTS
                </span>

                <h2>
                    Survey → Quote → Install
                </h2>

                <p>
                    For CCTV and networking projects, we survey first
                    then prepare your quotation.
                </p>

            </div>


            <div class="row">


                <div class="col-lg-4 col-md-4 mb-30">

                    <div class="core-process-simple">

                        <div>
                            01
                        </div>

                        <i class="fa-solid fa-location-dot"></i>

                        <h3>
                            Survey
                        </h3>

                        <p>
                            We visit and assess the site.
                        </p>

                    </div>

                </div>


                <div class="col-lg-4 col-md-4 mb-30">

                    <div class="core-process-simple">

                        <div>
                            02
                        </div>

                        <i class="fa-solid fa-file-invoice"></i>

                        <h3>
                            Quotation
                        </h3>

                        <p>
                            We prepare a quotation based on the survey.
                        </p>

                    </div>

                </div>


                <div class="col-lg-4 col-md-4 mb-30">

                    <div class="core-process-simple">

                        <div>
                            03
                        </div>

                        <i class="fa-solid fa-screwdriver-wrench"></i>

                        <h3>
                            Installation
                        </h3>

                        <p>
                            Once approved, we install and configure.
                        </p>

                    </div>

                </div>

            </div>

        </div>

    </section>


    <!-- =====================================================
         CCTV + NETWORKING
    ====================================================== -->

    <section class="core-service-highlight">

        <div class="container-fluid">

            <div class="row">


                <!-- CCTV -->

                <div class="col-lg-6 mb-30">

                    <div class="core-highlight-card cctv-highlight">

                        <div class="core-highlight-icon">
                            <i class="fa-solid fa-video"></i>
                        </div>

                        <h2>
                            CCTV
                        </h2>

                        <p>
                            Installation & Maintenance
                        </p>

                        <div class="core-highlight-list">

                            <span>
                                <i class="fa-solid fa-check"></i>
                                Installation
                            </span>

                            <span>
                                <i class="fa-solid fa-check"></i>
                                Maintenance
                            </span>

                            <span>
                                <i class="fa-solid fa-check"></i>
                                DVR / NVR
                            </span>

                            <span>
                                <i class="fa-solid fa-check"></i>
                                Remote Viewing
                            </span>

                        </div>

                        <a href="https://wa.me/254757130000?text=Hello%20CORE%20LTD%2C%20I%20need%20CCTV%20services.%20My%20location%20is%3A%20"
                            target="_blank">

                            Request CCTV Service
                            <i class="fa-solid fa-arrow-right"></i>

                        </a>

                    </div>

                </div>


                <!-- NETWORKING -->

                <div class="col-lg-6 mb-30">

                    <div class="core-highlight-card networking-highlight">

                        <div class="core-highlight-icon">
                            <i class="fa-solid fa-network-wired"></i>
                        </div>

                        <h2>
                            Networking
                        </h2>

                        <p>
                            Apartments, Offices & Hotspots
                        </p>

                        <div class="core-highlight-list">

                            <span>
                                <i class="fa-solid fa-check"></i>
                                Apartment Networks
                            </span>

                            <span>
                                <i class="fa-solid fa-check"></i>
                                Wi-Fi Access Points
                            </span>

                            <span>
                                <i class="fa-solid fa-check"></i>
                                Hotspot Setup
                            </span>

                            <span>
                                <i class="fa-solid fa-check"></i>
                                Network Cabling
                            </span>

                        </div>

                        <a href="https://wa.me/254757130000?text=Hello%20CORE%20LTD%2C%20I%20need%20networking%20services.%20My%20location%20is%3A%20"
                            target="_blank">

                            Plan My Network
                            <i class="fa-solid fa-arrow-right"></i>

                        </a>

                    </div>

                </div>

            </div>

        </div>

    </section>


    <!-- =====================================================
         WHATSAPP CTA
    ====================================================== -->

    <!-- <section class="core-service-whatsapp" id="whatsapp">

        <div class="container-fluid">

            <div class="core-whatsapp-inner">

                <div class="core-whatsapp-big-icon">

                    <i class="fa-brands fa-whatsapp"></i>

                </div>

                <div>

                    <span>
                        NEED A SERVICE?
                    </span>

                    <h2>
                        Chat With CORE LTD
                    </h2>

                    <p>
                        Tell us the service you need,
                        your location and contact.
                    </p>

                </div>

                <a href="https://wa.me/254757130000?text=Hello%20CORE%20LTD%2C%20I%20need%20your%20services.%0A%0AService%3A%20%0ALocation%3A%20%0AName%3A%20%0APhone%3A%20"
                    target="_blank">

                    <i class="fa-brands fa-whatsapp"></i>

                    Chat Now

                </a>

            </div>

        </div>

    </section> -->


    <!-- =====================================================
         CONTACT STRIP
    ====================================================== -->

    <section class="core-service-contact">

        <div class="container-fluid">

            <div class="row">

                <div class="col-lg-4 col-md-6 mb-20 mb-lg-0">

                    <div class="core-service-contact-item">

                        <i class="fa-brands fa-whatsapp""></i>

                        <div>

                            <a href="
                            https://wa.me/254757130000?text=Hello%20CORE%20LTD%2C%20I%20need%20your%20services.%0A%0AService%3A%20%0ALocation%3A%20%0AName%3A%20%0APhone%3A%20"
                            target="_blank">

                            <strong>
                                Chat with Us
                            </strong>
                            </a>

                    </div>

                </div>

            </div>


            <div class="col-lg-4 col-md-6 mb-20 mb-lg-0">

                <div class="core-service-contact-item">

                    <i class="fa-solid fa-location-dot"></i>

                    <div>

                        <small>
                            SERVICE AREA
                        </small>

                        <strong>
                            Nairobi & Surrounding Areas
                        </strong>

                    </div>

                </div>

            </div>


            <div class="col-lg-4 col-md-12">

                <div class="core-service-contact-item">

                    <i class="fa-solid fa-headset"></i>

                    <div>

                        <small>
                            SERVICES
                        </small>

                        <strong>
                            Internet • CCTV • Networking
                        </strong>

                    </div>

                </div>

            </div>

        </div>

</div>

</section>

</div>

<style>
    /* =========================================================
   CORE LTD - SERVICES PAGE
   Colourful Technology Theme
========================================================= */

    .core-services-page {
        --core-blue: #087fce;
        --core-dark-blue: #062f5b;
        --core-light-blue: #00a8e8;
        --core-cyan: #00c6ff;
        --core-yellow: #ffd400;
        --core-orange: #ff8c00;
        --core-red: #e31e24;
        --core-green: #19b85b;
        --core-dark: #071a2f;
        --core-text: #333;
        --core-light: #f5f9fc;
        --core-white: #ffffff;

        background: #fff;
        color: var(--core-text);
    }


    /* =========================================================
   BREADCRUMB
========================================================= */

    .core-services-page .breadcrumb-area {
        background: linear-gradient(135deg,
                #062f5b 0%,
                #087fce 55%,
                #00a8e8 100%);
        padding: 15px 0;
        margin: 0;
    }

    .core-services-page .breadcrumb-content ul {
        margin: 0;
        padding: 0;
    }

    .core-services-page .breadcrumb-content ul li {
        color: #fff;
        font-size: 14px;
    }

    .core-services-page .breadcrumb-content ul li a {
        color: #fff;
        transition: .3s ease;
    }

    .core-services-page .breadcrumb-content ul li a:hover {
        color: var(--core-yellow);
    }

    .core-services-page .breadcrumb-content ul li.active {
        color: var(--core-yellow);
        font-weight: 700;
    }


    /* =========================================================
   HERO SECTION
========================================================= */

    .core-service-hero {
        position: relative;
        overflow: hidden;
        min-height: 480px;

        background:
            radial-gradient(circle at 85% 20%,
                rgba(0, 198, 255, .35),
                transparent 35%),
            linear-gradient(135deg,
                #041c35 0%,
                #063d70 45%,
                #087fce 100%);

        padding: 75px 0;
    }

    .core-service-hero::before {
        content: "";
        position: absolute;
        width: 500px;
        height: 500px;
        border-radius: 50%;
        border: 70px solid rgba(255, 255, 255, .04);
        right: -180px;
        top: -180px;
    }

    .core-service-hero::after {
        content: "";
        position: absolute;
        width: 350px;
        height: 350px;
        border-radius: 50%;
        border: 40px solid rgba(255, 212, 0, .06);
        left: -150px;
        bottom: -180px;
    }

    .core-service-hero-inner {
        position: relative;
        z-index: 2;

        display: flex;
        align-items: center;
        justify-content: space-between;

        gap: 50px;
    }

    .core-service-hero-content {
        width: 55%;
    }

    .core-service-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 8px;

        background: rgba(255, 255, 255, .10);
        border: 1px solid rgba(255, 255, 255, .20);

        color: var(--core-yellow);

        padding: 8px 16px;
        border-radius: 30px;

        font-size: 13px;
        font-weight: 700;
        letter-spacing: 1px;

        margin-bottom: 20px;
    }

    .core-service-hero-content h1 {
        margin: 0 0 20px;

        color: #fff;

        font-size: 58px;
        line-height: 1.05;
        font-weight: 800;

        letter-spacing: -2px;
    }

    .core-service-hero-content h1 span {
        color: var(--core-yellow);
    }

    .core-service-hero-content p {
        max-width: 650px;

        color: rgba(255, 255, 255, .85);

        font-size: 18px;
        line-height: 1.7;

        margin-bottom: 30px;
    }

    .core-service-hero-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 14px;
    }

    .core-service-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;

        gap: 9px;

        padding: 13px 22px;

        border-radius: 8px;

        font-size: 14px;
        font-weight: 700;

        transition: all .3s ease;
    }

    .core-service-btn-primary {
        background: var(--core-yellow);
        color: #111;

        box-shadow: 0 8px 20px rgba(0, 0, 0, .20);
    }

    .core-service-btn-primary:hover {
        background: #fff;
        color: var(--core-dark-blue);
        transform: translateY(-3px);
    }

    .core-service-btn-whatsapp {
        background: #25d366;
        color: #fff;

        box-shadow: 0 8px 20px rgba(0, 0, 0, .20);
    }

    .core-service-btn-whatsapp:hover {
        background: #128c7e;
        color: #fff;
        transform: translateY(-3px);
    }


    /* =========================================================
   HERO VISUAL
========================================================= */

    .core-service-hero-visual {
        position: relative;

        width: 390px;
        height: 390px;

        flex-shrink: 0;
    }

    .core-hero-circle {
        position: absolute;

        width: 230px;
        height: 230px;

        top: 80px;
        left: 80px;

        border-radius: 50%;

        display: flex;
        align-items: center;
        justify-content: center;

        background:
            radial-gradient(circle,
                #00c6ff 0%,
                #087fce 55%,
                #062f5b 100%);

        border: 10px solid rgba(255, 255, 255, .12);

        box-shadow:
            0 0 0 25px rgba(255, 255, 255, .04),
            0 25px 60px rgba(0, 0, 0, .35);
    }

    .core-hero-circle i {
        color: #fff;
        font-size: 85px;
    }

    .core-floating-card {
        position: absolute;

        display: flex;
        align-items: center;
        gap: 10px;

        background: #fff;
        color: var(--core-dark-blue);

        padding: 13px 18px;

        border-radius: 10px;

        font-size: 14px;
        font-weight: 700;

        box-shadow: 0 15px 35px rgba(0, 0, 0, .20);

        z-index: 3;

        animation: coreFloat 4s ease-in-out infinite;
    }

    .core-floating-card i {
        font-size: 20px;
    }

    .card-one {
        top: 35px;
        left: 5px;
    }

    .card-one i {
        color: var(--core-blue);
    }

    .card-two {
        top: 30px;
        right: 0;
        animation-delay: .7s;
    }

    .card-two i {
        color: var(--core-red);
    }

    .card-three {
        bottom: 45px;
        right: 20px;
        animation-delay: 1.3s;
    }

    .card-three i {
        color: var(--core-green);
    }

    @keyframes coreFloat {

        0%,
        100% {
            transform: translateY(0);
        }

        50% {
            transform: translateY(-10px);
        }
    }


    /* =========================================================
   SECTION HEADING
========================================================= */

    .core-service-heading {
        text-align: center;
        margin-bottom: 45px;
    }

    .core-service-heading>span {
        display: inline-block;

        color: var(--core-blue);

        font-size: 12px;
        font-weight: 800;

        letter-spacing: 2px;

        margin-bottom: 8px;
    }

    .core-service-heading h2 {
        margin: 0;

        color: var(--core-dark-blue);

        font-size: 36px;
        line-height: 1.2;

        font-weight: 800;
    }

    .core-service-heading p {
        margin: 10px 0 0;

        color: #777;

        font-size: 15px;
    }

    .core-service-heading.light h2 {
        color: #fff;
    }

    .core-service-heading.light>span {
        color: var(--core-yellow);
    }

    .core-service-heading.light p {
        color: rgba(255, 255, 255, .75);
    }


    /* =========================================================
   SERVICE LIST
========================================================= */

    .core-service-list-section {
        padding: 75px 0;
        background: #fff;
    }

    .core-modern-service-card {
        position: relative;

        height: 100%;

        padding: 30px;

        background: #fff;

        border-radius: 14px;

        border: 1px solid #e7eef4;

        box-shadow: 0 8px 25px rgba(6, 47, 91, .07);

        overflow: hidden;

        transition: all .35s ease;
    }

    .core-modern-service-card::before {
        content: "";

        position: absolute;

        width: 100%;
        height: 4px;

        left: 0;
        top: 0;

        background: var(--core-blue);

        transition: height .3s ease;
    }

    .core-modern-service-card:hover {
        transform: translateY(-8px);

        box-shadow:
            0 18px 40px rgba(6, 47, 91, .14);
    }

    .core-modern-service-card:hover::before {
        height: 6px;
    }

    .internet-card::before {
        background: var(--core-blue);
    }

    .cctv-card::before {
        background: var(--core-red);
    }

    .maintenance-card::before {
        background: var(--core-orange);
    }

    .networking-card::before {
        background: var(--core-green);
    }

    .apartment-card::before {
        background: #7c3aed;
    }

    .hotspot-card::before {
        background: var(--core-cyan);
    }

    .core-modern-icon {
        width: 58px;
        height: 58px;

        display: flex;
        align-items: center;
        justify-content: center;

        border-radius: 12px;

        background: #eef8ff;

        color: var(--core-blue);

        font-size: 25px;

        margin-bottom: 20px;
    }

    .cctv-card .core-modern-icon {
        background: #fff0f0;
        color: var(--core-red);
    }

    .maintenance-card .core-modern-icon {
        background: #fff6e8;
        color: var(--core-orange);
    }

    .networking-card .core-modern-icon {
        background: #edfff4;
        color: var(--core-green);
    }

    .apartment-card .core-modern-icon {
        background: #f4efff;
        color: #7c3aed;
    }

    .hotspot-card .core-modern-icon {
        background: #eafcff;
        color: var(--core-cyan);
    }

    .core-modern-number {
        position: absolute;

        right: 25px;
        top: 22px;

        color: #edf2f6;

        font-size: 38px;
        font-weight: 900;
    }

    .core-modern-service-card h3 {
        position: relative;

        margin: 0 0 10px;

        color: var(--core-dark-blue);

        font-size: 21px;
        font-weight: 800;
    }

    .core-modern-service-card p {
        color: #777;

        font-size: 14px;
        line-height: 1.7;

        margin-bottom: 18px;
    }

    .core-modern-service-card>a {
        display: inline-flex;
        align-items: center;
        gap: 8px;

        color: var(--core-blue);

        font-size: 13px;
        font-weight: 800;

        transition: .3s ease;
    }

    .core-modern-service-card>a:hover {
        color: var(--core-dark-blue);
        gap: 12px;
    }


    /* =========================================================
   INTERNET SECTION
========================================================= */

    .core-modern-internet {
        position: relative;

        padding: 80px 0;

        background:
            radial-gradient(circle at 10% 20%,
                rgba(0, 198, 255, .20),
                transparent 30%),
            linear-gradient(135deg,
                #041d36,
                #063d70 55%,
                #087fce);

        overflow: hidden;
    }

    .core-modern-internet::before {
        content: "";

        position: absolute;

        width: 450px;
        height: 450px;

        border-radius: 50%;

        border: 60px solid rgba(255, 255, 255, .035);

        right: -200px;
        bottom: -200px;
    }

    .core-internet-card {
        position: relative;

        height: 100%;

        padding: 30px 25px;

        text-align: center;

        background: rgba(255, 255, 255, .98);

        border-radius: 16px;

        box-shadow: 0 15px 35px rgba(0, 0, 0, .18);

        transition: all .35s ease;

        overflow: hidden;
    }

    .core-internet-card:hover {
        transform: translateY(-8px);

        box-shadow: 0 22px 45px rgba(0, 0, 0, .25);
    }

    .core-internet-card.featured {
        border: 3px solid var(--core-yellow);

        transform: translateY(-8px);
    }

    .core-internet-card.featured:hover {
        transform: translateY(-13px);
    }

    .core-popular {
        position: absolute;

        top: 0;
        left: 0;
        right: 0;

        background: var(--core-yellow);

        color: #111;

        padding: 7px;

        font-size: 11px;
        font-weight: 900;

        letter-spacing: 1px;
    }

    .core-speed {
        color: var(--core-dark-blue);

        font-size: 58px;
        line-height: 1;

        font-weight: 900;

        margin-top: 5px;
    }

    .core-speed small {
        display: block;

        color: var(--core-blue);

        font-size: 13px;

        letter-spacing: 2px;
    }

    .core-speed-line {
        width: 50px;
        height: 4px;

        background: linear-gradient(90deg,
                var(--core-blue),
                var(--core-cyan));

        border-radius: 10px;

        margin: 18px auto;
    }

    .core-internet-price {
        color: var(--core-dark);

        font-size: 30px;
        font-weight: 900;
    }

    .core-internet-price span {
        color: var(--core-blue);

        font-size: 14px;
        font-weight: 700;
    }

    .core-monthly {
        color: #888;

        display: block;

        font-size: 10px;

        letter-spacing: 1px;

        margin-top: 5px;
    }

    .core-internet-card ul {
        list-style: none;

        padding: 0;
        margin: 25px 0;
    }

    .core-internet-card ul li {
        color: #555;

        font-size: 13px;

        padding: 8px 0;

        border-bottom: 1px solid #edf1f4;
    }

    .core-internet-card ul li:last-child {
        border-bottom: none;
    }

    .core-internet-card ul li i {
        color: var(--core-green);

        margin-right: 5px;
    }

    .core-internet-card>a {
        display: block;

        background: var(--core-blue);

        color: #fff;

        padding: 12px 15px;

        border-radius: 7px;

        font-size: 13px;
        font-weight: 800;

        transition: .3s ease;
    }

    .core-internet-card>a:hover {
        background: var(--core-dark-blue);
        color: #fff;
    }

    .core-internet-card.featured>a {
        background: var(--core-green);
    }

    .core-internet-card.featured>a:hover {
        background: #128c45;
    }


    /* =========================================================
   INTERNET NOTE
========================================================= */

    .core-internet-note {
        display: grid;

        grid-template-columns: repeat(3, 1fr);

        gap: 15px;

        margin-top: 35px;

        padding: 18px;

        border-radius: 12px;

        background: rgba(255, 255, 255, .08);

        border: 1px solid rgba(255, 255, 255, .12);
    }

    .core-internet-note>div {
        display: flex;
        align-items: center;
        justify-content: center;

        gap: 10px;

        color: #fff;

        font-size: 13px;

        text-align: center;
    }

    .core-internet-note i {
        color: var(--core-yellow);

        font-size: 19px;
    }


    /* =========================================================
   SURVEY PROCESS
========================================================= */

    .core-simple-process {
        padding: 75px 0;

        background: #f7fafc;
    }

    .core-process-simple {
        position: relative;

        height: 100%;

        padding: 30px;

        text-align: center;

        background: #fff;

        border-radius: 14px;

        border: 1px solid #e7eef4;

        box-shadow: 0 8px 25px rgba(6, 47, 91, .06);

        transition: .3s ease;
    }

    .core-process-simple:hover {
        transform: translateY(-6px);

        box-shadow: 0 15px 35px rgba(6, 47, 91, .12);
    }

    .core-process-simple>div:first-child {
        position: absolute;

        top: 15px;
        right: 18px;

        color: #edf2f6;

        font-size: 28px;
        font-weight: 900;
    }

    .core-process-simple>i {
        width: 65px;
        height: 65px;

        display: flex;
        align-items: center;
        justify-content: center;

        margin: 0 auto 18px;

        border-radius: 50%;

        background: #eef8ff;

        color: var(--core-blue);

        font-size: 25px;
    }

    .core-process-simple:nth-child(1)>i {
        color: var(--core-blue);
    }

    .core-process-simple h3 {
        margin: 0 0 8px;

        color: var(--core-dark-blue);

        font-size: 20px;
        font-weight: 800;
    }

    .core-process-simple p {
        margin: 0;

        color: #777;

        font-size: 14px;
        line-height: 1.6;
    }


    /* =========================================================
   CCTV + NETWORKING HIGHLIGHTS
========================================================= */

    .core-service-highlight {
        padding: 75px 0;

        background: #fff;
    }

    .core-highlight-card {
        position: relative;

        height: 100%;

        padding: 35px;

        border-radius: 18px;

        overflow: hidden;

        color: #fff;

        box-shadow: 0 15px 35px rgba(0, 0, 0, .15);

        transition: .35s ease;
    }

    .core-highlight-card:hover {
        transform: translateY(-8px);
    }

    .cctv-highlight {
        background:
            radial-gradient(circle at 90% 10%,
                rgba(255, 255, 255, .15),
                transparent 30%),
            linear-gradient(135deg,
                #9e1018,
                #e31e24);
    }

    .networking-highlight {
        background:
            radial-gradient(circle at 90% 10%,
                rgba(255, 255, 255, .15),
                transparent 30%),
            linear-gradient(135deg,
                #063e6f,
                #087fce);
    }

    .core-highlight-icon {
        width: 62px;
        height: 62px;

        display: flex;
        align-items: center;
        justify-content: center;

        border-radius: 13px;

        background: rgba(255, 255, 255, .15);

        margin-bottom: 20px;

        font-size: 28px;
    }

    .core-highlight-card h2 {
        color: #fff;

        margin: 0 0 7px;

        font-size: 30px;
        font-weight: 900;
    }

    .core-highlight-card>p {
        margin: 0 0 25px;

        color: rgba(255, 255, 255, .85);

        font-size: 15px;
    }

    .core-highlight-list {
        display: grid;

        grid-template-columns: repeat(2, 1fr);

        gap: 10px;

        margin-bottom: 25px;
    }

    .core-highlight-list span {
        display: flex;
        align-items: center;

        gap: 8px;

        color: #fff;

        font-size: 13px;
    }

    .core-highlight-list i {
        color: var(--core-yellow);
    }

    .core-highlight-card>a {
        display: inline-flex;
        align-items: center;

        gap: 10px;

        background: #fff;

        color: var(--core-dark-blue);

        padding: 12px 18px;

        border-radius: 7px;

        font-size: 13px;
        font-weight: 800;

        transition: .3s ease;
    }

    .core-highlight-card>a:hover {
        background: var(--core-yellow);
        color: #111;
    }


    /* =========================================================
   WHATSAPP CTA
========================================================= */

    .core-service-whatsapp {
        padding: 45px 0;

        background:
            linear-gradient(135deg,
                #075e54,
                #128c7e,
                #25d366);
    }

    .core-whatsapp-inner {
        display: flex;

        align-items: center;

        gap: 25px;

        color: #fff;
    }

    .core-whatsapp-big-icon {
        width: 75px;
        height: 75px;

        flex-shrink: 0;

        display: flex;
        align-items: center;
        justify-content: center;

        background: rgba(255, 255, 255, .15);

        border-radius: 50%;

        font-size: 42px;
    }

    .core-whatsapp-inner>div:nth-child(2) {
        flex: 1;
    }

    .core-whatsapp-inner span {
        display: block;

        color: rgba(255, 255, 255, .75);

        font-size: 11px;
        font-weight: 800;

        letter-spacing: 2px;

        margin-bottom: 5px;
    }

    .core-whatsapp-inner h2 {
        color: #fff;

        margin: 0 0 5px;

        font-size: 28px;
        font-weight: 900;
    }

    .core-whatsapp-inner p {
        color: rgba(255, 255, 255, .85);

        margin: 0;

        font-size: 14px;
    }

    .core-whatsapp-inner>a {
        display: inline-flex;
        align-items: center;
        justify-content: center;

        gap: 9px;

        padding: 14px 25px;

        background: #fff;

        color: #075e54;

        border-radius: 8px;

        font-size: 14px;
        font-weight: 900;

        transition: .3s ease;

        white-space: nowrap;
    }

    .core-whatsapp-inner>a:hover {
        background: var(--core-yellow);

        color: #111;

        transform: translateY(-3px);
    }


    /* =========================================================
   CONTACT STRIP
========================================================= */

    .core-service-contact {
        padding: 35px 0;

        background: var(--core-dark-blue);
    }

    .core-service-contact-item {
        display: flex;

        align-items: center;

        gap: 15px;

        min-height: 60px;
    }

    .core-service-contact-item>i {
        width: 48px;
        height: 48px;

        display: flex;
        align-items: center;
        justify-content: center;

        flex-shrink: 0;

        border-radius: 10px;

        background: rgba(255, 255, 255, .10);

        color: var(--core-yellow);

        font-size: 20px;
    }

    .core-service-contact-item small {
        display: block;

        color: rgba(255, 255, 255, .55);

        font-size: 10px;
        font-weight: 800;

        letter-spacing: 1px;

        margin-bottom: 3px;
    }

    .core-service-contact-item strong {
        display: block;

        color: #fff;

        font-size: 14px;
        font-weight: 700;
    }


    /* =========================================================
   GENERAL SPACING
========================================================= */

    .core-services-page .mb-30 {
        margin-bottom: 30px;
    }

    .core-services-page .mb-20 {
        margin-bottom: 20px;
    }


    /* =========================================================
   RESPONSIVE - TABLETS
========================================================= */

    @media (max-width: 991px) {

        .core-service-hero {
            padding: 60px 0;
        }

        .core-service-hero-inner {
            flex-direction: column;

            text-align: center;
        }

        .core-service-hero-content {
            width: 100%;
        }

        .core-service-hero-content p {
            margin-left: auto;
            margin-right: auto;
        }

        .core-service-hero-actions {
            justify-content: center;
        }

        .core-service-hero-visual {
            margin-top: 10px;
        }

        .core-service-heading h2 {
            font-size: 31px;
        }

        .core-internet-card.featured {
            transform: none;
        }

        .core-internet-card.featured:hover {
            transform: translateY(-8px);
        }

        .core-whatsapp-inner {
            flex-wrap: wrap;
        }

        .core-whatsapp-inner>div:nth-child(2) {
            min-width: calc(100% - 110px);
        }

        .core-whatsapp-inner>a {
            margin-left: 100px;
        }
    }


    /* =========================================================
   RESPONSIVE - MOBILE
========================================================= */

    @media (max-width: 767px) {

        .core-service-hero {
            padding: 45px 0;
        }

        .core-service-hero-content h1 {
            font-size: 40px;

            letter-spacing: -1px;
        }

        .core-service-hero-content p {
            font-size: 15px;
        }

        .core-service-hero-actions {
            flex-direction: column;
        }

        .core-service-btn {
            width: 100%;
        }

        .core-service-hero-visual {
            width: 310px;
            height: 310px;
        }

        .core-hero-circle {
            width: 180px;
            height: 180px;

            top: 65px;
            left: 65px;
        }

        .core-hero-circle i {
            font-size: 60px;
        }

        .core-floating-card {
            padding: 9px 12px;

            font-size: 11px;
        }

        .card-one {
            left: 0;
        }

        .card-two {
            right: 0;
        }

        .card-three {
            right: 0;
            bottom: 25px;
        }

        .core-service-list-section,
        .core-simple-process,
        .core-service-highlight {
            padding: 55px 0;
        }

        .core-service-heading {
            margin-bottom: 30px;
        }

        .core-service-heading h2 {
            font-size: 27px;
        }

        .core-modern-service-card {
            padding: 25px;
        }

        .core-modern-service-card h3 {
            font-size: 19px;
        }

        .core-modern-service-card p {
            font-size: 13px;
        }

        .core-modern-internet {
            padding: 55px 0;
        }

        .core-internet-card {
            padding: 28px 20px;
        }

        .core-internet-note {
            grid-template-columns: 1fr;

            gap: 10px;
        }

        .core-internet-note>div {
            justify-content: flex-start;

            padding: 8px;
        }

        .core-highlight-card {
            padding: 28px;
        }

        .core-highlight-card h2 {
            font-size: 26px;
        }

        .core-highlight-list {
            grid-template-columns: 1fr;
        }

        .core-whatsapp-inner {
            flex-direction: column;

            text-align: center;
        }

        .core-whatsapp-big-icon {
            width: 65px;
            height: 65px;

            font-size: 35px;
        }

        .core-whatsapp-inner>div:nth-child(2) {
            min-width: 100%;
        }

        .core-whatsapp-inner h2 {
            font-size: 24px;
        }

        .core-whatsapp-inner>a {
            margin-left: 0;

            width: 100%;
        }

        .core-service-contact-item {
            justify-content: center;

            text-align: left;
        }

    }


    /* =========================================================
   SMALL MOBILE
========================================================= */

    @media (max-width: 480px) {

        .core-service-hero-content h1 {
            font-size: 34px;
        }

        .core-service-hero-visual {
            width: 280px;
            height: 280px;
        }

        .core-hero-circle {
            width: 160px;
            height: 160px;

            top: 60px;
            left: 60px;
        }

        .core-hero-circle i {
            font-size: 50px;
        }

        .core-floating-card {
            padding: 8px 10px;
        }

        .core-floating-card i {
            font-size: 16px;
        }

        .core-speed {
            font-size: 50px;
        }

        .core-internet-price {
            font-size: 27px;
        }

        .core-service-contact-item strong {
            font-size: 13px;
        }

    }

    /* =========================================================
   CORE LTD - CCTV & NETWORKING HIGHLIGHT CARDS
   ========================================================= */

    .core-service-highlight {
        padding: 20px 0 50px;
        position: relative;
    }

    .core-highlight-card {
        position: relative;
        padding: 35px 35px 32px;
        border-radius: 18px;
        overflow: hidden;
        min-height: 340px;

        background: #ffffff;

        border: 1px solid rgba(0, 0, 0, 0.06);

        box-shadow:
            0 8px 30px rgba(0, 0, 0, 0.08);

        transition:
            transform 0.35s ease,
            box-shadow 0.35s ease;

        z-index: 1;
    }


    /* =========================================================
   DECORATIVE BACKGROUND
   ========================================================= */

    .core-highlight-card::before {
        content: "";
        position: absolute;

        width: 220px;
        height: 220px;

        right: -80px;
        top: -90px;

        border-radius: 50%;

        opacity: 0.12;

        z-index: -1;

        transition:
            transform 0.5s ease,
            opacity 0.4s ease;
    }

    .core-highlight-card::after {
        content: "";
        position: absolute;

        width: 120px;
        height: 120px;

        right: 30px;
        bottom: -70px;

        border-radius: 50%;

        opacity: 0.07;

        z-index: -1;
    }


    /* =========================================================
   HOVER
   ========================================================= */

    .core-highlight-card:hover {
        transform: translateY(-8px);

        box-shadow:
            0 18px 45px rgba(0, 0, 0, 0.14);
    }

    .core-highlight-card:hover::before {
        transform: scale(1.35);

        opacity: 0.18;
    }


    /* =========================================================
   CCTV CARD
   ========================================================= */

    .cctv-highlight {
        border-top: 4px solid #e53935;
    }

    .cctv-highlight::before {
        background: #e53935;
    }

    .cctv-highlight::after {
        background: #ff9800;
    }


    /* =========================================================
   NETWORKING CARD
   ========================================================= */

    .networking-highlight {
        border-top: 4px solid #1565c0;
    }

    .networking-highlight::before {
        background: #1565c0;
    }

    .networking-highlight::after {
        background: #00acc1;
    }


    /* =========================================================
   ICON
   ========================================================= */

    .core-highlight-icon {
        width: 68px;
        height: 68px;

        display: flex;
        align-items: center;
        justify-content: center;

        border-radius: 16px;

        margin-bottom: 22px;

        font-size: 28px;

        color: #ffffff;

        box-shadow:
            0 8px 20px rgba(0, 0, 0, 0.15);

        transition:
            transform 0.35s ease,
            box-shadow 0.35s ease;
    }


    /* CCTV ICON */

    .cctv-highlight .core-highlight-icon {
        background: linear-gradient(135deg,
                #e53935,
                #ff7043);
    }


    /* NETWORKING ICON */

    .networking-highlight .core-highlight-icon {
        background: linear-gradient(135deg,
                #1565c0,
                #00acc1);
    }


    .core-highlight-card:hover .core-highlight-icon {
        transform: scale(1.08) rotate(-3deg);

        box-shadow:
            0 12px 28px rgba(0, 0, 0, 0.2);
    }


    /* =========================================================
   TITLE
   ========================================================= */

    .core-highlight-card h2 {
        margin: 0 0 7px;

        font-size: 28px;

        font-weight: 700;

        line-height: 1.2;

        color: #222;

        letter-spacing: -0.3px;
    }


    /* =========================================================
   SUBTITLE
   ========================================================= */

    .core-highlight-card>p {
        margin: 0 0 22px;

        font-size: 15px;

        font-weight: 500;

        color: #777;
    }


    /* =========================================================
   FEATURE LIST
   ========================================================= */

    .core-highlight-list {
        display: grid;

        grid-template-columns: repeat(2, minmax(0, 1fr));

        gap: 12px 18px;

        margin-bottom: 27px;
    }

    .core-highlight-list span {
        display: flex;

        align-items: center;

        gap: 9px;

        font-size: 14px;

        font-weight: 500;

        color: #444;
    }


    /* CHECK ICON */

    .core-highlight-list span i {
        width: 21px;
        height: 21px;

        display: inline-flex;

        align-items: center;
        justify-content: center;

        border-radius: 50%;

        font-size: 10px;

        color: #ffffff;

        flex-shrink: 0;
    }


    /* CCTV CHECK */

    .cctv-highlight .core-highlight-list span i {
        background: #e53935;
    }


    /* NETWORKING CHECK */

    .networking-highlight .core-highlight-list span i {
        background: #1565c0;
    }


    /* =========================================================
   BUTTON
   ========================================================= */

    .core-highlight-card>a {
        display: inline-flex;

        align-items: center;

        gap: 12px;

        padding: 12px 20px;

        border-radius: 8px;

        color: #ffffff;

        font-size: 14px;

        font-weight: 600;

        text-decoration: none;

        transition:
            transform 0.3s ease,
            box-shadow 0.3s ease,
            background 0.3s ease;
    }


    /* CCTV BUTTON */

    .cctv-highlight>a {
        background: linear-gradient(135deg,
                #e53935,
                #ff7043);

        box-shadow:
            0 6px 16px rgba(229, 57, 53, 0.25);
    }


    /* NETWORKING BUTTON */

    .networking-highlight>a {
        background: linear-gradient(135deg,
                #1565c0,
                #00acc1);

        box-shadow:
            0 6px 16px rgba(21, 101, 192, 0.25);
    }


    /* BUTTON HOVER */

    .core-highlight-card>a:hover {
        color: #ffffff;

        text-decoration: none;

        transform: translateX(4px);

        box-shadow:
            0 10px 24px rgba(0, 0, 0, 0.2);
    }


    /* ARROW */

    .core-highlight-card>a i {
        transition:
            transform 0.3s ease;
    }

    .core-highlight-card>a:hover i {
        transform: translateX(5px);
    }


    /* =========================================================
   MOBILE
   ========================================================= */

    @media (max-width: 767px) {

        .core-service-highlight {
            padding: 10px 0 35px;
        }

        .core-highlight-card {
            padding: 28px 24px;

            min-height: auto;

            border-radius: 15px;
        }

        .core-highlight-icon {
            width: 60px;
            height: 60px;

            font-size: 24px;

            margin-bottom: 18px;
        }

        .core-highlight-card h2 {
            font-size: 24px;
        }

        .core-highlight-card>p {
            font-size: 14px;
            margin-bottom: 20px;
        }

        .core-highlight-list {
            grid-template-columns: 1fr;

            gap: 10px;

            margin-bottom: 23px;
        }

        .core-highlight-list span {
            font-size: 13px;
        }

        .core-highlight-card>a {
            width: 100%;

            justify-content: center;

            padding: 12px 15px;
        }
    }


    /* =========================================================
   SMALL MOBILE
   ========================================================= */

    @media (max-width: 480px) {

        .core-highlight-card {
            padding: 24px 20px;
        }

        .core-highlight-card h2 {
            font-size: 22px;
        }

        .core-highlight-icon {
            width: 56px;
            height: 56px;

            font-size: 22px;
        }

    }
</style>