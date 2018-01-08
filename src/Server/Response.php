<?php
/**
 * A Response wrapper.
 *
 * The server uses the Response wrapper to set the HTTP status of the response and
 * echo the data encoded in a JSON string back to the client.
 *
 * @package    chat_server
 * @author     Dimitri
 */

namespace ChatApplication\Server;

use function http_response_code;
use function json_encode;

class Response
{
    /**
     * @var integer
     */
    private $status_code;
    /**
     * @var array
     */
    private $payload;

    /**
     * Response constructor.
     * @param $status_code integer with the HTTP status code.
     * @param $payload array of the key, value pairs of the response data.
     */
    public function __construct($status_code, $payload = []) {
        $this->status_code = $status_code;
        $this->payload = $payload;
    }

    /**
     * Echoes the HTTP status code along with the data of the response converted to JSON.
     * This will be received by the client.
     */
    public function send() {
        http_response_code($this->status_code);
        echo json_encode($this->payload, true);
    }
}