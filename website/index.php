<?php
define('VIEWABLE', true);
include 'config.php';

?>
<!doctype html>

<html>
<head>
    <meta charset="utf-8">
    <title>Automatic-Eureka</title>
    <script src="https://use.fontawesome.com/ae2e0830a7.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/purecss@0.6.2/build/pure-min.css" integrity="sha384-UQiGfs9ICog+LwheBSRCt1o5cbyKIHbwjWscjemyBMT9YCUMZffs6UqUTd0hObXD" crossorigin="anonymous">
    <link rel="stylesheet" href="main.css">
</head>

<body>
        <div id="logo"></div>
        <form class="form-wrapper cf" action="dataset.php" method="get">
            <input type="text" placeholder="Search here..." name="q" required>
            <input type="hidden" name="sort" value="_score:desc">
            <button type="submit">Search</button>
        </form>
        <div class="below-search">
<?php

$file = $elasticsearch_server;
$file_headers = @get_headers($file);
$server_online = False;

if ($file_headers && $file_headers[0] != 'HTTP/1.1 404 Not Found') {
    $server_online = True;
}
?>
            <!-- <p><a href="#" style="color: #d83c3c;" alt="">Advanced Search</a></p> -->
            <p><a class="pure-button <?php echo("button-" . ($server_online ? "success" : "error")); ?>" href="http://localhost:9200/" alt="">
                <i class="fa <?php echo(($server_online ? "fa-thumbs-up" : "fa-thumbs-down")); ?>"></i>
                <?php echo("Server " . ($server_online ? "Online" : "Offline")); ?>
            </a></p>
        </div>

</body>
</html>
