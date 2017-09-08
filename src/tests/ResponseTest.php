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

use ChatApplication\Server\Response;
use PHPUnit\Framework\TestCase;

class ResponseTest extends TestCase
{
    /** @test */
    public function it_can_encode_the_data_and_echo() {
        $payload = ['key' => 'value'];
        $response = new Response(200, $payload);
        $this->expectOutputString('{"key":"value"}');
        $response->send();
    }
}
