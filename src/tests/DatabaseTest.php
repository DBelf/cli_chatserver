<?php
/**
 * Short description for file
 *
 * Long description for file (if any)...
 *
 * @package    ChatAssignment
 * @author     Dimitri
 */

namespace ChatApplication\tests;

require_once(__DIR__ . '/../../vendor/autoload.php');

use PHPUnit\Framework\TestCase;
use ChatApplication\Server\DatabaseWrapper;

class DatabaseTest extends TestCase
{

    protected $db;

    protected function setUp() {
        $this->db = new DatabaseWrapper();
    }

    /** @test */
    public function it_can_initialize_an_empty_db_with_three_tables() {
        $this->assertEquals(0, $this->db->total_users());
        $this->assertEquals(0, $this->db->total_messages());
        $this->assertEquals(0, $this->db->total_unread());
    }

    /** @test */
    public function it_can_save_a_user() {
        $user_count = $this->db->total_users();
        $this->db->insert_user('Bob', 1);
        $this->assertEquals($user_count + 1, $this->db->total_users());
    }

    /** @test */
    public function it_can_retrieve_a_user() {
        $username = 'Bob';
        $this->db->insert_user($username);
        $this->assertEquals(1, $this->db->total_users());
        $user = $this->db->retrieve_user_by_name('Bob');
        $this->assertEquals($username, $user['username']);
    }

    /** @test */
    public function it_can_delete_a_user(){
        $this->db = new DatabaseWrapper();
        $username = 'Bob';
        $this->assertEquals(0, $this->db->total_users());
        $this->db->insert_user($username);
        $this->assertEquals(1, $this->db->total_users());
        $this->db->delete_user($username);
        $this->assertEquals(0, $this->db->total_users());
    }

    /** @test */
    public function it_can_save_a_message() {
        $this->db = new DatabaseWrapper();
        $sender = 'Bob';
        $receiver = 'Jill';
        $body = "Hello!";
        $this->db->insert_user($sender);
        $this->db->insert_user($receiver);
        $this->assertEquals(2, $this->db->total_users());

        $sender_id = $this->db->retrieve_user_by_name($sender)['id'];
        $receiver_id = $this->db->retrieve_user_by_name($receiver)['id'];

        $this->db->insert_message($sender_id, $receiver_id, $body);
        $this->assertEquals(1, $this->db->total_messages());
        $this->assertEquals(1, $this->db->total_unread());
    }
    
    /** @test */
    public function it_can_retrieve_an_unread_message(){
        $this->db = new DatabaseWrapper();
        $sender = 'Bob';
        $receiver = 'Jill';
        $body = "Hello!";

        $this->db->insert_user($sender);
        $this->db->insert_user($receiver);

        $sender_id = $this->db->retrieve_user_by_name($sender)['id'];
        $receiver_id = $this->db->retrieve_user_by_name($receiver)['id'];
        $this->db->insert_message($sender_id, $receiver_id, $body);

        $unread = $this->db->retrieve_unread($receiver)[0];
        $this->assertEquals($body, $unread['body']);
        $this->assertEquals($sender, $unread['sender_name']);
    }
    
    /** @test */
    public function it_can_remove_a_message_from_the_unread_table(){
        $this->db = new DatabaseWrapper();
        $sender = 'Bob';
        $receiver = 'Jill';
        $body = "Hello!";

        $this->db->insert_user($sender);
        $this->db->insert_user($receiver);

        $sender_id = $this->db->retrieve_user_by_name($sender)['id'];
        $receiver_id = $this->db->retrieve_user_by_name($receiver)['id'];
        $this->db->insert_message($sender_id, $receiver_id, $body);

        $unread = $this->db->retrieve_unread($receiver)[0];

        $unread_count = $this->db->total_unread();

        $this->db->remove_from_unread($unread['id']);
        $this->assertEquals($unread_count - 1, $this->db->total_unread());
    }
}