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
    protected $_database_name = 'src/tests/test_sql.db';

    protected function setUp() {
        $this->_db = new SQLiteDatabase($this->_database_name);
    }

    protected function tearDown() {
        $this->_db = null;
    }

    /** @test */
    public function it_can_initialize_a_db_with_three_tables() {
        $base_statement = "SELECT Count(*) FROM sqlite_master WHERE type='table'";
        $result = $this->_db->query($base_statement)->fetchColumn()[0];
        //Assert on 4 because sqlite_sequence is also returned.
        $this->assertEquals(4, $result);
    }

    /** @test */
    public function it_can_return_the_id_of_the_last_insert() {
        $this->assertEquals(0, $this->_db->get_last_insert_id());
    }

    /** @test */
    public function it_can_execute_an_insert_query() {
        $base_statement = "INSERT INTO Users (username) VALUES(:username)";
        $count_statement = "SELECT Count(*) FROM Users";
        $argument = ['username' => 'Bob'];
        $prev_state = $this->_db->query($count_statement)->fetchColumn()[0];
        $this->_db->query($base_statement, $argument);
        $new_state = $this->_db->query($count_statement)->fetchColumn()[0];
        $this->assertEquals($prev_state + 1, $new_state);
    }

    /** @test */
    public function it_can_execute_a_select_query() {
        $base_statement = "SELECT * FROM Users WHERE username = :username";
        $argument = ['username' => 'Bob'];
        $result = $this->_db->query($base_statement, $argument)->fetchAll();
        $this->assertEquals(1, $result[0]['id']);
        $this->assertEquals('Bob', $result[0]['username']);
    }

    /** @test */
    public function it_can_execute_an_update_query() {
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
    }

    /** @test */
    public function it_can_execute_a_delete_query() {
        $base_statement = "DELETE FROM Users WHERE username = :username";
        $argument = ['username' => 'Robert'];

        $this->_db->query($base_statement, $argument);
        $result = $this->_db->query("SELECT Count(*) FROM Users")->fetchColumn();

        $this->assertEquals(0, $result[0]);
    }

    //Removes the database file to ensure predictable tests.
    public static function tearDownAfterClass() {
        $file = __DIR__ . '\test_sql.db';
        unlink($file);
    }
}