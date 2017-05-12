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
    <a style="display:block" href="/">
        <div id="logo-dataset"></div>
    </a>
    <div class="container">
        <div class="card">
            <header class="card-header">
                <div class="card-header-title">Search Builder</div>
            </header>
            <div class="card-content">
                <form action="dataset.php" method="POST">
                    <input type="hidden" name="type" value="advanced">
                    <section>
                        <div class="card">
                            <header class="card-header">
                                <p class="card-header-title">
                                    Query Terms
                                </p>
                            </header>
                            <div class="card-content">
                                <div id="builder" class="content">
                                    <div class="field is-horizontal part">
                                        <div class="field-body">
                                            <div class="field is-narrow">
                                                <div class="control">
                                                    <div class="select is-fullwidth">
                                                        <select name="op[]">
                                                            <option value="AND">AND</option>
                                                            <option value="OR" >OR</option>
                                                            <option value="NOT">NOT</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="field is-narrow">
                                                <div class="control">
                                                    <div class="select is-fullwidth">
                                                        <select name="field[]">
                                                            <option value="_all">All Fields</option>
                                                            <option value="title">Title</option>
                                                            <option value="notes">Description</option>
                                                            <option value="table_text">Table Text</option>
                                                            <option value="NERs">Named Entities</option>
                                                            <option value="Wikis">Related Wikis</option>
                                                            <option value="tags">Tags</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="field">
                                                <div class="control">
                                                    <input class="input" type="text" name="query[]">
                                                </div>
                                            </div>
                                            <div class="field has-addons is-narrow">
                                                <p class="control">
                                                    <a class="button minus-button" disabled>
                                                        <span class="icon is-small">
                                                            <i class="fa fa-minus"></i>
                                                        </span>
                                                    </a>
                                                </p>
                                                <p class="control">
                                                    <a class="button plus-button">
                                                        <span class="icon is-small">
                                                            <i class="fa fa-plus"></i>
                                                        </span>
                                                    </a>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                    <section>
                        <br>
                        <div class="card">
                            <header class="card-header">
                                <p class="card-header-title">
                                    Filter Settings
                                </p>
                            </header>
                            <div class="card-content">
                                <div class="content">
                                    Filters go here
                                </div>
                            </div>
                        </div>
                    </section>
                    <div class="field" style="margin-top:10px;">
                        <p class="control">
                        <button class="button is-<?php echo ($server_online) ? "info" : "danger"; ?>" <?php echo ($server_online) ? "" : "disabled"; ?>>Search</button>
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>

<script>

// Copy the blank query when the page loads
var defaultQuery = $(".part").first().clone();

function alterButtons() {
    $(".plus-button:not(:last)").each(function() {
        $(this).attr("disabled", true);
    });
    $(".plus-button:last").attr("disabled", false);

    if ($(".part").length == 1) {
        $(".minus-button").first().attr("disabled", true);
    } else {
        $(".minus-button").each(function() {
            $(this).attr("disabled", false);
        });
    }
}

$('body').on('click', '.plus-button', function() {
    defaultQuery.clone().appendTo("#builder");
    alterButtons();
});

$('body').on('click', '.minus-button', function() {
    $(this).closest(".part").remove();
    alterButtons();
});


</script>
<?php
include('footer.php');
?>
</body>
</html>
