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
use ChatApplication\Server\Models\UsersModel;
use PHPUnit\Framework\TestCase;

class MessagesModelTest extends TestCase
{
    /**
     * @var DatabaseService
     */
    protected $db;
    /**
     * @var MessagesModel
     */
    protected $messages_model;

    protected function setUp() {
        $this->db = new SQLiteDatabase(__DIR__ . '/test_messages_model.db');
        $this->messages_model = new MessagesModel($this->db);
        $users_model = new UsersModel($this->db);
        $arguments = ['username' => 'Bob']; //id = 1
        $users_model->post($arguments);
        $arguments = ['username' => 'Jill']; //id = 2
        $users_model->post($arguments);
    }

    protected function tearDown() {
        $this->db = null;
        $this->messages_model = null;
    }

    /** @test */
    public function it_can_add_a_new_message_to_read_and_unread_databases() {
        $arguments = [
            'sender_id' => 1,
            'receiver_id' => 2,
            'body' => 'Hello!'
        ];
        $message_count = $this->db->query("SELECT Count(*) FROM Messages")->fetchColumn()[0];
        $unread_count = $this->db->query("SELECT Count(*) FROM Unread")->fetchColumn()[0];

        $this->messages_model->post($arguments);
        $new_message_count = $this->db->query("SELECT Count(*) FROM Messages")->fetchColumn()[0];
        $new_unread_count = $this->db->query("SELECT Count(*) FROM Unread")->fetchColumn()[0];

        $this->assertEquals($message_count + 1, $new_message_count);
        $this->assertEquals($unread_count + 1, $new_unread_count);
    }

    //Removes the database file to ensure predictable tests.
    public static function tearDownAfterClass() {
        $file = __DIR__ . '\test_messages_model.db';
        unlink($file);
    }
}