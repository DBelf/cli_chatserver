<?php
/**
 * Short description for file
 *
 * Long description for file (if any)...
 *
 * @package    bunq_assignment
 * @author     Dimitri
 */

namespace ChatApplication\Server;


use function http_response_code;

class Response
{
    protected $response_code;
    protected $data = [];

    /**
     * Response constructor.
     * @param $response_code
     * @param $data
     */
    public function __construct($response_code, $data) {
        $this->response_code = $response_code;
        $this->data = $data;

    }

    public function send() {
        http_response_code($this->response_code);
        echo $this->data;
    }
}