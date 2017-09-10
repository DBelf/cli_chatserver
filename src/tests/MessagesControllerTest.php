<?php
/**
 * Tests MessagesController.
 *
 * A MessagesController should add the new message to the Messages and Unread tables.
 * A MessagesController should only have the put and get verbs implemented.
 * A MessagesController fails when the arguments are incorrect.
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
        //Arrange the database to contain two users already.
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
        //Arrange the message and the number of messages currently in the database.
        $arguments = [
            'sender_name' => 'Bob',
            'receiver_name' => 'Jill',
            'body' => 'Hello!'
        ];
        $message_count = $this->db->query("SELECT Count(*) FROM Messages")->fetchColumn()[0];
        $unread_count = $this->db->query("SELECT Count(*) FROM Unread")->fetchColumn()[0];
        //Invoke the post method.
        $this->messages_controller->post($arguments);
        //Arrange the updated message count.
        $new_message_count = $this->db->query("SELECT Count(*) FROM Messages")->fetchColumn()[0];
        $new_unread_count = $this->db->query("SELECT Count(*) FROM Unread")->fetchColumn()[0];
        //Assert a success response and whether the message has been added to successfully to both tables.
        $this->assertTrue($this->messages_controller->get_result_array()['ok']);
        $this->assertEquals($message_count + 1, $new_message_count);
        $this->assertEquals($unread_count + 1, $new_unread_count);
    }
    
    /** @test */
    public function it_can_retrieve_all_unread_messages_for_a_user() {
        //Arrange.
        $arguments = [
            'receiver' => 'Jill' //Jill
        ];
        //Invoke the get method.
        $this->messages_controller->get($arguments);
        $results = $this->messages_controller->get_result_array();
        //Assert a success response and whether the response contains the recently added message.
        $this->assertTrue($results['ok']);
        $this->assertInternalType('array', $results['messages']);
        $this->assertEquals(1, count($results['messages']));
        $this->assertEquals('Hello!', $results['messages'][0]['body']);
    }

    /** @test */
    public function it_fails_to_retrieve_messages_without_an_argument() {
        //Invoke the get method without arguments.
        $this->messages_controller->get();
        $results = $this->messages_controller->get_result_array();
        //Assert a fail response and correct error message.
        $this->assertFalse($results['ok']);
        $this->assertEquals('No argument supplied!', $results['error']);
    }
    
    /** @test */
    public function it_fails_on_unimplemented_put_verb() {
        //Invoke an unimplemented method.
        $this->messages_controller->put();
        $results = $this->messages_controller->get_result_array();
        //Assert a fail response and correct error message.
        $this->assertFalse($results['ok']);
        $this->assertEquals('Method not implemented!', $results['error']);
    }

    /** @test */
    public function it_fails_on_unimplemented_delete_verb() {
        //Invoke an unimplemented method.
        $this->messages_controller->delete();
        $results = $this->messages_controller->get_result_array();
        //Assert a fail response and correct error message.
        $this->assertFalse($results['ok']);
        $this->assertEquals('Method not implemented!', $results['error']);
    }

    //Removes the database file to ensure predictable tests.
    public static function tearDownAfterClass() {
        $file = __DIR__ . '\test_messages_controller.db';
        unlink($file);
    }
}