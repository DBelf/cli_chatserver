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

use ChatApplication\Server\Controllers\MessagesController;
use ChatApplication\Server\Controllers\UsersController;
use ChatApplication\Server\DatabaseService\SQLiteDatabase;
use PHPUnit\Framework\TestCase;

class MessagesControllerTest extends TestCase
{
    /**
     * @var SQLiteDatabase
     */
    protected $db;
    /**
     * @var MessagesController
     */
    protected $messages_controller;

    protected function setUp() {
        $this->db = new SQLiteDatabase(__DIR__ . '/test_messages_controller.db');
        $this->messages_controller = new MessagesController($this->db);
        $users_controller = new UsersController($this->db);
        $arguments = ['username' => 'Bob']; //id = 1
        $users_controller->post($arguments);
        $arguments = ['username' => 'Jill']; //id = 2
        $users_controller->post($arguments);
    }

    protected function tearDown() {
        $this->db = null;
        $this->messages_controller = null;
    }

    /** @test */
    public function it_can_add_a_new_message_to_read_and_unread_tables() {
        $arguments = [
            'sender_name' => 'Bob',
            'receiver_name' => 'Jill',
            'body' => 'Hello!'
        ];
        $message_count = $this->db->query("SELECT Count(*) FROM Messages")->fetchColumn()[0];
        $unread_count = $this->db->query("SELECT Count(*) FROM Unread")->fetchColumn()[0];

        $this->messages_controller->post($arguments);
        $new_message_count = $this->db->query("SELECT Count(*) FROM Messages")->fetchColumn()[0];
        $new_unread_count = $this->db->query("SELECT Count(*) FROM Unread")->fetchColumn()[0];
        $this->assertTrue($this->messages_controller->get_result_array()['ok']);
        $this->assertEquals($message_count + 1, $new_message_count);
        $this->assertEquals($unread_count + 1, $new_unread_count);
    }
    
    /** @test */
    public function it_can_retrieve_all_unread_messages_for_a_user() {
        $arguments = [
            'receiver' => 'Jill' //Jill
        ];
        $this->messages_controller->get($arguments);
        $results = $this->messages_controller->get_result_array();
        $this->assertTrue($results['ok']);
        $this->assertInternalType('array', $results['messages']);
        $this->assertEquals(1, count($results['messages']));
        $this->assertEquals('Hello!', $results['messages'][0]['body']);
    }

    /** @test */
    public function it_fails_to_retrieve_messages_without_an_argument() {
        $this->messages_controller->get();
        $results = $this->messages_controller->get_result_array();
        $this->assertFalse($results['ok']);
        $this->assertEquals('No argument supplied!', $results['error']);
    }
    
    /** @test */
    public function it_fails_on_unimplemented_put_verb() {
        $this->messages_controller->put();
        $results = $this->messages_controller->get_result_array();
        $this->assertFalse($results['ok']);
        $this->assertEquals('Method not implemented!', $results['error']);
    }

    /** @test */
    public function it_fails_on_unimplemented_delete_verb() {
        $this->messages_controller->delete();
        $results = $this->messages_controller->get_result_array();
        $this->assertFalse($results['ok']);
        $this->assertEquals('Method not implemented!', $results['error']);
    }

    //Removes the database file to ensure predictable tests.
    public static function tearDownAfterClass() {
        $file = __DIR__ . '\test_messages_controller.db';
        unlink($file);
    }
}