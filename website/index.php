<?php
define('VIEWABLE', true);
include 'utils.php';

?>
<!doctype html>

<html>
<head>
    <meta charset="utf-8">
    <title>Automatic-Eureka</title>
    <script
        src="https://code.jquery.com/jquery-3.2.1.min.js"
        integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4="
        crossorigin="anonymous"></script>
    <link
        rel="stylesheet"
        type="text/css"
        href="https://cdnjs.cloudflare.com/ajax/libs/bulma/0.4.0/css/bulma.css">
    <link
        rel="stylesheet"
        href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="main.css">

</head>

<body>
<?php include_once("analyticstracking.php") ?>
    <div class="columns">
        <div class="column is-half is-offset-one-quarter has-text-centered">
            <a style="display:block" href="/">
                <div id="logo"></div>
            </a>
            <form action="dataset.php" method="POST">
                <div class="field has-addons has-addons-centered" style="margin-bottom:20px;">
                    <p class="control">
                        <input class="input is-large" type="text" placeholder="<?php echo ($server_online) ? "Search..." : "Server Offline";?>" name="q" required <?php echo ($server_online) ? "autofocus" : "disabled";?>>
                    </p>
                    <input type="hidden" name="type" value="simple">
                    <p class="control">
                        <button type="submit" class="button is-<?php echo ($server_online) ? "info" : "danger";?> is-large" <?php echo ($server_online) ? "" : "disabled";?>>Search</button>
                    </p>
                </div>
            </form>
            <a href="advanced.php" class="button is-link" >Advanced Search</a>
        </div>
    </div>
</body>
</html>
