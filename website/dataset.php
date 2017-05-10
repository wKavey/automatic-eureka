<?php
define('VIEWABLE', true);
include 'utils.php';
include 'elasticFuncs.php';

$type = $query = $sort = Null;

// Both the simple query (index.php) and the advanced queries (advanced.php) come to this page
// We need to determine what kind of query and pass them on to either advancedQuery(...) or simpleQuery(...)
if (isset($_POST['type']) && $_POST['type'] == "simple") {
    $type = "simple";
} else if (isset($_POST['type']) && $_POST['type'] == "advanced") {
    $type = "advanced";
} else {
    $type = "simple";
}

$results = Null;

if ($type == "advanced") {
    // If advanced we need the entire POST array
    $results = advancedQuery($_POST);
} else {
    // If simple all we need is the query string
    $results = simpleQuery((isset($_POST["q"]) ? $_POST["q"] : "*"));
}
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
    <a style="display:block" href="/">
        <div id="logo-dataset"></div>
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
    <div class="container">
        <div class="card">
            <header class="card-header">
                <div class="card-header-title">Search Results</div>
            </header>
            <div class="card-content">
<?php

if ($results->hits->total == 0) {
    print("No results found");
} else {
    foreach ($results->hits->hits as $hit) {
        echo(resultToHTML($hit));
    }
}

?>
            </div>
        </div>
    </div>
    <div class="below_search">
        <p>
        </p>
    </div>

</body>
</html>
