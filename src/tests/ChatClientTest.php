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
        $chat_client = new ChatClient(__DIR__ . '/resources/bob_name.txt');
        $this->expectOutputRegex('/Enter username:/');
        $this->assertTrue($chat_client->prompt_user_for_username($this->remote_request));
        $this->assertEquals('Bob', $chat_client->get_username());
    }

    /** @test */
    public function it_returns_false_if_the_username_already_exists() {
        $chat_client = new ChatClient(__DIR__ . '/resources/bob_name.txt');
        $this->expectOutputRegex('/Enter username:/');
        $this->assertFalse($chat_client->prompt_user_for_username($this->remote_request));
        $this->expectOutputRegex('/Username already exists!/');
    }

    /** @test */
    public function it_can_poll_for_new_messages() {
        $chat_client = new ChatClient(__DIR__ . '/resources/robert_name.txt');
        $this->assertTrue($chat_client->prompt_user_for_username($this->remote_request));
        $arguments = ['Robert', 'Sup!'];
        $message_send_command = new MessageSendCommand($this->remote_request, $arguments);
        $message_send_command->execute('Jill');
        $message_send_command = new MessageSendCommand($this->remote_request, $arguments);
        $message_send_command->execute('Jill');
        $this->assertTrue($chat_client->poll_for_unread_messages($this->remote_request));
    }

    protected function setUp() {
        $this->remote_request = new RemoteRequest(WEB_SERVER_HOST, 8002);
    }
}
