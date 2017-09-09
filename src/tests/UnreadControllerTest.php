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
        $this->db = new SQLiteDatabase(__DIR__ . '/test_unread_controller.db');
        $this->unread_controller = new UnreadController($this->db);
        $users_controller = new UsersController($this->db);
        $arguments = ['username' => 'Bob'];
        $users_controller->post($arguments);
        $arguments = ['username' => 'Jill'];
        $users_controller->post($arguments);

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
        $arguments = ['message_id' => 1];
        $unread_count = $this->db->query("SELECT Count(*) FROM Unread")->fetchColumn()[0];
        $this->unread_controller->delete($arguments);
        $results = $this->unread_controller->get_result_array();
        $new_unread_count = $this->db->query("SELECT Count(*) FROM Unread")->fetchColumn()[0];
        $this->assertTrue($results['ok']);
        $this->assertEquals($unread_count - 1, $new_unread_count);
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
