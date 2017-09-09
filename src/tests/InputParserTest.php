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

use ChatApplication\Client\ChatCommands\MessageSendCommand;
use ChatApplication\Client\ChatCommands\UnknownCommand;
use ChatApplication\Client\ChatCommands\UsersGetCommand;
use ChatApplication\Client\ChatCommands\UsersUpdateCommand;
use ChatApplication\Client\InputParser;
use ChatApplication\Client\RemoteRequest;
use PHPUnit\Framework\TestCase;

class InputParserTest extends TestCase
{
    /**
     * @var InputParser
     */
    protected $input_parser;
    protected $remote_request;

    protected function setUp() {
        $this->remote_request = new RemoteRequest('localhost', 8002);
        $this->input_parser = new InputParser($this->remote_request);
    }

    /** @test */
    public function it_can_parse_message_send_commands() {
        $message_send_command = $this->input_parser->parse('message send username body');
        $this->assertInstanceOf(MessageSendCommand::class, $message_send_command);
        $this->assertAttributeContains('username', 'arguments', $message_send_command);
        $this->assertAttributeContains('body', 'arguments', $message_send_command);
    }

    /** @test */
    public function it_can_parse_user_get_commands() {
        $users_get_command = $this->input_parser->parse('users get');
        $this->assertInstanceOf(UsersGetCommand::class, $users_get_command);
    }

    /** @test */
    public function it_can_parse_user_update_commands() {
        $users_update_command = $this->input_parser->parse('users update new_name');
        $this->assertInstanceOf(UsersUpdateCommand::class, $users_update_command);
        $this->assertAttributeContains('new_name', 'arguments', $users_update_command);
    }

    /** @test */
    public function it_returns_an_unknown_command_object_on_invalid_input() {
        $unknown_command = $this->input_parser->parse('unknown command data');
        $this->assertInstanceOf(UnknownCommand::class, $unknown_command);
    }
}
