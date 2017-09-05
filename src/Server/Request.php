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

use function json_decode;

class Request
{
    private $url_elements;
    private $method;
    private $payload;

    public function __construct($url, $method, $payload) {
        $this->url_elements = explode('/', $url);
        $this->method = $method;
        $this->payload = json_decode($payload, true);
    }


}