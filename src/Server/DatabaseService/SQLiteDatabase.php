<?php
/**
 * An SQLite PDO wrapper, used by Controllers to query the database.
 * @see Controller and the implementations.
 *
 * @package    chat_server
 * @author     Dimitri
 */

namespace ChatApplication\Server\DatabaseService;

use PDO;
use PDOException;
use PDOStatement;

class SQLiteDatabase implements DatabaseService
{
    /**
     * @var PDO
     */
    private $dbh;
    /**
     * @var string
     */
    private $statement;

    /**
     * SQLiteDatabase constructor.
     * @param $database_path
     */
    public function __construct($database_path) {
        try {
            $this->dbh = new PDO('sqlite:' . $database_path);
            $this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
        $this->init_tables();
    }

    /**
     * Wrapper for the PDO begin transaction method.
     * @see PDO::beginTransaction()
     */
    public function start_transaction() {
        $this->dbh->beginTransaction();
    }

    /**
     * Wrapper for the PDO commit method.
     * @see PDO::commit()
     */
    public function commit() {
        $this->dbh->commit();
    }

    /**
     * Wrapper for the PDO rollBack method.
     * @see PDO::rollBack()
     */
    public function roll_back() {
        $this->dbh->rollBack();
    }

    /**
     * Used by the constructor to initialize the three tables.
     * Wraps the transaction in a try/catch block because constructing an incomplete database is useless.
     */
    private function init_tables() {
        $tables = array(
            "CREATE TABLE IF NOT EXISTS Users (
              id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
              username VARCHAR,
              UNIQUE (username));",
            "CREATE TABLE IF NOT EXISTS Messages (
              id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
              sender INTEGER NOT NULL,
              receiver INTEGER NOT NULL,
              body VARCHAR,
              timestamp INTEGER);",
            "CREATE TABLE IF NOT EXISTS Unread (
              user_id INTEGER NOT NULL,
              message_id INTEGER NOT NULL,
              FOREIGN KEY(user_id) REFERENCES Users(id),
              FOREIGN KEY(message_id) REFERENCES Messages(id));"
        );
        try {
            $this->dbh->beginTransaction();
            //Loop over the tables and add them.
            foreach ($tables as $table) {
                $this->statement = $this->dbh->prepare($table);
                $this->statement->execute();
            }
            $this->dbh->commit();
        } catch (PDOException $e) {
            //Roll back the database to a previous state if anything went wrong.
            echo $e->getMessage();
            $this->dbh->rollBack();
        }
    }

    /**
     * Wrapper for the PDO query and execute methods.
     * @see PDO::query()
     * @see PDO::execute()
     *
     * @param $statement
     * @param array $arguments
     * @return \PDOStatement
     * @see PDOStatement
     */
    public function query($statement, $arguments = array()) {
        $this->statement = $this->dbh->prepare($statement);
        $this->statement->execute($arguments);
        return $this->statement;
    }

    /**
     * Wrapper for the PDO lastInsertId method.
     * @see PDO::lastInsertId()
     *
     * @return string
     */
    public function get_last_insert_id() {
        return $this->dbh->lastInsertId();
    }
}