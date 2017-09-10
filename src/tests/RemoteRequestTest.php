<?php
/**
 * Tests the RemoteRequest. @see RemoteRequest
 *
 * A RemoteRequest should send a request a server and return the response.
 * RemoteRequests support functionality for all HTTP verbs (GET, POST, PUT and DELETE).
 * Used in combination with the index.php page which can be found in the resources folder.
 * The index.php page echoes the request details back.
 *
 * @package    bunq_assignment
 * @author     Dimitri
 */

namespace ChatApplication\tests;

require(__DIR__ . '/../../vendor/autoload.php');

use ChatApplication\Client\RemoteRequest;
use PHPUnit\Framework\TestCase;
use function explode;
use function json_encode;
use function urldecode;

class RemoteRequestTest extends TestCase
{
    protected $remote_request;

    protected function setUp() {
        $this->remote_request = new RemoteRequest(WEB_SERVER_HOST, WEB_SERVER_PORT);
    }

    /** @test */
    public function it_can_send_an_empty_get_request_to_an_endpoint() {
        //Arrange the parameters.
        $endpoint = '/messages';
        $payload = json_encode(array());
        //Send the request.
        $response = $this->remote_request->get_from_endpoint($endpoint);
        $data = explode('?json=', $response)[1];
        //Assert the response is as expected.
        $this->assertRegExp('/GET/', $response);
        $this->assertRegExp($endpoint . '/', $response);
        $this->assertEquals($payload, urldecode($data));
    }

    /** @test */
    public function it_can_send_a_json_get_request_to_an_endpoint() {
        //Arrange the parameters.
        $endpoint = '/users';
        $payload = json_encode(array('key' => 'value'));
        //Send the request.
        $response = $this->remote_request->get_from_endpoint($endpoint, $payload);
        $data = explode('?json=', $response)[1];
        $this->assertRegExp('/GET/', $response);
        $this->assertRegExp($endpoint . '/', $response);
        $this->assertEquals($payload, json_decode(urldecode($data), true));
    }

    /** @test */
    public function it_can_send_a_post_request_to_an_endpoint() {
        //Arrange the parameters.
        $endpoint = '/messages';
        $payload = json_encode(array('key' => 'value'));
        //Send the request.
        $response = $this->remote_request->post_to_endpoint($endpoint, $payload);
        $data = explode('/', $response)[3];
        //Assert the response is as expected.
        $this->assertRegExp('/POST/', $response);
        $this->assertRegExp($endpoint . '/', $response);
        $this->assertEquals($payload, json_decode($data, true));
    }

    /** @test */
    public function it_can_send_a_put_request_to_an_endpoint() {
        //Arrange the parameters.
        $endpoint = '/users';
        $payload = json_encode(array('key' => 'value'));
        //Send the request.
        $response = $this->remote_request->put_on_endpoint($endpoint, $payload);
        $data = explode('/', $response)[3];
        //Assert the response is as expected.
        $this->assertRegExp('/PUT/', $response);
        $this->assertRegExp($endpoint . '/', $response);
        $this->assertEquals($payload, json_decode($data, true));
    }

    /** @test */
    public function it_can_send_a_delete_request_to_an_endpoint() {
        //Arrange the parameters.
        $endpoint = '/unread';
        $payload = json_encode(array('key' => 'value'));
        //Send the request.
        $response = $this->remote_request->delete_from_endpoint($endpoint, $payload);
        $data = explode('/', $response)[3];
        //Assert the response is as expected.
        $this->assertRegExp('/DELETE/', $response);
        $this->assertRegExp($endpoint . '/', $response);
        $this->assertEquals($payload, json_decode($data, true));
    }
}