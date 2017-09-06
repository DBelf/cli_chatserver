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

require_once(__DIR__ . '/../../vendor/autoload.php');

use ChatApplication\Server\DatabaseService\SQLiteDatabase;
use PHPUnit\Framework\TestCase;
use ChatApplication\Server\Models;

class UserModelTest extends TestCase
{
    protected $_db;
    protected $_users_model;

    protected function setUp() {
        $this->_db = new SQLiteDatabase(__DIR__ . '/test_models.db');
        $this->_users_model = new Models\UsersModel($this->_db);
    }

    protected function tearDown() {
        $this->_db = null;
        $this->_users_model = null;
    }

    /** @test */
    public function it_can_post_a_new_user_and_return_id() {
        $arguments = ['username' => 'Bob'];
        $this->_users_model->post($arguments);
        $results = $this->_users_model->get_results();
        $id = $results['user_id'];
        $this->assertTrue($results['ok']);
        $this->assertEquals(1, $id);
    }

    /** @test */
    public function it_cant_add_same_username_twice() {
        $arguments = ['username' => 'Bob'];
        $this->_users_model->post($arguments);
        $results = $this->_users_model->get_results();
        $error_message = $results['error'];
        $this->assertFalse($results['ok']);
        $this->assertEquals("Duplicate entry found" ,$error_message);
    }

    /** @test */
    public function it_can_retrieve_a_user_id_and_username() {
        $arguments = ['username' => 'Bob'];
        $this->_users_model->get($arguments);
        $results = $this->_users_model->get_results();
        $user = $results['users'];
        $this->assertTrue($results['ok']);
        $this->assertArrayHasKey('id' ,$user->to_array());
        $this->assertArrayHasKey('username' ,$user->to_array());
        $this->assertEquals('Bob' ,$user->to_array()['username']);
    }

    /** @test */
    public function it_can_retrieve_all_users() {
        $this->_users_model->get();
        $results = $this->_users_model->get_results();
        $users = $results['users'];
        $this->assertTrue($results['ok']);
        $this->assertInternalType('array', $users);
        $first = $users[0];
        $this->assertEquals('Bob', $first->to_array()['username']);
    }
    
    /** @test */
    public function it_can_change_a_username() {
        $arguments = [
            'old_username' => 'Bob',
            'new_username' => 'Robert'
        ];
        $this->_users_model->put($arguments);
        $results = $this->_users_model->get_results();
        $new_name = $results['new_username'];
        $this->assertTrue($results['ok']);
        $this->assertEquals('Robert', $new_name);
    }

    /** @test */
    public function it_cant_change_to_an_existing_username() {
        $arguments = ['username' => 'Bob'];
        $id = $this->_users_model->post($arguments);
        $arguments = [
            'old_username' => 'Robert',
            'new_username' => 'Bob'
        ];
        $this->_users_model->put($arguments);
        $results = $this->_users_model->get_results();
        $error_message = $results['error'];
        $this->assertFalse($results['ok']);
        $this->assertEquals("Duplicate entry found" ,$error_message);
    }

    /** @test */
    public function it_can_delete_a_user() {
        $arguments = [
          'username' => 'Robert'
        ];
        $this->_users_model->delete($arguments);
        $this->_users_model->get();
        $results = $this->_users_model->get_results();
        $users = $results['users'];
        $this->assertTrue(count($users) === 1);
    }
    
    //Removes the database file to ensure predictable tests.
    public static function tearDownAfterClass() {
        $file = __DIR__ . '\test_models.db';
        unlink($file);
    }
}