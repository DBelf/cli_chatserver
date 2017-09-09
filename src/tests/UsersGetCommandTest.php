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

use ChatApplication\Client\ChatCommands\UsersGetCommand;
use ChatApplication\Client\RemoteRequest;
use ChatApplication\Server\DatabaseService\SQLiteDatabase;
use PHPUnit\Framework\TestCase;

class UsersGetCommandTest extends TestCase
{
    protected $remote_request;

    public static function setUpBeforeClass() {
        $dbh = new SQLiteDatabase(__DIR__ . '/../../database/chat_server.db');
        $dbh->query('INSERT INTO Users (username) VALUES(:username)', ['username' => 'Bob']);
        $dbh->query('INSERT INTO Users (username) VALUES(:username)', ['username' => 'Jill']);
        $dbh->query('INSERT INTO Users (username) VALUES(:username)', ['username' => 'Carl']);
        $dbh = null;
    }

    protected function setUp() {
        $this->remote_request = new RemoteRequest(WEB_SERVER_HOST, 8002);
    }

    /** @test */
    public function it_displays_the_information_of_a_single_user() {
        $arguments = ['Bob'];
        $message_send_command = new UsersGetCommand($this->remote_request, $arguments);
        $this->assertTrue($message_send_command->execute('Jill'));
        $this->expectOutputRegex('/Bob: 1/');
    }


    /** @test */
    public function it_displays_the_information_of_all_users() {
        $arguments = [];
        $message_send_command = new UsersGetCommand($this->remote_request, $arguments);
        $this->assertTrue($message_send_command->execute('Jill'));
        $this->expectOutputRegex('/Bob: 1[\r\n|\n]+Jill: 2[\r\n|\n]+Carl: 3/');
    }

    /** @test */
    public function it_displays_a_message_if_username_doesnt_exist_in_database() {
        $arguments = ['Robert'];
        $message_send_command = new UsersGetCommand($this->remote_request, $arguments);
        $this->assertFalse($message_send_command->execute('Jill'));
        $this->expectOutputRegex('/Username doesn\'t exist!/');
    }

    public static function tearDownAfterClass() {
        $dbh = new SQLiteDatabase(__DIR__ . '/../../database/chat_server.db');
        $dbh->query('DROP TABLE Users', []);
        $dbh->query('DROP TABLE Messages', []);
        $dbh->query('DROP TABLE Unread', []);
        $dbh = null;
    }
}
