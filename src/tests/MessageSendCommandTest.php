<?php
/**
 * Short description for file
 *
 * Proper functionality has to be tested by using the dsn and port of the server api.
 *
 * @package    bunq_assignment
 * @author     Dimitri
 */

namespace ChatApplication\tests;

use ChatApplication\Client\ChatCommands\MessageSendCommand;
use ChatApplication\Client\RemoteRequest;
use PHPUnit\Framework\TestCase;
use const WEB_SERVER_HOST;

class MessageSendCommandTest extends TestCase
{
    protected $remote_request;

    protected function setUp() {
        $this->remote_request = new RemoteRequest(WEB_SERVER_HOST, 8002);
    }

    /** @test */
    public function it_can_send_messages() {
        $arguments = ['Carl', 'Hey!'];
        $message_send_command = new MessageSendCommand($this->remote_request, $arguments);
        $message_send_command->execute('Jane');
    }

}
