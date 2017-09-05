<?php
/**
 * Short description for file
 *
 * Long description for file (if any)...
 *
 * @package    bunq_assignment
 * @author     Dimitri
 */

namespace ChatApplication\Client;


use function curl_close;

class RemoteRequest
{
    private $host_address;
    private $host_port;

    /**
     * RemoteRequest constructor.
     * @param $host_address
     * @param $host_port
     */
    public function __construct($host_address, $host_port) {
        $this->host_address = $host_address;
        $this->host_port = $host_port;
    }

    public function get_from_endpoint($endpoint, $data = array()) {
        $ch = curl_init();
        $json_url = urlencode(json_encode($data));
        curl_setopt($ch, CURLOPT_URL,
            $this->host_address . ':' . $this->host_port . '/index.php' . $endpoint . '?json=' . $json_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 3);
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }

}