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
    private $data;

    /**
     * Response constructor.
     * @param $status_code
     * @param $data
     */
    public function __construct($status_code, $data = []) {
        $this->status_code = $status_code;
        $this->data = $data;
    }

    public function send() {
        http_response_code($this->status_code);
        echo json_encode($this->data, true);
    }
}