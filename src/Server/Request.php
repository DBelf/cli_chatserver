<?php
/**
 * A wrapper for the information contained in a request sent from a client to the server.
 *
 * The server uses a request to load the Controller and add the request payload to the method invoked on the controller.
 *
 * @package    chat_server
 * @author     Dimitri
 */

namespace ChatApplication\Server;

use function explode;
use function file_get_contents;
use function preg_match;
use function strpos;
use function strtolower;
use function urldecode;

class Request
{
    /**
     * @var string
     */
    private $verb;
    /**
     * @var string
     */
    private $uri;
    /**
     * @var array
     */
    private $payload;
    /**
     * @var string
     */
    private $file_in = 'php://input';

    /**
     * Request constructor.
     * @param $method
     * @param $uri
     */
    public function __construct($method, $uri) {
        $this->verb = strtolower($method);
        $this->uri = $uri;
    }

    /**
     * Uses a regular expression to parse the endpoint from the URI.
     * Inserting the URI index.php/users/additional/path/info
     * will result in the endpoint Users.
     *
     * @return string
     */
    private function parse_endpoint_from_uri() {
        preg_match('/\/\w*.php\/(\w*)[\/| \?]?.*/', $this->uri, $matches);
        return ucwords(strtolower($matches[1]));
    }

    /**
     * Gets the payload from the request sent to the server.
     */
    public function parse_payload() {
        if ($this->verb === 'get') {
            //Payload of a get request is encoded into the URL.
            if (strpos($this->uri, '?json=') !== false) {
                $data = explode('?json=', $this->uri)[1];
                $this->payload = urldecode($data);
            } else {
                $this->payload = json_encode(array());
            }
        } else {
            //Payload of other verbs can be read from php://input
            $this->payload = file_get_contents($this->file_in);
        }
    }

    /**
     * Returns the payload of the request.
     * @return array
     */
    public function get_payload() {
        return json_decode($this->payload, true);
    }

    /**
     * Returns the verb of the request.
     * @return string
     */
    public function get_verb() {
        return $this->verb;
    }

    /**
     * Parses the endpoint from the request URI and returns this.
     * Uses parse_endpoint_from_uri to parse.
     * @see Request::parse_endpoint_from_uri()
     *
     * @return string
     */
    public function get_endpoint() {
        return $this->parse_endpoint_from_uri();
    }
}