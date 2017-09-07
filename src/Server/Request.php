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

use function explode;
use function file_get_contents;
use function json_encode;
use function preg_match;
use function print_r;
use function strpos;
use function strtolower;
use function urldecode;

class Request
{
    private $method;
    private $uri;
    private $payload;
    private $file_in = 'php://input';

    public function __construct($method, $uri) {
        $this->method = strtolower($method);
        $this->uri = $uri;
    }

    private function parse_endpoint_from_uri() {
        preg_match("/\/\w*.php\/(\w*)[\/| \?]?.*/", $this->uri, $matches);
        print_r($matches[1]."\n");
        return strtolower($matches[1]);
    }

    public function parse_payload() {
        if($this->method === 'get') {
            if (strpos($this->uri, '?json=') !== false) {
                $data = explode('?json=', $this->uri)[1];
                $this->payload = urldecode($data);
            } else {
                $this->payload = array();
            }
        } else {
            $this->payload = file_get_contents($this->file_in);
        }
    }

    public function get_payload() {
        return $this->payload;
    }

    public function get_method() {
        return $this->method;
    }

    public function get_endpoint() {
        return $this->parse_endpoint_from_uri();
    }
}