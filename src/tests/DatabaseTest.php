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

    protected $_db;

    protected function setUp() {
        $this->_db = new DatabaseWrapper();
    }

    /** @test */
    public function it_can_initialize_an_empty_db_with_three_tables() {
        $this->assertEquals(0, $this->_db->total_users());
        $this->assertEquals(0, $this->_db->total_messages());
        $this->assertEquals(0, $this->_db->total_unread());
    }

    /** @test */
    public function it_can_save_a_user() {
        $user_count = $this->_db->total_users();
        $this->_db->insert_user('Bob', 1);
        $this->assertEquals($user_count + 1, $this->_db->total_users());
    }

    /** @test */
    public function it_can_retrieve_a_user() {
        $username = 'Bob';
        $this->_db->insert_user($username);
        $this->assertEquals(1, $this->_db->total_users());
        $user = $this->_db->retrieve_user_by_name('Bob');
        $this->assertEquals($username, $user['username']);
    }

    /** @test */
    public function it_can_delete_a_user(){
        $this->_db = new DatabaseWrapper();
        $username = 'Bob';
        $this->assertEquals(0, $this->_db->total_users());
        $this->_db->insert_user($username);
        $this->assertEquals(1, $this->_db->total_users());
        $this->_db->delete_user($username);
        $this->assertEquals(0, $this->_db->total_users());
    }

    /** @test */
    public function it_can_save_a_message() {
        $this->_db = new DatabaseWrapper();
        $sender = 'Bob';
        $receiver = 'Jill';
        $body = "Hello!";
        $this->_db->insert_user($sender);
        $this->_db->insert_user($receiver);
        $this->assertEquals(2, $this->_db->total_users());

        $sender_id = $this->_db->retrieve_user_by_name($sender)['id'];
        $receiver_id = $this->_db->retrieve_user_by_name($receiver)['id'];

        $this->_db->insert_message($sender_id, $receiver_id, $body);
        $this->assertEquals(1, $this->_db->total_messages());
        $this->assertEquals(1, $this->_db->total_unread());
    }
    
    /** @test */
    public function it_can_retrieve_an_unread_message(){
        $this->_db = new DatabaseWrapper();
        $sender = 'Bob';
        $receiver = 'Jill';
        $body = "Hello!";

        $this->_db->insert_user($sender);
        $this->_db->insert_user($receiver);

        $sender_id = $this->_db->retrieve_user_by_name($sender)['id'];
        $receiver_id = $this->_db->retrieve_user_by_name($receiver)['id'];
        $this->_db->insert_message($sender_id, $receiver_id, $body);

        $unread = $this->_db->retrieve_unread($receiver)[0];
        $this->assertEquals($body, $unread['body']);
        $this->assertEquals($sender, $unread['sender_name']);
    }
    
    /** @test */
    public function it_can_remove_a_message_from_the_unread_table(){
        $this->_db = new DatabaseWrapper();
        $sender = 'Bob';
        $receiver = 'Jill';
        $body = "Hello!";

        $this->_db->insert_user($sender);
        $this->_db->insert_user($receiver);

        $sender_id = $this->_db->retrieve_user_by_name($sender)['id'];
        $receiver_id = $this->_db->retrieve_user_by_name($receiver)['id'];
        $this->_db->insert_message($sender_id, $receiver_id, $body);

        $unread = $this->_db->retrieve_unread($receiver)[0];

        $unread_count = $this->_db->total_unread();

        $this->_db->remove_from_unread($unread['id']);
        $this->assertEquals($unread_count - 1, $this->_db->total_unread());
    }
}