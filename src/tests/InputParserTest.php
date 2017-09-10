<?php
/**
 * Tests the InputParser.
 *
 * An InputParser should parse the commands from the input.
 * An InputParser should construct the corresponding ChatCommand if it exists.
 * An InputParser should constrcut the UnknownCommand if the command does not exist.
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
        //Arrange the input.
        $message_send_command = $this->input_parser->parse('message send username body');
        //Assert the arguments are set and the correct ChatCommand is constructed.
        $this->assertInstanceOf(MessageSendCommand::class, $message_send_command);
        $this->assertAttributeCount(2, 'arguments', $message_send_command);
        $this->assertAttributeContains('username', 'arguments', $message_send_command);
        $this->assertAttributeContains('body', 'arguments', $message_send_command);
    }

    /** @test */
    public function it_can_parse_user_get_commands() {
        //Arrange the input.
        $users_get_command = $this->input_parser->parse('users get');
        //Assert the corret ChatCommand is constructed.
        $this->assertInstanceOf(UsersGetCommand::class, $users_get_command);
    }

    /** @test */
    public function it_can_parse_user_update_commands() {
        //Arrange the input.
        $users_update_command = $this->input_parser->parse('users update new_name');
        //Assert the arguments are set and the correct ChatCommand is constructed.
        $this->assertInstanceOf(UsersUpdateCommand::class, $users_update_command);
        $this->assertAttributeContains('new_name', 'arguments', $users_update_command);
    }

    /** @test */
    public function it_returns_an_unknown_command_object_on_invalid_input() {
        //Arrange the input.
        $unknown_command = $this->input_parser->parse('unknown command data');
        //Assert the corret ChatCommand is constructed.
        $this->assertInstanceOf(UnknownCommand::class, $unknown_command);
    }
}
