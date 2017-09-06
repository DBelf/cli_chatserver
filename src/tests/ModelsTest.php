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

class ModelsTest extends TestCase
{
    protected $_db;

    protected function setUp() {
        $this->_db = new SQLiteDatabase(__DIR__ . '/test_models.db');
    }

    protected function tearDown() {
        $this->_db = null;
    }

    /** @test */
    public function it_can_post_a_new_user_and_return_id() {
        $users_model = new Models\UsersModel($this->_db);
        $arguments = ['username' => 'Bob'];
        $id = $users_model->post($arguments);
        $this->assertEquals(1, $id);
    }

    /** @test */
    public function it_can_retrieve_a_user_id_and_username() {
        $users_model = new Models\UsersModel($this->_db);
        $arguments = ['username' => 'Bob'];
        $user = $users_model->get($arguments);
        $this->assertArrayHasKey('id' ,$user->to_array());
        $this->assertArrayHasKey('username' ,$user->to_array());
        $this->assertEquals('Bob' ,$user->to_array()['username']);
    }

    /** @test */
    public function it_can_retrieve_all_users() {
        $users_model = new Models\UsersModel($this->_db);
        $users = $users_model->get();
        $this->assertInternalType('array', $users);
        $first = $users[0];
        $this->assertEquals('Bob', $first->to_array()['username']);
    }

    //Removes the database file to ensure predictable tests.
    public static function tearDownAfterClass() {
        $file = __DIR__ . '\test_models.db';
        unlink($file);
    }
}