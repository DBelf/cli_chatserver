<?php
/**
 * Used for testing RemoteRequests.
 *
 * This index is used by the server which is loaded by the PHPUnit bootstrap.
 * The index returns the request information to verify the correctness of the implementation of
 * the RemoteRequest.php class.
 *
 * @package    chat_server
 * @author     Dimitri
 */

/*
 * The GET request uses URL decoding to send JSON payloads so the response is different.
 */

function parse_request(){
    switch ($_SERVER['REQUEST_METHOD']){
        case 'GET':
            echo $_SERVER['REQUEST_METHOD'] . $_SERVER['REQUEST_URI'];
            break;
        default:
            $request_body = file_get_contents('php://input');
            echo $_SERVER['REQUEST_METHOD'] . $_SERVER['REQUEST_URI'] . '/' . $request_body;
            break;
    }
}

parse_request();
