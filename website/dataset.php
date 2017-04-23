<?php
define('VIEWABLE', true);
include 'utils.php';
include 'elasticFuncs.php';

# Get search parameters if they exist - also set some default values
$query = (isset($_GET["q"]) ? $_GET["q"] : "*");
$sort = (isset($_GET["sort"]) ? $_GET["sort"] : "_score: desc");

$results = simpleQuery($query, $sort);
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
    <div id="logo-dataset">
        <a href="" alt="Automatic Eureka"></a>
    </div>
        <form class="form-wrapper cf" action="dataset.php" method="get">
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
        echo(datasetHTML($hit));
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
