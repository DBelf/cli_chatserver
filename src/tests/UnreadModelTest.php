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
use ChatApplication\Server\Models\MessagesModel;
use ChatApplication\Server\Models\UnreadModel;
use ChatApplication\Server\Models\UsersModel;
use PHPUnit\Framework\TestCase;

class UnreadModelTest extends TestCase
{
    /**
     * @var DatabaseService
     */
    protected $db;
    /**
     * @var MessagesModel
     */
    protected $unread_model;

    protected function setUp() {
        $this->db = new SQLiteDatabase(__DIR__ . '/test_unread_model.db');
        $this->unread_model = new UnreadModel($this->db);
        $users_model = new UsersModel($this->db);
        $arguments = ['username' => 'Bob']; //id = 1
        $users_model->post($arguments);
        $arguments = ['username' => 'Jill']; //id = 2
        $users_model->post($arguments);

        $message_model = new MessagesModel($this->db);
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
        $this->unread_model->delete($arguments);
        $results = $this->unread_model->get_result_array();
        $new_unread_count = $this->db->query("SELECT Count(*) FROM Unread")->fetchColumn()[0];
        $this->assertTrue($results['ok']);
        $this->assertEquals(0, $new_unread_count);
    }

    /** @test */
    public function it_fails_on_unimplmented_get_verb() {
        $this->unread_model->get(array());
        $results = $this->unread_model->get_result_array();
        $this->assertFalse($results['ok']);
        $this->assertEquals('Method not implemented!', $results['error']);
    }

    /** @test */
    public function it_fails_on_unimplemented_post_verb() {
        $this->unread_model->put(array());
        $results = $this->unread_model->get_result_array();
        $this->assertFalse($results['ok']);
        $this->assertEquals('Method not implemented!', $results['error']);
    }

    /** @test */
    public function it_fails_on_unimplemented_put_verb() {
        $this->unread_model->put(array());
        $results = $this->unread_model->get_result_array();
        $this->assertFalse($results['ok']);
        $this->assertEquals('Method not implemented!', $results['error']);
    }

    protected function tearDown() {
        $this->db = null;
        $this->unread_model = null;
    }

    //Removes the database file to ensure predictable tests.
    public static function tearDownAfterClass() {
        $file = __DIR__ . '\test_unread_model.db';
        unlink($file);
    }
}
