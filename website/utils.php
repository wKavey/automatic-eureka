<?php
include 'config.php';

# Simple function that determines if the elasticserach server is running
# Queries the URL provided in config.php
function isServerOnline() {
    global $elasticsearch_server;

    $headers = @get_headers($elasticsearch_server);

    if ($headers && $headers[0] != 'HTTP/1.1 404 Not Found') {
        return true;
    } else {
        return false;
    }
}

// Set the variable so we can use it in other pages
$server_online = isServerOnline();


# Accepts a result dataset and returns the proper HTML for displaying it
function datasetHTML($d) {
    $html_string = "";

    $html_string .= '<article class="media"><div class="media-content"><div class="content"><p>';
    $html_string .= '<strong>' . $d->_source->title . '</strong> <small>' . $d->_score . '</small>';
    $html_string .= '<br>' . $d->_source->notes;
    $html_string .= '</p></div></div></article>';

    return $html_string;
}


?>
