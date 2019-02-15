<?php
    header ('Content-Type: text/plain; charset=utf-8');
    include 'core/library/includes.php';
    ini_set('display_errors', 0);
    echo $config['nsfw_requests'];
?>