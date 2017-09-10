<?php
/**
 * Tests the ChatClient.
 *
 * It should be able to prompt a user for a unique username.
 * Notifies the user if the selected name isn't unique.
 *
 * It can also poll the server for unread messages for the selected username,
 * if any messages are found it should also request the server remove the displayed messages
 * from the Unread table.
 *
 * @package    bunq_assignment
 * @author     Dimitri
 */

namespace ChatApplication\tests;

use ChatApplication\Client\ChatClient;
use ChatApplication\Client\ChatCommands\MessageSendCommand;
use ChatApplication\Client\RemoteRequest;
use ChatApplication\Server\DatabaseService\SQLiteDatabase;
use PHPUnit\Framework\TestCase;

class ChatClientTest extends TestCase
{
    protected $remote_request;

    public static function setUpBeforeClass() {
        $dbh = new SQLiteDatabase(__DIR__ . '/../../database/chat_server.db');
        $dbh->query('INSERT INTO Users (username) VALUES(:username)', ['username' => 'Jill']);
        $dbh = null;
    }

    public static function tearDownAfterClass() {
        $dbh = new SQLiteDatabase(__DIR__ . '/../../database/chat_server.db');
        $dbh->query('DROP TABLE Users', []);
        $dbh->query('DROP TABLE Messages', []);
        $dbh->query('DROP TABLE Unread', []);
        $dbh = null;
    }

    /** @test */
    public function it_can_prompt_for_a_username_and_send_to_server() {
        //Arrange the client's username.
        $chat_client = new ChatClient(__DIR__ . '/resources/bob_name.txt');
        //Assert the user is prompted.
        $this->expectOutputRegex('/Enter username:/');
        //Assert the username is set correctly.
        $this->assertTrue($chat_client->prompt_user_for_username($this->remote_request));
        $this->assertEquals('Bob', $chat_client->get_username());
    }

    /** @test */
    public function it_returns_false_if_the_username_already_exists() {
        //Arrange the client's username.
        $chat_client = new ChatClient(__DIR__ . '/resources/bob_name.txt');
        //Assert the user is prompted.
        $this->expectOutputRegex('/Enter username:/');
        //Assert the user gets feedback.
        $this->assertFalse($chat_client->prompt_user_for_username($this->remote_request));
        $this->expectOutputRegex('/Username already exists!/');
    }

    /** @test */
    public function it_can_poll_for_new_messages() {
        //Arrange the other user and the messages.
        $chat_client = new ChatClient(__DIR__ . '/resources/robert_name.txt');
        $arguments = ['Robert', 'Sup!'];
        $message_send_command = new MessageSendCommand($this->remote_request, $arguments);
        $message_send_command->execute('Jill');
        $message_send_command = new MessageSendCommand($this->remote_request, $arguments);
        $message_send_command->execute('Jill');
        //Act and assert that unread messages have been found.
        $this->assertTrue($chat_client->poll_for_unread_messages($this->remote_request));
        //Arrange
        $dbh = new SQLiteDatabase(__DIR__ . '/../../database/chat_server.db');
        $unread_count = $dbh->query('SELECT COUNT(*) FROM Unread', [])->fetchColumn()[0];
        $dbh = null;
        //Assert no messages are left in the unread table.
        $this->assertEquals(0, $unread_count);
    }

    protected function setUp() {
        $this->remote_request = new RemoteRequest(WEB_SERVER_HOST, 8002);
    }
}
