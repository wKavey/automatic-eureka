<?php

include 'config.php';

function simpleQuery($query) {
    # Pull in the server URL and index from config.php
    global $elasticsearch_server;
    global $default_index;

    # Construct the search API endpoint URL
    $base_url = $elasticsearch_server . $default_index . "/_search";

    # Give it the user query
    $params = array(
        'q' => $query
    );

    # Build the resulting url with the parameters
    $query_url = $base_url . "?" . http_build_query($params);

    # Return the results as an a PHP array
    return json_decode(file_get_contents($query_url));
}

function advancedQuery($post_data) {
    # Pull in the server URL and index from config.php
    global $elasticsearch_server;
    global $default_index;

    $must = array();
    $should = array();
    $must_not = array();

    $query_array = array("query" => array("bool" => array()));

    # Loop over POST fields and segment into must, should, and must_not
    foreach ($post_data['query'] as $index=>$query) {
        if ($query !== '') {
            if ($post_data['op'][$index] == "AND") {
                $temp = array(array("match" => array($post_data['field'][$index] => $query)));
                $query_array["query"]["bool"]["must"][] = $temp;
            } else if ($post_data['op'][$index] == "OR") {
                $temp = array(array("match" => array($post_data['field'][$index] => $query)));
                $query_array["query"]["bool"]["should"][] = $temp;
            } else if ($post_data['op'][$index] == "NOT") {
                $temp = array(array("match" => array($post_data['field'][$index] => $query)));
                $query_array["query"]["bool"]["must_not"][] = $temp;
            }
        }
    }

    $data_string = json_encode($query_array);

    $base_url = $elasticsearch_server . $default_index . "/_search";

    $ch = curl_init($base_url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Content-Length: ' . strlen($data_string))
    );

    $result = curl_exec($ch);
    return json_decode($result);
}

?>
