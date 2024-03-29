<?php
/**
 * Tests the SQLiteDatabase. @see SQLiteDatabase
 *
 * A SQLiteDatabase should initialze the database with three tables if they don't exist.
 * A SQLiteDatabase should return the id of the last inserted row.
 * A SQLiteDatabase can execute insert, select, update and delete queries and return the result of the query.
 *
 * @package    chat_server
 * @author     Dimitri
 */

namespace ChatApplication\tests;

require_once(__DIR__ . '/../../vendor/autoload.php');

use ChatApplication\Server\DatabaseService\SQLiteDatabase;
use PHPUnit\Framework\TestCase;
use function unlink;

class SQLiteDatabaseTest extends TestCase
{
    /**
     * @var SQLiteDatabase
     */
    protected $db;
    protected $database_name = 'src/tests/test_sql.db';

    protected function setUp() {
        $this->db = new SQLiteDatabase($this->database_name);
    }

    protected function tearDown() {
        $this->db = null;
    }

    /** @test */
    public function it_can_initialize_a_db_with_three_tables() {
        //Arrange.
        $base_statement = 'SELECT Count(*) FROM sqlite_master WHERE type=\'table\'';
        //execute the query.
        $result = $this->db->query($base_statement)->fetchColumn(0);
        //Assert on 4 Tables because sqlite_sequence is also returned.
        $this->assertEquals(4, $result);
    }

    /** @test */
    public function it_can_return_the_id_of_the_last_insert() {
        //Assert.
        $this->assertEquals(0, $this->db->get_last_insert_id());
    }

    /** @test */
    public function it_can_execute_an_insert_query() {
        //Arrange the query.
        $base_statement = 'INSERT INTO Users (username) VALUES(:username)';
        $count_statement = 'SELECT Count(*) FROM Users';
        $argument = ['username' => 'Bob'];
        $prev_state = $this->db->query($count_statement)->fetchColumn(0);
        //Execute the query.
        $this->db->query($base_statement, $argument);
        $new_state = $this->db->query($count_statement)->fetchColumn(0);
        //Assert a row was added to the database.
        $this->assertEquals($prev_state + 1, $new_state);
    }

    /** @test */
    public function it_can_execute_a_select_query() {
        //Arrange the query.
        $base_statement = 'SELECT * FROM Users WHERE username = :username';
        $argument = ['username' => 'Bob'];
        //Execute the query.
        $result = $this->db->query($base_statement, $argument)->fetchAll();
        //Assert the select statement returns the correct values.
        $this->assertEquals(1, $result[0]['id']);
        $this->assertEquals('Bob', $result[0]['username']);
    }

    /** @test */
    public function it_can_execute_an_update_query() {
        //Arrange the query.
        $base_statement = 'UPDATE Users SET username = :new_username WHERE username = :username';
        $arguments = [
            'new_username' => 'Robert',
            'username' => 'Bob'
        ];
        //Execute the query.
        $this->db->query($base_statement, $arguments);
        //Assert the new username exists.
        $result = $this->db->query('SELECT Count(*) FROM Users WHERE username = \'Robert\'')->fetchColumn();
        $this->assertEquals(1, $result[0]);
        //Assert the old username doesn't exist anymore.
        $result = $this->db->query('SELECT Count(*) FROM Users WHERE username = \'Bob\'')->fetchColumn();
        $this->assertEquals(0, $result[0]);
    }

    /** @test */
    public function it_can_execute_a_delete_query() {
        //Arrange the query.
        $base_statement = 'DELETE FROM Users WHERE username = :username';
        $argument = ['username' => 'Robert'];
        //Execute the query.
        $this->db->query($base_statement, $argument);
        $result = $this->db->query('SELECT Count(*) FROM Users')->fetchColumn();
        //Assert whether the Users table is empty.
        $this->assertEquals(0, $result[0]);
    }

    //Removes the database file to ensure predictable tests.
    public static function tearDownAfterClass() {
        $file = __DIR__ . '/test_sql.db';
        unlink($file);
    }
}