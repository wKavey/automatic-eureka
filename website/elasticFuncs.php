<?php

include 'config.php';

/**
 * Returns results from the ElasticSearch server for a simple query
 *
 * @param string $query The query string
 *
 * @return the results list encoded in JSON
 */
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

/**
 * Returns results from the ElasticSearch server for an advanced query
 * This function parses the POST data to separate parts of the query and builds the
 * necessary ElasticSearch JSON object
 *
 * @param array $post_data takes an array containing POST data passed to dataset.php
 *                         this array should three lists containing operations (AND, OR, NOT),
 *                         field names, and query strings
 */

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
        if ($query !== '') { # Only construct queries from non-blank query strings
            if ($post_data['op'][$index] == "AND") {
                $temp = array(
                    array(
                        "match" => array(
                            $post_data['field'][$index] => $query
                        )
                    )
                );
                $query_array["query"]["bool"]["must"][] = $temp;
            } else if ($post_data['op'][$index] == "OR") {
                $temp = array(
                    array(
                        "match" => array(
                            $post_data['field'][$index] => $query
                        )
                    )
                );
                $query_array["query"]["bool"]["should"][] = $temp;
            } else if ($post_data['op'][$index] == "NOT") {
                $temp = array(
                    array(
                        "match" => array(
                            $post_data['field'][$index] => $query
                        )
                    )
                );
                $query_array["query"]["bool"]["must_not"][] = $temp;
            }
        }
    }

    # Encode the JSON array we will send to ElasticSearch
    $data_string = json_encode($query_array);

    $query_url = $elasticsearch_server . $default_index . "/_search";
    
    # Construct the curl object and set necessary parameters
    $ch = curl_init($query_url);
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
