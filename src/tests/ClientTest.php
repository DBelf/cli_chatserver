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

require(__DIR__ . '/../../vendor/autoload.php');

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Exception\RequestException;

class ClientTest extends TestCase
{
    protected $client;
    protected $mock;
    protected $handler;

    protected function setUp() {
        $this->mock = new MockHandler([
            new Response(200,[], json_encode('{\'name\':\'test\'}'))
        ]);
        $this->handler = HandlerStack::create($this->mock);
        $this->client = new Client(['handler' => $this->handler]);
    }

    /** @test */
    public function it_should_display_multiple_other_users(){
        $response = $this->client->request('GET', '/users');

        $this->assertEquals(200, $response->getStatusCode());
    }
}