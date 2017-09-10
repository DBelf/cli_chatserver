<?php
/**
 * Tests the Response. @see Response
 *
 * Responses are wrappers for HTTP responses with status codes and a payload.
 * They return their payload as a string.
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
        //Arrange.
        $payload = ['key' => 'value'];
        $response = new Response(200, $payload);
        //Assert the response echoes its payload to stdout.
        $this->expectOutputString('{"key":"value"}');
        $response->send();
    }
}
