<?php
/**
 * Short description for file
 *
 * Long description for file (if any)...
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
        $res = $this->client->request('GET', 'index.php/users/');
        $this->assertEquals(200, $res->getStatusCode());
    }
    
    /** @test */
    public function it_calls_the_corresponding_method_if_the_endpoint_exists() {
        $res = $this->client->request('GET', 'index.php/users/');
        $this->assertEquals(200, $res->getStatusCode());
        $response_payload = json_decode($res->getBody()->getContents(), true);
        $this->assertInternalType('array', $response_payload);
        $this->assertTrue($response_payload['ok']);
        $this->assertArrayHasKey('users', $response_payload);
    }
    
    /** @test */
    public function it_can_send_a_404_response_on_unavailable_endpoints() {
        try {
            $this->client->request('GET', 'index.php/unavailable/');
        } catch (GuzzleHttp\Exception\ClientException $e) {
            $res = $e->getResponse();
            $this->assertEquals(404, $res->getStatusCode());
            $this->assertEquals('"Page Unavailable not found!"', (string) $res->getBody());
        }
    }
}