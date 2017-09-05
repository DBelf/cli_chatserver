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
    /** @test */
    public function it_can_initialize_an_empty_db_with_three_tables() {
        $db = new DatabaseWrapper();
        $this->assertEquals(0, $db->total_users());
        $this->assertEquals(0, $db->total_messages());
        $this->assertEquals(0, $db->total_unread());
    }

    /** @test */
    public function it_can_save_a_user() {
        $db = new DatabaseWrapper();
        $user_count = $db->total_users();
        $db->insert_user('Bob', 1);
        $this->assertEquals($user_count + 1, $db->total_users());
    }

    /** @test */
    public function it_can_retrieve_a_user() {
        $db = new DatabaseWrapper();
        $username = 'Bob';
        $db->insert_user($username);
        $this->assertEquals(1, $db->total_users());
        $user = $db->retrieve_user_by_name('Bob');
        $this->assertEquals($username, $user['username']);
    }

    /** @test */
    public function it_can_delete_a_user(){
        $db = new DatabaseWrapper();
        $username = 'Bob';
        $this->assertEquals(0, $db->total_users());
        $db->insert_user($username);
        $this->assertEquals(1, $db->total_users());
        $db->delete_user($username);
        $this->assertEquals(0, $db->total_users());
    }

    /** @test */
    public function it_can_save_a_message() {
        $db = new DatabaseWrapper();
        $sender = 'Bob';
        $receiver = 'Jill';
        $body = "Hello!";
        $db->insert_user($sender);
        $db->insert_user($receiver);
        $this->assertEquals(2, $db->total_users());

        $sender_id = $db->retrieve_user_by_name($sender)['id'];
        $receiver_id = $db->retrieve_user_by_name($receiver)['id'];

        $db->insert_message($sender_id, $receiver_id, $body);
        $this->assertEquals(1, $db->total_messages());
        $this->assertEquals(1, $db->total_unread());
    }
    
    /** @test */
    public function it_can_retrieve_an_unread_message_and_remove_the_entry(){
        $db = new DatabaseWrapper();
        $sender = 'Bob';
        $receiver = 'Jill';
        $body = "Hello!";

        $db->insert_user($sender);
        $db->insert_user($receiver);

        $sender_id = $db->retrieve_user_by_name($sender)['id'];
        $receiver_id = $db->retrieve_user_by_name($receiver)['id'];
        $db->insert_message($sender_id, $receiver_id, $body);

        $unread_count = $db->total_unread();
        $unread = $db->retrieve_unread($receiver)[0];
        $this->assertEquals($body, $unread['body']);
        $this->assertEquals($sender, $unread['sender_name']);
        $this->assertEquals($unread_count - 1, $db->total_unread());
    }
}