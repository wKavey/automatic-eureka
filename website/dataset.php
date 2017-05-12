<?php
define('VIEWABLE', true);
include 'utils.php';
include 'elasticFuncs.php';

$type = Null;
$title = "";

// Both the simple query (index.php) and the advanced queries (advanced.php) come to this page
// We need to determine what kind of query and pass them on to either advancedQuery(...) or simpleQuery(...)
if (isset($_POST['type']) && $_POST['type'] == "simple") {
    $type = "simple";
    $title = $_POST['q'];
} else if (isset($_POST['type']) && $_POST['type'] == "advanced") {
    $type = "advanced";
    $title = "Advanced";
} else {
    // If no query redirect user to the homepage
    header('Location: /');
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
    <title><?php echo($title); ?> - Automatic-Eureka</title>
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
    <a style="display:block" href="/">
        <div id="logo-dataset"></div>
    </a>
    <form action="dataset.php" method="POST">
        <div class="field has-addons has-addons-centered" style="margin-bottom:20px;">
            <p class="control">
                <input class="input is-large" type="text" onfocus="this.selectionStart = this.selectionEnd = this.value.length;" value="<?php echo((isset($_POST["q"])) ? $_POST["q"] : "");?>" placeholder="<?php echo ($server_online) ? "Search..." : "Server Offline";?>" name="q" required <?php echo ($server_online) ? "autofocus" : "disabled";?>>
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
                <div class="card-header-title">Search Results <small style="margin-left:20px;"><?php echo('(' . $results->took*0.001 . " seconds)"); ?></small></div>
            </header>
            <div id="results" class="card-content">
<?php

if ($results->hits->total == 0) {
    print("No results found");
}

?>
            </div>
        </div>
    </div>
    <div class="below_search">
        <p>
        </p>
    </div>
<script>

<?php
print("var results = " . json_encode($results) . ';');
?>

var max_results = 10;

// return an overall score for an array of hits
function agg_score(hits) {
	var score = hits[0]._score;
	for (var i = 0; i < hits.length; ++i) {
		if (hits[i]._score > score) score = hits[i]._score;
	}
	return score;
}

function escquote(str) {
	return str.replace(/"/g, "&quot;").replace(/(<([^>]+)>)/ig, "").replace(/\s+/g, ' ');
}

// turn a CSV filename into something more presentable
function pretty_name(name) {
	var pattern = /[-_]+/g;
	if (name.match(/\s/)) pattern = /\s+/g;
	return name.replace(pattern, '&nbsp;').replace(/\.CSV$/i, '');
}

// convert a scored array of hits into an HTML result
function agg2html(score, hits) {
	var s = '<article class="media"><div class="media-content"><div class="content"><p>';

	s += '<strong>' + hits[0]._source.title + '</strong>';
        s += ' <small>' + score + '</small></p><p class="resources">';

        var description = hits[0]._source.notes.substring(0, 250);
        if (hits[0]._source.notes.length > 250)
            description += "...";

        s += description + '\n' + "<br>";
	for (var i = 0; i < Math.min(10, hits.length); ++i) {
		category = Math.min(Math.floor(hits[i]._score / results.hits.max_score * 5 + 1), 5);


		s += ' <a class="csv rel' + category +
			'" href="' + hits[i]._source.resource.url +
			'" title="' + escquote(hits[i]._source.resource.description) + '">' +
			pretty_name(hits[i]._source.resource.name) +
		'</a>';
	}

	s += '</p></div></article>';
	return s;
}

document.addEventListener('DOMContentLoaded', function() {
	var aggregated = {};

	// group hits by parent id
	for (var i = 0; i < results.hits.hits.length; ++i) {
		var pid = results.hits.hits[i]._source.dataset.id;
		if (!(pid in aggregated)) aggregated[pid] = [];
		aggregated[pid].push(results.hits.hits[i]);
	}

	// add score to each group and sort by score
	var new_results = Object.values(aggregated).map(function(agg) {
		return [agg_score(agg), agg];
	}).sort(function(a, b) {
		return b[0] - a[0];
	});

	// append to HTML
	var resdiv = document.getElementById('results');
	for (var i = 0; i < new_results.length; ++i) {
		resdiv.insertAdjacentHTML('beforeend', agg2html(new_results[i][0], new_results[i][1]));
	}
});
</script>
<?php
include('footer.php');
?>
</body>
</html>
