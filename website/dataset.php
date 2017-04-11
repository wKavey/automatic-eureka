<?php
define('VIEWABLE', true);
include 'config.php';

function getQueryResults($query, $sort) {
    global $elasticsearch_server;
    global $default_index;

    $base_url = $elasticsearch_server . $default_index . "/_search";

    $params = array(
        'q' => $query,
        'sort' => $sort
    );

    $query_url = $base_url . "?" . http_build_query($params);

    return json_decode(file_get_contents($query_url));
}

$query = (isset($_GET["q"]) ? $_GET["q"] : "*");
$sort = (isset($_GET["sort"]) ? $_GET["sort"] : "_score: desc");

$results = getQueryResults($query, $sort);
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
        <div id="logo-dataset">
            <a href="" alt="Automatic Eureka"></a>
        </div>
        <form class="form-wrapper cf" action="dataset.php" method="get">
            <input type="text" placeholder="Search here..." name="q" value="<?php echo($query); ?>"  required>
            <input type="hidden" name="sort" value="_score:desc">
            <button type="submit">Search</button>
        </form>
    <div class="pure-g">
        <div id="main-grid" style="background-color:white;" class="pure-u-4-5">
<?php

if ($results->hits->total == 0) {
    print("No results found");
} else {
    foreach ($results->hits->hits as $hit) {
        $item = $hit->_source;

        echo "<p>";
        echo "score: $hit->_score </br>";
        echo "title: $item->title </br>";
        echo "notes: $item->notes </br>";
        echo "</p>";
    }
}

?>
        </div>
    </div>

</body>
</html>
