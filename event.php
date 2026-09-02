<?php

require_once __DIR__ . '/includes/db.php';

$pageTitle = 'Events | CORE LTD';


/*
|--------------------------------------------------------------------------
| GET ACTIVE EVENTS
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare("
    SELECT
        id,
        title,
        slug,
        event_date,
        event_time,
        venue,
        description,
        image
    FROM events
    WHERE status = 1
    ORDER BY
        event_date DESC,
        created_at DESC
");

$stmt->execute();

$result = $stmt->get_result();

$events = [];

while ($row = $result->fetch_assoc()) {

    $events[] = $row;
}

$stmt->close();

include __DIR__ . '/header.php';

?>

<title><?= htmlspecialchars($pageTitle); ?></title>


<style>
.core-events-page {
    padding: 60px 0;
    background: #f7f9fc;
}

.core-events-heading {
    text-align: center;
    margin-bottom: 45px;
}

.core-events-heading span {
    display: inline-block;
    color: #0d6efd;
    font-size: 13px;
    font-weight: 800;
    letter-spacing: 2px;
    margin-bottom: 10px;
}

.core-events-heading h1 {
    font-size: 38px;
    font-weight: 800;
    margin: 0 0 10px;
}

.core-events-heading p {
    color: #64748b;
    margin: 0;
}

.core-event-card {
    background: #fff;
    border-radius: 15px;
    overflow: hidden;
    height: 100%;
    box-shadow: 0 8px 30px rgba(15, 23, 42, .07);
    transition: .3s ease;
}

.core-event-card:hover {
    transform: translateY(-7px);
    box-shadow: 0 15px 40px rgba(15, 23, 42, .13);
}

.core-event-image {
    position: relative;
    height: 240px;
    overflow: hidden;
    background: #e2e8f0;
}

.core-event-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: .4s ease;
}

.core-event-card:hover .core-event-image img {
    transform: scale(1.05);
}

.core-event-date {
    position: absolute;
    top: 15px;
    left: 15px;
    background: #0d6efd;
    color: #fff;
    border-radius: 10px;
    padding: 9px 13px;
    text-align: center;
    font-weight: 800;
}

.core-event-date strong {
    display: block;
    font-size: 21px;
    line-height: 1;
}

.core-event-date small {
    font-size: 11px;
    text-transform: uppercase;
}

.core-event-content {
    padding: 22px;
}

.core-event-content h2 {
    font-size: 21px;
    line-height: 1.3;
    margin: 0 0 12px;
}

.core-event-content h2 a {
    color: #111827;
    text-decoration: none;
}

.core-event-meta {
    display: flex;
    flex-direction: column;
    gap: 7px;
    margin-bottom: 15px;
    color: #64748b;
    font-size: 14px;
}

.core-event-meta i {
    width: 18px;
    color: #0d6efd;
}

.core-event-description {
    color: #64748b;
    font-size: 14px;
    line-height: 1.6;
    margin-bottom: 18px;
}

.core-event-button {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: #111827;
    color: #fff;
    padding: 11px 17px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 700;
    font-size: 13px;
}

.core-event-button:hover {
    background: #0d6efd;
    color: #fff;
}

.core-no-events {
    text-align: center;
    padding: 80px 20px;
}

.core-no-events i {
    font-size: 50px;
    color: #cbd5e1;
    margin-bottom: 20px;
}
</style>


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
                    Events
                </li>

            </ul>

        </div>

    </div>

</div>


<section class="core-events-page">

    <div class="container-fluid">

        <div class="core-events-heading">

            <span>
                CORE LTD
            </span>

            <h1>
                Events
            </h1>

            <p>
                Stay updated with our latest events, training and activities.
            </p>

        </div>


        <?php if (!empty($events)): ?>

        <div class="row">

            <?php foreach ($events as $event): ?>

            <div class="col-lg-4 col-md-6 mb-30">

                <div class="core-event-card">


                    <div class="core-event-image">

                        <?php if (!empty($event['image'])): ?>

                        <img src="<?= htmlspecialchars($event['image']); ?>"
                            alt="<?= htmlspecialchars($event['title']); ?>">

                        <?php else: ?>

                        <div style="
                                        width:100%;
                                        height:100%;
                                        display:flex;
                                        align-items:center;
                                        justify-content:center;
                                    ">

                            <i class="fa fa-calendar" style="font-size:50px;color:#cbd5e1;"></i>

                        </div>

                        <?php endif; ?>


                        <?php if (!empty($event['event_date'])): ?>

                        <div class="core-event-date">

                            <strong>
                                <?= date(
                                                'd',
                                                strtotime($event['event_date'])
                                            ); ?>
                            </strong>

                            <small>
                                <?= date(
                                                'M',
                                                strtotime($event['event_date'])
                                            ); ?>
                            </small>

                        </div>

                        <?php endif; ?>

                    </div>


                    <div class="core-event-content">

                        <h2>

                            <a href="event.php?slug=<?= urlencode($event['slug']); ?>">

                                <?= htmlspecialchars($event['title']); ?>

                            </a>

                        </h2>


                        <div class="core-event-meta">

                            <?php if (!empty($event['event_date'])): ?>

                            <span>

                                <i class="fa fa-calendar"></i>

                                <?= date(
                                                'd M Y',
                                                strtotime($event['event_date'])
                                            ); ?>

                            </span>

                            <?php endif; ?>


                            <?php if (!empty($event['event_time'])): ?>

                            <span>

                                <i class="fa fa-clock"></i>

                                <?= htmlspecialchars($event['event_time']); ?>

                            </span>

                            <?php endif; ?>


                            <?php if (!empty($event['venue'])): ?>

                            <span>

                                <i class="fa fa-location-dot"></i>

                                <?= htmlspecialchars($event['venue']); ?>

                            </span>

                            <?php endif; ?>

                        </div>


                        <?php if (!empty($event['description'])): ?>

                        <div class="core-event-description">

                            <?= htmlspecialchars(
                                            mb_strimwidth(
                                                $event['description'],
                                                0,
                                                150,
                                                '...'
                                            )
                                        ); ?>

                        </div>

                        <?php endif; ?>


                        <a href="event.php?slug=<?= urlencode($event['slug']); ?>" class="core-event-button">

                            View Event

                            <i class="fa fa-arrow-right"></i>

                        </a>

                    </div>

                </div>

            </div>

            <?php endforeach; ?>

        </div>

        <?php else: ?>

        <div class="core-no-events">

            <i class="fa fa-calendar-xmark"></i>

            <h2>
                No Events Available
            </h2>

            <p>
                Check back soon for upcoming CORE LTD events.
            </p>

        </div>

        <?php endif; ?>

    </div>

</section>


<?php

include __DIR__ . '/footer.php';

?>