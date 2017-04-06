<!doctype html>

<html>
<head>
	<meta charset="utf-8">
	<title>Automatic-Eureka</title>
	<link rel="stylesheet" href="main.css">
	<link rel="stylesheet" href="https://unpkg.com/purecss@0.6.2/build/pure-min.css" integrity="sha384-UQiGfs9ICog+LwheBSRCt1o5cbyKIHbwjWscjemyBMT9YCUMZffs6UqUTd0hObXD" crossorigin="anonymous">
</head>

<body>
	<div id="logo"></div>
	<form class="form-wrapper cf" action="dataset.php" method="get">
		<input type="text" placeholder="Search here..." name="query" required>
		<input type="hidden" name="sort" value="score desc, name asc">
		<button type="submit">Search</button>
	</form>
	<div class="below-search">
		<p><a href="#" alt="">Advanced Search</a></p>
	</div>

</body>
</html>
