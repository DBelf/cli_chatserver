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

use ChatApplication\Server\Request;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use function json_decode;

class RequestTest extends TestCase
{
    /** @test */
    public function it_can_parse_the_endpoint_from_the_uri() {
        $request = new Request('GET', '/index.php/unread/rest');
        $request->parse_payload();
        $this->assertEquals('Unread', $request->get_endpoint());
    }

    /** @test */
    public function it_can_read_the_payload_from_the_uri_if_verb_is_get() {
        $request = new Request('GET', '/index.php/unread?json=%7B%22key%22%3A%22value%22%7D');
        $request->parse_payload();
        $this->assertEquals('Unread', $request->get_endpoint());
        $this->assertEquals(json_decode('{"key":"value"}', true), $request->get_payload());
    }

    /** @test */
    public function it_can_read_the_payload_from_the_request_body_for_other_verbs() {
        $request = new Request('POST', '/index.php/unread');
        $reflection = new ReflectionClass($request);
        $file_in = $reflection->getProperty('file_in');
        $file_in->setAccessible(true);
        $file_in->setValue($request, __DIR__ . '/resources/test.json');
        $request->parse_payload();
        $this->assertEquals('Unread', $request->get_endpoint());
        $this->assertEquals(json_decode('{"key":"value"}', true), $request->get_payload());
    }
}
