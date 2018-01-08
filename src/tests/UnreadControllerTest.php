<?php
/**
 * Tests the UnreadController. @see UnreadController
 *
 * An UnreadController should delete the row from the unread table corresponding to the message id.
 * An UnreadController should only have the delete verb implemented.
 *
 * @package    chat_server
 * @author     Dimitri
 */

namespace ChatApplication\tests;

use ChatApplication\Server\Controllers\MessagesController;
use ChatApplication\Server\Controllers\UnreadController;
use ChatApplication\Server\Controllers\UsersController;
use ChatApplication\Server\DatabaseService\DatabaseService;
use ChatApplication\Server\DatabaseService\SQLiteDatabase;
use PHPUnit\Framework\TestCase;

class UnreadControllerTest extends TestCase
{
    /**
     * @var DatabaseService
     */
    protected $db;
    /**
     * @var MessagesController
     */
    protected $unread_controller;

    protected function setUp() {
        //Constructs a new database.
        $this->db = new SQLiteDatabase(__DIR__ . '/test_unread_controller.db');
        $this->unread_controller = new UnreadController($this->db);
        $users_controller = new UsersController($this->db);
        //Adds two users to the database.
        $arguments = ['username' => 'Bob'];
        $users_controller->post($arguments);
        $arguments = ['username' => 'Jill'];
        $users_controller->post($arguments);
        //Adds a message to the database.
        $message_controller = new MessagesController($this->db);
        $arguments = [
            'sender_name' => 'Bob',
            'receiver_name' => 'Jill',
            'body' => 'Hello!'
        ];
        $message_controller->post($arguments);
    }

    /** @test */
    public function it_can_delete_a_row_from_the_unread_table() {
        //Arrange message id.
        $arguments = ['message_id' => 1];
        //Arrange the unread count.
        $unread_count = $this->db->query("SELECT Count(*) FROM Unread", [])->fetchColumn(0);
        //Invoke the delete method.
        $this->unread_controller->delete($arguments);
        $results = $this->unread_controller->get_result_array();
        //Arrange the new unread count.
        $new_unread_count = $this->db->query("SELECT Count(*) FROM Unread", [])->fetchColumn(0);
        //Assert whether the delete was successful and whether the row has been removed from the Unread table.
        $this->assertTrue($results['ok']);
        $this->assertEquals($unread_count - 1, $new_unread_count);
    }

    /** @test */
    public function it_fails_on_unimplmented_get_verb() {
        //Invoke an unimplemented method.
        $this->unread_controller->get(array());
        $results = $this->unread_controller->get_result_array();
        //Assert a fail response and correct error message.
        $this->assertFalse($results['ok']);
        $this->assertEquals('Method not implemented!', $results['error']);
    }

    /** @test */
    public function it_fails_on_unimplemented_post_verb() {
        //Invoke an unimplemented method.
        $this->unread_controller->put(array());
        $results = $this->unread_controller->get_result_array();
        //Assert a fail response and correct error message.
        $this->assertFalse($results['ok']);
        $this->assertEquals('Method not implemented!', $results['error']);
    }

    /** @test */
    public function it_fails_on_unimplemented_put_verb() {
        //Invoke an unimplemented method.
        $this->unread_controller->put(array());
        $results = $this->unread_controller->get_result_array();
        //Assert a fail response and correct error message.
        $this->assertFalse($results['ok']);
        $this->assertEquals('Method not implemented!', $results['error']);
    }

    protected function tearDown() {
        $this->db = null;
        $this->unread_controller = null;
    }

    //Removes the database file to ensure predictable tests.
    public static function tearDownAfterClass() {
        $file = __DIR__ . '/test_unread_controller.db';
        unlink($file);
    }
}
