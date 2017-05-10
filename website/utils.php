<?php
include 'config.php';

/**
 * Checks to see if the server is online, used by the global variable $server_online
 *
 * @return boolean value, True if server is online, false otherwise
 */
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

/**
 * Converts a result row from ElasticSearch to the proper HTML needed for datasets.php
 *
 * @param JSON $d Dataset to be converted to HTML
 *
 * @return html string to be inserted into the page
 */
function resultToHTML($d) {
    $html_string = "";

    $html_string .= '<article class="media"><div class="media-content"><div class="content"><p>';
    $html_string .= '<strong>' . $d->_source->title . '</strong> <small>' . $d->_score . '</small>';
    $html_string .= '<br>' . $d->_source->notes;
    $html_string .= '</p></div></div></article>';

    return $html_string;
}


?>
