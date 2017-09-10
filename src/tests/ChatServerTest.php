<?php
/**
 * Tests the ChatServer.
 *
 * The ChatServer should reply with 200 status code on correct requests.
 * The ChatServer should construct the correct controller and call the corresponding method on a correct request.
 * The ChatServer sends a 404 response on incorrect requests.
 *
 * @package    bunq_assignment
 * @author     Dimitri
 */

namespace ChatApplication\tests;

use GuzzleHttp;
use PHPUnit\Framework\TestCase;
use function json_decode;

class ChatServerTest extends TestCase
{
    /**
     * @var GuzzleHttp\Client
     */
    protected $client;

    protected function setUp() {
        $http_base = ['base_uri' => 'http://localhost:8002/'];
        $this->client = new GuzzleHttp\Client($http_base);
    }

    /** @test */
    public function it_can_send_a_success_response_on_existing_endpoints() {
        //Arrange the request.
        $res = $this->client->request('GET', 'index.php/users/');
        //Assert a correct statuscode.
        $this->assertEquals(200, $res->getStatusCode());
    }
    
    /** @test */
    public function it_calls_the_corresponding_method_if_the_endpoint_exists() {
        //Arrange the request and response.
        $res = $this->client->request('GET', 'index.php/users/');
        $response_payload = json_decode($res->getBody()->getContents(), true);
        //Assert the statuscode is successful and if the response body is correct.
        $this->assertEquals(200, $res->getStatusCode());
        $this->assertInternalType('array', $response_payload);
        $this->assertTrue($response_payload['ok']);
        $this->assertArrayHasKey('users', $response_payload);
    }
    
    /** @test */
    public function it_can_send_a_404_response_on_unavailable_endpoints() {
        try {
            //Arrange the request.
            $this->client->request('GET', 'index.php/unavailable/');
        } catch (GuzzleHttp\Exception\ClientException $e) {
            //Assert the statuscode is 404 and the body is correct.
            $res = $e->getResponse();
            $this->assertEquals(404, $res->getStatusCode());
            $this->assertEquals('"Page Unavailable not found!"', (string) $res->getBody());
        }
    }
}