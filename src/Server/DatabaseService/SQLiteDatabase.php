<?php
/**
 * Short description for file
 *
 * Long description for file (if any)...
 *
 * @package    bunq_assignment
 * @author     Dimitri
 */

namespace ChatApplication\Server\DatabaseService;

use \PDO;
use PDOException;

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

    public function start_transaction() {
        $this->dbh->beginTransaction();
    }

    public function commit() {
        $this->dbh->commit();
    }

    public function roll_back() {
        $this->dbh->rollBack();
    }

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
              sender INTEGER,
              receiver INTEGER,
              body VARCHAR,
              timestamp INTEGER);",
            "CREATE TABLE IF NOT EXISTS Unread (
              user_id INTEGER,
              message_id INTEGER,
              FOREIGN KEY(user_id) REFERENCES Users(id),
              FOREIGN KEY(message_id) REFERENCES Messages(id));"
        );
        try {
            $this->dbh->beginTransaction();

            foreach ($tables as $table) {
                $this->statement = $this->dbh->prepare($table);
                $this->statement->execute();
            }
            $this->dbh->commit();
        } catch (PDOException $e) {
            echo $e->getMessage();
            $this->dbh->rollBack();
        }
    }

    public function query($statement, $arguments = array()) {
        $this->statement = $this->dbh->prepare($statement);
        $this->statement->execute($arguments);
        return $this->statement;
    }

    public function get_last_insert_id() {
        return $this->dbh->lastInsertId();
    }
}