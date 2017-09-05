<?php
/**
 * A PDO wrapper used for the database used by the server to store and retrieve messages.
 * Used to create a Singleton Database intended for this project only.
 *
 * The database consists of three tables:
 *
 * Users table
 * ------------------
 * id username
 *
 * Messages table
 * ------------------
 * id sender receiver body timestamp
 *
 * Unread table
 * ------------------
 * user_id message_id
 *
 *
 * Users are added with their usernames.
 * Messages are added by keeping track of the sender, recipient, body and timestamp.
 * The recipient and message id are used to track all the unread messages of a user.
 *
 * @package    ChatAssignment
 * @author     Dimitri
 */

//https://code.tutsplus.com/tutorials/why-you-should-be-using-phps-pdo-for-database-access--net-12059

namespace ChatApplication\Server;

require_once(__DIR__ . '/../../vendor/autoload.php');

use \PDO;
use PDOException;

class DatabaseWrapper
{
    private $_dbh;
    private $_statement;

    /**
     * DatabaseWrapper constructor.
     */
    public function __construct() {
        try {
            $this->_dbh = new PDO('sqlite::memory:');
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
              username VARCHAR);",
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

    /**
     *
     * This function inserts the message into the Messages table.
     * Another call is done to the @see DatabaseWrapper::insert_unread() to insert the corresponding keys into
     * the Unread table.
     * The functionality of the queries is wrapped in a transaction because both queries have to be completed
     * to ensure the server can retrieve the list of unread messages.
     *
     * @param $sender_id the id from the Users table of the original sender of the message.
     * @param $receiver_id the id from the Users of the recipient of the message.
     * @param $body the plaintext body of the message.
     */
    public function insert_message($sender_id, $receiver_id, $body) {
        try {
            $this->_dbh->beginTransaction();
            $this->_statement = $this->_dbh->prepare(
                "INSERT INTO Messages (sender, receiver, body, timestamp)
                          VALUES(:sender_id, :receiver_id, :body, CURRENT_TIMESTAMP)"
            );
            $this->_statement->execute(array(
               'sender_id' => $sender_id,
                'receiver_id' => $receiver_id,
                'body' => $body
            ));

            $message_id = $this->_dbh->lastInsertId();
            $this->insert_unread($receiver_id, $message_id);
            $this->_dbh->commit();
        } catch (PDOException $e) {
            echo $e->getMessage();
            $this->_dbh->rollBack();
        }
    }

    /**
     *
     * This function adds an entry to the Unread table.
     *
     * @param $user_id the Users table foreign key corresponding to the unread message.
     * @param $message_id the Message table foreign key corresponding to the unread message.
     */
    public function insert_unread($user_id, $message_id) {
        $this->_statement = $this->_dbh->prepare(
            "INSERT INTO Unread (user_id, message_id) VALUES(:user_id, :message_id)"
        );
        $this->_statement->execute(array(
            'user_id' => $user_id,
            'message_id' => $message_id
        ));
    }

    /**
     *
     * Adds a user to the Users table.
     *
     * @param $username a string containing the username.
     */
    public function insert_user($username) {
        $this->_statement = $this->_dbh->prepare(
            "INSERT INTO Users (username) VALUES(:username)"
        );
        $this->_statement->execute(array(
            'username' => $username
        ));
    }

    /**
     * @param $username
     * @return mixed
     */
    public function retrieve_user_by_name($username) {
        $this->_statement = $this->_dbh->prepare(
            "SELECT * FROM Users WHERE username = :username"
        );
        $this->_statement->execute(array('username' => $username));
        return $this->_statement->fetch();
    }

    //FIXME Should only retrieve the unread messages!
    public function retrieve_unread($receiver_name) {
        $this->_statement = $this->_dbh->prepare(
            "SELECT m.id, m.receiver, m.body, m.timestamp, u.username as sender_name 
                    FROM Messages m 
                    INNER JOIN Users u ON u.id = m.sender
                    INNER JOIN Unread ur ON ur.message_id = m.id
                    WHERE m.receiver IN (SELECT id FROM Users WHERE username = :receiver_name)"
        );
        $this->_statement->execute(array('receiver_name' => $receiver_name));
        $result = $this->_statement->fetchAll();
        return $result;
    }

    /**
     *
     * Deletes a row from the Unread table.
     *
     * @param $message_id the Messages foreign key of the row that has to be removed.
     */
    public function remove_from_unread($message_id){
        $this->_statement = $this->_dbh->prepare(
            "DELETE FROM Unread WHERE message_id = :message_id"
        );
        $this->_statement->execute(array('message_id' => $message_id));
    }

    /**
     *
     * Deletes a row from the Users table.
     *
     * @param $username the username of the row that has to be removed from the Users table.
     */
    public function delete_user($username) {
        $this->_statement = $this->_dbh->prepare(
            "DELETE FROM Users WHERE username = :username"
        );
        $this->_statement->execute(array('username' => $username));
    }

    /**
     * @return integer indicates the number of entries in the Users table.
     */
    public function total_users() {
        $this->_statement = $this->_dbh->prepare(
            "SELECT Count(*) FROM Users"
        );
        $this->_statement->execute();
        return $this->_statement->fetchColumn();
    }

    /**
     * @return integer indicates the number of entries in the Messages table.
     */
    public function total_messages() {
        $this->_statement = $this->_dbh->prepare(
            "SELECT Count(*) FROM Messages"
        );
        $this->_statement->execute();
        return $this->_statement->fetchColumn();
    }

    /**
     * @return integer indicates the number of entries in the Unread table.
     */
    public function total_unread() {
        $this->_statement = $this->_dbh->prepare(
            "SELECT Count(*) FROM Unread"
        );
        $this->_statement->execute();
        return $this->_statement->fetchColumn();
    }


}