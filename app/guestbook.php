<?php

require_once __DIR__ . '/vendor/autoload.php';

// this references the "mongo" service
$messages = (new MongoDB\Client('mongodb://mongo'))->guestbook->messages;
$method = $_SERVER['REQUEST_METHOD'];

// load the guestbook html page here
require 'guestbook.html';

// if a message was posted insert it
if ('POST' === $method && isset($_POST['submit'])) {
    $args = array(
        'message' => array(
            'filter' => FILTER_SANITIZE_STRING
        )
    );
    $filter = filter_input_array(INPUT_POST, $args);

    try {
        $insertMessageResult = $messages->insertOne([
            'message' => $filter['message']
        ]);
    } catch (MongoDB\Driver\Exception\Exception $e) {
        echo 'MongoDB Exception: ' . $e->getMessage();
    }
}

// get all messages and display them
try {
    $cursor = $messages->find();
    
    echo '<div class="messages">';
    foreach ($cursor as $doc) {
        echo "<p>", $doc['message'], "</p>";
    }
    echo '</div>';
} catch (MongoDB\Driver\Exception\Exception $e) {
    echo 'MongoDB Exception: ' . $e->getMessage();
}
