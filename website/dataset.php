<?php
define('VIEWABLE', true);
include 'utils.php';
include 'elasticFuncs.php';

$type = $query = $sort = Null;

if (isset($_POST['type']) && $_POST['type'] == "simple") {
    $type = "simple";
    $query = (isset($_POST["q"]) ? $_POST["q"] : "*");
} else if (isset($_POST['type']) && $_POST['type'] == "advanced") {
    $type = "advanced";
    print_r($_POST);
} else {
    $type = "simple";
    $query = "*";
}

$results = Null;

if ($type == "advanced") {
    $results = advancedQuery($_POST);
} else {
    $results = simpleQuery($query);
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
    <form class="form-wrapper cf" action="dataset.php" method="get">
        <div class="field has-addons has-addons-centered" style="margin-bottom:20px;">
            <p class="control">
                <input class="input is-large" type="text" placeholder="Query" name="q" required>
            </p>
            <p class="control">
                <button type="submit" class="button is-info is-large">Search</button>
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
