<?php
$pageConfig = [
    "title" => "Gym Traffic",
    "styles" => ["./gym-traffic.css"],
    "scripts" => ["./gym-traffic.js"],
    "titlebar" => [
        "title" => "Gym Traffic",
        "back_url" => "../",
    ],
    "need_auth" => true
];

$active_users = 72;
$traffic_measure = $active_users / 83 * 10;

$status_list = [
    "Gym is all yours",
    "Gym is buzzing",
    "Gym is packed",
];

$status = $traffic_measure > 0.66 * 10 ? $status_list[2] : ($traffic_measure > 0.33 * 10 ? $status_list[1] : $status_list[0]);

require_once "../includes/header.php";
require_once "../includes/titlebar.php";
?>

<script>
    const $TRAFFIC_MEASURE = <?= $traffic_measure ?>;
</script>

<main>
    <?php
    // $subnavbarConfig = [
    //     'links' => [
    //         [
    //             'title' => 'Live',
    //             'href' => './'
    //         ],
    //         [
    //             'title' => 'Past Week',
    //             'href' => './past-week'
    //         ]
    //     ],
    //     "active" => 1
    // ];

    // require_once "../includes/subnavbar.php"; 
    ?>
    <div class="meter">
        <div class="arrow"></div>
    </div>
    <span class="label">Live Traffic</span>
    <h2>0/10</h2>
    <div class="data">
        <h1><?= $status ?></h1>
        <div class="active-users">
            <div class="dot"></div>
            <span><?= $active_users ?> rat<?= $active_users === 1 ? " is" : "s are" ?> working out</span>
        </div>
        <p class="paragraph small">*The traffic values are estimates to give you a general idea and may not reflect exact conditions. Use them as a guide, and remember—you can crush your workout at any time!</p>
    </div>
</main>

<?php require_once "../includes/navbar.php" ?>
<?php require_once "../includes/footer.php" ?>