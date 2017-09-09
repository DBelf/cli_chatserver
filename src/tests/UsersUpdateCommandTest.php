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

use ChatApplication\Client\ChatCommands\UsersUpdateCommand;
use ChatApplication\Client\RemoteRequest;
use ChatApplication\Server\DatabaseService\SQLiteDatabase;
use PHPUnit\Framework\TestCase;

class UsersUpdateCommandTest extends TestCase
{
    protected $remote_request;

    public static function setUpBeforeClass() {
        $dbh = new SQLiteDatabase(__DIR__ . '/../../database/chat_server.db');
        $dbh->query('INSERT INTO Users (username) VALUES(:username)', ['username' => 'Bob']);
        $dbh->query('INSERT INTO Users (username) VALUES(:username)', ['username' => 'Carl']);
        $dbh = null;
    }

    protected function setUp() {
        $this->remote_request = new RemoteRequest(WEB_SERVER_HOST, 8002);
    }

    /** @test */
    public function it_can_send_an_update_request() {
        $arguments = ['Robert'];
        $users_update_command = new UsersUpdateCommand($this->remote_request, $arguments);
        $this->assertTrue($users_update_command->execute('Bob'));
        $this->expectOutputRegex('/Username successfully updated to Robert!/');
    }

    /** @test */
    public function it_displays_an_error_if_the_username_already_exists() {
        $arguments = ['Carl'];
        $users_update_command = new UsersUpdateCommand($this->remote_request, $arguments);
        $this->assertFalse($users_update_command->execute('Robert'));
        $this->expectOutputRegex('/Username already exists!/');
    }

    /** @test */
    public function it_displays_a_message_if_incorrect_number_of_arguments_provided() {
        $arguments = [];
        $message_send_command = new UsersUpdateCommand($this->remote_request, $arguments);
        $this->assertFalse($message_send_command->execute('Robert'));
        $this->expectOutputRegex('/Need a username to update!/');
    }

    public static function tearDownAfterClass() {
        $dbh = new SQLiteDatabase(__DIR__ . '/../../database/chat_server.db');
        $dbh->query('DROP TABLE Users', []);
        $dbh->query('DROP TABLE Messages', []);
        $dbh->query('DROP TABLE Unread', []);
        $dbh = null;
    }
}
