<?php
/**
 * Tests the MessagesSendCommand. @see MessageSendCommand
 *
 * A MessageSendCommand can send a post request to the server.
 * A MessageSendCommand can display errors if anything went wrong.
 *
 * @package    chat_server
 * @author     Dimitri
 */

namespace ChatApplication\tests;

use ChatApplication\Client\ChatCommands\MessageSendCommand;
use ChatApplication\Client\RemoteRequest;
use ChatApplication\Server\DatabaseService\SQLiteDatabase;
use PHPUnit\Framework\TestCase;
use const WEB_SERVER_HOST;

class MessageSendCommandTest extends TestCase
{
    protected $remote_request;

    public static function setUpBeforeClass() {
        //Adds two users to the database for testing.
        $dbh = new SQLiteDatabase(__DIR__ . '/../../database/chat_server.db');
        $dbh->query('INSERT INTO Users (username) VALUES(:username)', ['username' => 'Bob']);
        $dbh->query('INSERT INTO Users (username) VALUES(:username)', ['username' => 'Jill']);
        $dbh = null;
    }

    protected function setUp() {
        $this->remote_request = new RemoteRequest(WEB_SERVER_HOST, 8002);
    }

    /** @test */
    public function it_can_send_messages() {
        //Arrange.
        $arguments = ['Bob', 'Sup!'];
        $message_send_command = new MessageSendCommand($this->remote_request, $arguments);
        //Assert the server sent a success response.
        $this->assertTrue($message_send_command->execute('Jill'));
    }

    /** @test */
    public function it_displays_a_message_if_incorrect_number_of_arguments_provided() {
        //Arrange.
        $arguments = ['Bob'];
        $message_send_command = new MessageSendCommand($this->remote_request, $arguments);
        //Assert the server sent a fail response and display the corresponding error.
        $this->assertFalse($message_send_command->execute('Jill'));
        $this->expectOutputRegex('/Need a username and body to send a message!/');
    }

    /** @test */
    public function it_displays_an_error_if_sending_fails() {
        //Arrange.
        $arguments = ['Unknown', 'Sup!'];
        $message_send_command = new MessageSendCommand($this->remote_request, $arguments);
        //Assert the server sent a fail response and display the corresponding error.
        $this->assertFalse($message_send_command->execute('Jill'));
        $this->expectOutputRegex('/No such user!/');
    }

    //Cleans the to ensure predictable tests.
    public static function tearDownAfterClass() {
        $dbh = new SQLiteDatabase(__DIR__ . '/../../database/chat_server.db');
        $dbh->query('DROP TABLE Users', []);
        $dbh->query('DROP TABLE Messages', []);
        $dbh->query('DROP TABLE Unread', []);
        $dbh = null;
    }

}
