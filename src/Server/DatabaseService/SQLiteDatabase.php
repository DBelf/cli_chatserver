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
use function print_r;

class SQLiteDatabase implements DatabaseService
{
    private $_dbh;
    private $_statement;

    public function __construct($database_path) {
        try {
            $this->_dbh = new PDO('sqlite:' . $database_path);
            $this->_dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->_dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
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
              FOREIGN KEY(message_id) REFERENCES MessagesModel(id));"
        );
        try {
            $this->_dbh->beginTransaction();

            foreach ($tables as $table) {
                $this->_statement = $this->_dbh->prepare($table);
                $this->_statement->execute();
            }
            $this->_dbh->commit();
        } catch (PDOException $e) {
            echo $e->getMessage();
            $this->_dbh->rollBack();
        }
    }

    public function query($statement, $arguments = array()) {
        $this->_statement = $this->_dbh->prepare($statement);
        $this->_statement->execute($arguments);
        return $this->_statement;
    }

    public function get_last_insert_id() {
        return $this->_dbh->lastInsertId();
    }
}