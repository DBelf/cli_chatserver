<?php
/**
 * cURL wrapper used to construct Client side requests to the server.
 * Each request is sent to the index.php page of the server.
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
     * @param $host_address string
     * @param $host_port string
     */
    public function __construct($host_address, $host_port) {
        $this->host_address = $host_address;
        $this->host_port = $host_port;
    }

    /**
     * Creates a GET request and uses PHP's cURL library to send the request to the given address and endpoint.
     *
     * @param $endpoint string the endpoint of the request that should be made.
     * @param array $payload the unencoded key,value array which can be added to the request, optional.
     * @return string $response the result json string of the result.
     */
    public function get_from_endpoint($endpoint, $payload = []) {
        $ch = curl_init();
        $json_url = urlencode(json_encode($payload));
        curl_setopt($ch, CURLOPT_URL,
            $this->host_address . ':' . $this->host_port . '/index.php' . $endpoint . '?json=' . $json_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 3);
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }

    /**
     * Creates a POST request and uses PHP's cURL library to send the request to the given address and endpoint.
     *
     * @param $endpoint string the endpoint of the request that should be made.
     * @param array $payload the unencoded key,value array which can be added to the request, optional.
     * @return string $response the result json string of the result.
     */
    public function post_to_endpoint($endpoint, $payload = []) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,
            $this->host_address . ':' . $this->host_port . '/index.php' . $endpoint);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }

    /**
     * Creates a PUT request and uses PHP's cURL library to send the request to the given address and endpoint.
     *
     * @param $endpoint string the endpoint of the request that should be made.
     * @param array $payload the unencoded key,value array which can be added to the request, optional.
     * @return string $response the result json string of the result.
     */
    public function put_on_endpoint($endpoint, $payload = []) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,
            $this->host_address . ':' . $this->host_port . '/index.php' . $endpoint);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }

    /**
     * Creates a DELETE request and uses PHP's cURL library to send the request to the given address and endpoint.
     *
     * @param $endpoint string the endpoint of the request that should be made.
     * @param array $payload the unencoded key,value array which can be added to the request, optional.
     * @return string $response the result json string of the result.
     */
    public function delete_from_endpoint($endpoint, $payload = []) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,
            $this->host_address . ':' . $this->host_port . '/index.php' . $endpoint);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }

}