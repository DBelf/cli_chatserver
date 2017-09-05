<?php
/**
 * Used for testing RemoteRequests.
 *
 * This index is used by the server which is loaded by the PHPUnit bootstrap.
 * The index returns the request information to verify the correctness of the implementation of
 * the RemoteRequest.php class.
 *
 * @package    bunq_assignment
 * @author     Dimitri
 */

/*
 * The GET request uses URL decoding to send JSON payloads so the response is different.
 */
switch ($_SERVER['REQUEST_METHOD']){
    case 'GET':
        echo $_SERVER['REQUEST_METHOD'] . $_SERVER['REQUEST_URI'];
        break;
    default:
        $request_body = file_get_contents('php://input');
        echo $_SERVER['REQUEST_METHOD'] . $_SERVER['REQUEST_URI'] . '/' . $request_body;
        break;
}

