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
    protected $_response_code;
    protected $_data = [];

    /**
     * Response constructor.
     * @param $response_code
     * @param $data
     */
    public function __construct($response_code, $data) {
        $this->_response_code = $response_code;
        //Should skip the first element to include real errors
        foreach($data as $element) {
            $this->_data[] = $element->to_array();
        }
    }

    public function send() {
        http_response_code($this->_response_code);
        echo _data;
    }
}