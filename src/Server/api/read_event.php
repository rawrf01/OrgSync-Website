<?php
// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=UTF-8');

// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

function debug($message) {
    error_log(date('Y-m-d H:i:s') . " - $message\n", 3, 'debug.log');
}

try {
    require_once(dirname(__FILE__) . '/../core/initialize.php');
    debug('API initialized');

    if (!$db) {
        throw new PDOException('Database connection failed');
    }

    // Configure PDO to throw exceptions and return associative arrays
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    
    debug('Database connection successful');

    // Instantiate the EventHandler class
    $eventHandler = new EventHandler($db);

    // Fetch events from the database and encode the banner image
    $events = $eventHandler->read();

    // Check if any events were found
    if ($events) {
        $response = array(
            'status' => 'success',
            'count' => count($events),
            'data' => $events
        );
    } else {
        $response = array(
            'status' => 'success',
            'count' => 0,
            'data' => array(),
            'message' => 'No records found'
        );
    }

    debug('Final response: ' . json_encode($response, JSON_PRETTY_PRINT));
    echo json_encode($response);

} catch (PDOException $e) {
    debug('Database error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(array(
        'status' => 'error',
        'message' => 'Database error: ' . $e->getMessage()
    ));
} catch (Exception $e) {
    debug('General error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(array(
        'status' => 'error',
        'message' => 'General error: ' . $e->getMessage()
    ));
}

?>