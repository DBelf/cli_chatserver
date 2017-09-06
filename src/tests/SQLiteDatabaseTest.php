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
use function unlink;

class SQLiteDatabaseTest extends TestCase
{
    protected $_db;
    protected $_database_name = 'src/tests/test.db';

    /** @test */
    public function it_can_initialize_a_db_with_three_tables() {
        $this->_db = new SQLiteDatabase($this->_database_name);
        $base_statement = "SELECT Count(*) FROM sqlite_master WHERE type='table'";
        $result = $this->_db->query($base_statement)->fetchColumn()[0];
        //Assert on 4 because sqlite_sequence is also returned.
        $this->assertEquals(4, $result);
        $this->_db = null;
    }

    /** @test */
    public function it_can_return_the_id_of_the_last_insert() {
        $this->_db = new SQLiteDatabase($this->_database_name);
        $this->assertEquals(0, $this->_db->get_last_insert_id());
        $this->_db = null;
    }

    /** @test */
    public function it_can_execute_an_insert_query() {
        $this->_db = new SQLiteDatabase($this->_database_name);
        $base_statement = "INSERT INTO Users (username) VALUES(:username)";
        $count_statement = "SELECT Count(*) FROM Users";
        $argument = ['username' => 'Bob'];
        $prev_state = $this->_db->query($count_statement)->fetchColumn()[0];
        $this->_db->query($base_statement, $argument);
        $new_state = $this->_db->query($count_statement)->fetchColumn()[0];
        $this->assertEquals($prev_state + 1, $new_state);
        $this->_db = null;
    }

    /** @test */
    public function it_can_execute_a_select_query() {
        $this->_db = new SQLiteDatabase($this->_database_name);
        $base_statement = "SELECT * FROM Users WHERE username = :username";
        $argument = ['username' => 'Bob'];
        $result = $this->_db->query($base_statement, $argument)->fetchAll();
        $this->assertEquals(1, $result[0]['id']);
        $this->assertEquals('Bob', $result[0]['username']);
        $this->_db = null;
    }

    /** @test */
    public function it_can_execute_an_update_query() {
        $this->_db = new SQLiteDatabase($this->_database_name);
        $base_statement = "UPDATE Users SET username = :new_username WHERE username = :username";
        $arguments = [
            'new_username' => 'Robert',
            'username' => 'Bob'
        ];
        $this->_db->query($base_statement, $arguments);

        $result = $this->_db->query("SELECT Count(*) FROM Users WHERE username = 'Robert'")->fetchColumn();
        $this->assertEquals(1, $result[0]);

        $result = $this->_db->query("SELECT Count(*) FROM Users WHERE username = 'Bob'")->fetchColumn();
        $this->assertEquals(0, $result[0]);

        $this->_db = null;
    }

    /** @test */
    public function it_can_execute_a_delete_query() {
        $this->_db = new SQLiteDatabase($this->_database_name);
        $base_statement = "DELETE FROM Users WHERE username = :username";
        $argument = ['username' => 'Robert'];

        $this->_db->query($base_statement, $argument);
        $result = $this->_db->query("SELECT Count(*) FROM Users")->fetchColumn();

        $this->assertEquals(0, $result[0]);

        $this->_db = null;
    }

    //Removes the database file to ensure predictable tests.
    public static function tearDownAfterClass() {
        $file = __DIR__ . '\test.db';
        unlink($file);
    }
}