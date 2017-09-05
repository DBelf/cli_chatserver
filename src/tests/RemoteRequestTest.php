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
use ChatApplication\Client\RemoteRequest;

class RemoteRequestTest extends TestCase
{
    protected $remote_request;

    protected function setUp() {
       $this->remote_request = new RemoteRequest(WEB_SERVER_HOST, WEB_SERVER_PORT);
    }
    
    /** @test */
    public function it_can_send_a_get_request_for_an_endpoint(){
        $endpoint = '/users';
        $response = $this->remote_request->get_from_endpoint($endpoint);
        $this->assertEquals($endpoint, $response);
    }
}