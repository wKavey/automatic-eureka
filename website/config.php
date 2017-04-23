<?php

# Prevent someone from loading this as a standalone web page
if (!defined('VIEWABLE')) {
    header('Location: /');
}

$elasticsearch_server = "http://localhost:9200";
$default_index = "/automatic-eureka/dataset"


?>
