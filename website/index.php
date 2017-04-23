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
    <div class="columns">
        <div class="column is-half is-offset-one-quarter">
            <div id="logo"></div>
            <form action="dataset.php" method="get">
                <div class="field has-addons has-addons-centered">
                    <p class="control">
                        <input class="input is-large" type="text" placeholder="Query" name="q" required>
                    </p>
                    <input type="hidden" name="sort" value="_score:desc">
                    <p class="control">
                        <button type="submit" class="button is-info is-large">Search</button>
                    </p>
                </div>
            </form>
            <div class="block" style="margin-top: 20px; text-align: center">
<?php
if (isServerOnline()) {
    echo '<a href="'. $elasticsearch_server . '" class="button is-success">
        <span>Server is Online&nbsp;&nbsp;</span>
        <span class="icon">
            <i class="fa fa-thumbs-up"></i>
        </span>
    </a>';
} else {
    echo '<a href="'. $elasticsearch_server . '" class="button is-danger">
        <span>Server is Offline&nbsp;&nbsp;</span>
        <span class="icon">
            <i class="fa fa-thumbs-down"></i>
        </span>
    </a>';
}
?>
            </div>
        </div>
    </div>
</body>
</html>
