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

use ChatApplication\Server\DatabaseService\SQLiteDatabase;
use ChatApplication\Server\Controllers\MessagesController;
use ChatApplication\Server\Controllers\UnreadController;
use ChatApplication\Server\Controllers\UsersController;
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
        $this->db = new SQLiteDatabase(__DIR__ . '/test_unread_controller.db');
        $this->unread_controller = new UnreadController($this->db);
        $users_model = new UsersController($this->db);
        $arguments = ['username' => 'Bob']; //id = 1
        $users_model->post($arguments);
        $arguments = ['username' => 'Jill']; //id = 2
        $users_model->post($arguments);

        $message_model = new MessagesController($this->db);
        $arguments = [
            'sender_id' => 1,
            'receiver_id' => 2,
            'body' => 'Hello!'
        ];
        $message_model->post($arguments);
    }

    /** @test */
    public function it_can_delete_a_row_from_the_unread_table() {
        $arguments = ['message_id' => 1];
        $unread_count = $this->db->query("SELECT Count(*) FROM Unread")->fetchColumn()[0];
        $this->unread_controller->delete($arguments);
        $results = $this->unread_controller->get_result_array();
        $new_unread_count = $this->db->query("SELECT Count(*) FROM Unread")->fetchColumn()[0];
        $this->assertTrue($results['ok']);
        $this->assertEquals(0, $new_unread_count);
    }

    /** @test */
    public function it_fails_on_unimplmented_get_verb() {
        $this->unread_controller->get(array());
        $results = $this->unread_controller->get_result_array();
        $this->assertFalse($results['ok']);
        $this->assertEquals('Method not implemented!', $results['error']);
    }

    /** @test */
    public function it_fails_on_unimplemented_post_verb() {
        $this->unread_controller->put(array());
        $results = $this->unread_controller->get_result_array();
        $this->assertFalse($results['ok']);
        $this->assertEquals('Method not implemented!', $results['error']);
    }

    /** @test */
    public function it_fails_on_unimplemented_put_verb() {
        $this->unread_controller->put(array());
        $results = $this->unread_controller->get_result_array();
        $this->assertFalse($results['ok']);
        $this->assertEquals('Method not implemented!', $results['error']);
    }

    protected function tearDown() {
        $this->db = null;
        $this->unread_controller = null;
    }

    //Removes the database file to ensure predictable tests.
    public static function tearDownAfterClass() {
        $file = __DIR__ . '\test_unread_controller.db';
        unlink($file);
    }
}
