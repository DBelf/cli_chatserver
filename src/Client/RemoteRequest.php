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

    /**
     * Creates a GET request and uses PHP's curl library to send the request to the given address and endpoint.
     *
     * @param $endpoint string the endpoint of the request that should be made.
     * @param string $payload the string encoded json object which can be added to the request, optional.
     * @return string the result if the request was successful or a boolean false if the request was unsuccessful.
     */
    public function get_from_endpoint($endpoint, $payload = '[]') {
        $ch = curl_init();
        $json_url = urlencode($payload);
        curl_setopt($ch, CURLOPT_URL,
            $this->host_address . ':' . $this->host_port . '/index.php' . $endpoint . '?json=' . $json_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 3);
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }

    public function post_to_endpoint($endpoint, $payload = '[]') {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,
            $this->host_address . ':' . $this->host_port . '/index.php' . $endpoint);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }

    public function put_on_endpoint($endpoint, $payload = '[]') {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,
            $this->host_address . ':' . $this->host_port . '/index.php' . $endpoint);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }

    public function delete_from_endpoint($endpoint, $payload = '[]') {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,
            $this->host_address . ':' . $this->host_port . '/index.php' . $endpoint);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }

}