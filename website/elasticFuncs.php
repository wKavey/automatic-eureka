<?php

include 'config.php';

function simpleQuery($query, $sort) {
    # Pull in the server URL and index from config.php
    global $elasticsearch_server;
    global $default_index;

    # Construct the search API endpoint URL
    $base_url = $elasticsearch_server . $default_index . "/_search";

    # Give it the user query and the desired sorting method
    $params = array(
        'q' => $query,
        'sort' => $sort
    );

    # Build the resulting url with the parameters
    $query_url = $base_url . "?" . http_build_query($params);

    # Return the results as an a PHP array
    return json_decode(file_get_contents($query_url));
}

?>
