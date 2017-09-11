<?php
/**
 * Tests the UsersController. @see UsersController
 *
 * A UsersController can add a new user to the Users table.
 * A UsersController can update a row in the Users table if the new username is unique.
 * A UsersController can delete a row from the Users table.
 * A UsersController can retrieve one or all rows from the Users table.
 * A UsersController should only have all HTTP verbs implemented.
 * A UsersController fails when the arguments are incorrect.
 *
 * @package    bunq_assignment
 * @author     Dimitri
 */

namespace ChatApplication\tests;

require_once(__DIR__ . '/../../vendor/autoload.php');

use ChatApplication\Server\Controllers;
use ChatApplication\Server\DatabaseService\DatabaseService;
use ChatApplication\Server\DatabaseService\SQLiteDatabase;
use PHPUnit\Framework\TestCase;

class UsersControllerTest extends TestCase
{

    /**
     * @var DatabaseService
     */
    protected $db;

    /**
     * @var Controllers\UsersController
     */
    protected $users_controller;

    protected function setUp() {
        $this->db = new SQLiteDatabase(__DIR__ . '/test_users_controller.db');
        $this->users_controller = new Controllers\UsersController($this->db);
    }

    protected function tearDown() {
        $this->db = null;
        $this->users_controller = null;
    }

    /** @test */
    public function it_can_post_a_new_user_and_return_id() {
        //Arrange the username.
        $arguments = ['username' => 'Bob'];
        //Invoke the post method.
        $this->users_controller->post($arguments);
        $results = $this->users_controller->get_result_array();
        $id = $results['user_id'];
        $username = $results['username'];
        //Assert the post was successful and the response is correct.
        $this->assertTrue($results['ok']);
        $this->assertEquals(1, $id);
        $this->assertEquals('Bob', $username);
    }

    /** @test */
    public function it_fails_to_add_same_username_twice() {
        //Arrange the username.
        $arguments = ['username' => 'Bob'];
        //Invoke the post method.
        $this->users_controller->post($arguments);
        $results = $this->users_controller->get_result_array();
        $error_message = $results['error'];
        //Assert the post was unsuccessful and the error is correct.
        $this->assertFalse($results['ok']);
        $this->assertEquals('Username already exists!', $error_message);
    }

    /** @test */
    public function it_can_retrieve_a_user_id_and_username() {
        //Arrange the username.
        $arguments = ['username' => 'Bob'];
        //Invoke the get method.
        $this->users_controller->get($arguments);
        $results = $this->users_controller->get_result_array();
        $user = $results['users'][0];
        //Assert the get was successful and the response is correct.
        $this->assertTrue($results['ok']);
        $this->assertArrayHasKey('id' ,$user);
        $this->assertArrayHasKey('username' ,$user);
        $this->assertEquals('Bob' ,$user['username']);
    }

    /** @test */
    public function it_can_retrieve_all_users() {
        //Invoke the get method.
        $this->users_controller->get();
        $results = $this->users_controller->get_result_array();
        $users = $results['users'];
        //Assert the get was successful and the response is correct.
        $this->assertTrue($results['ok']);
        $this->assertInternalType('array', $users);
        $first = $users[0];
        $this->assertEquals('Bob', $first['username']);
    }

    /** @test */
    public function it_can_change_a_username() {
        //Arrange the old and new usernames.
        $arguments = [
            'old_username' => 'Bob',
            'new_username' => 'Robert'
        ];
        //Invoke the put method.
        $this->users_controller->put($arguments);
        $results = $this->users_controller->get_result_array();
        //Assert the post was successful and the username is correct.
        $new_name = $results['new_username'];
        $this->assertTrue($results['ok']);
        $this->assertEquals('Robert', $new_name);
    }

    /** @test */
    public function it_fails_to_change_to_an_existing_username() {
        //Arrange the old and new usernames.
        $arguments = ['username' => 'Bob'];
        $this->users_controller->post($arguments);
        $arguments = [
            'old_username' => 'Robert',
            'new_username' => 'Bob'
        ];
        //Invoke the put method.
        $this->users_controller->put($arguments);
        $results = $this->users_controller->get_result_array();
        //Assert the post was unsuccessful and the error is correct.
        $error_message = $results['error'];
        $this->assertFalse($results['ok']);
        $this->assertEquals('Username already exists!', $error_message);
    }

    /** @test */
    public function it_can_delete_a_user() {
        //Arrange the username.
        $arguments = [
          'username' => 'Robert'
        ];
        //Invoke the delete method to delete Robert.
        $this->users_controller->delete($arguments);
        //Get a list of the remaining users.
        $this->users_controller->get();
        $results = $this->users_controller->get_result_array();
        $users = $results['users'];
        //Assert the list of remaining users contains one user named Bob.
        $this->assertTrue(count($users) === 1);
        $this->assertEquals('Bob', $users[0]['username']);
    }

    //Removes the database file to ensure predictable tests.
    public static function tearDownAfterClass() {
        $file = __DIR__ . '/test_users_controller.db';
        unlink($file);
    }
}