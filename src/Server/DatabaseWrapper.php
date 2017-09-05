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
    private $dbh;
    private $statement;

    /**
     * DatabaseWrapper constructor.
     */
    public function __construct() {
        try {
            $this->dbh = new PDO('sqlite::memory:');
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
            $this->dbh->beginTransaction();
            $this->statement = $this->dbh->prepare(
                "INSERT INTO Messages (sender, receiver, body, timestamp)
                          VALUES(:sender_id, :receiver_id, :body, CURRENT_TIMESTAMP)"
            );
            $this->statement->execute(array(
               'sender_id' => $sender_id,
                'receiver_id' => $receiver_id,
                'body' => $body
            ));

            $message_id = $this->dbh->lastInsertId();
            $this->insert_unread($receiver_id, $message_id);
            $this->dbh->commit();
        } catch (PDOException $e) {
            echo $e->getMessage();
            $this->dbh->rollBack();
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
        $this->statement = $this->dbh->prepare(
            "INSERT INTO Unread (user_id, message_id) VALUES(:user_id, :message_id)"
        );
        $this->statement->execute(array(
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
        $this->statement = $this->dbh->prepare(
            "INSERT INTO Users (username) VALUES(:username)"
        );
        $this->statement->execute(array(
            'username' => $username
        ));
    }

    /**
     * @param $username
     * @return mixed
     */
    public function retrieve_user_by_name($username) {
        $this->statement = $this->dbh->prepare(
            "SELECT * FROM Users WHERE username = :username"
        );
        $this->statement->execute(array('username' => $username));
        return $this->statement->fetch();
    }

    //FIXME Should only retrieve the unread messages!
    public function retrieve_unread($receiver_name) {
        try {
            $this->dbh->beginTransaction();
            $this->statement = $this->dbh->prepare(
                "SELECT m.id, m.receiver, m.body, m.timestamp, u.username as sender_name 
                        FROM Messages m 
                        INNER JOIN Users u ON u.id = m.sender
                        INNER JOIN Unread ur ON ur.message_id = m.id
                        WHERE m.receiver IN (SELECT id FROM Users WHERE username = :receiver_name)"
            );
            $this->statement->execute(array('receiver_name' => $receiver_name));
            $result = $this->statement->fetchAll();
            foreach($result as $row) {
                $this->remove_from_unread($row['id']);
            }
            $this->dbh->commit();
            return $result;
        } catch (PDOException $e) {
            echo $e->getMessage();
            $this->dbh->rollBack();
        }
    }

    /**
     *
     * Deletes a row from the Unread table.
     *
     * @param $message_id the Messages foreign key of the row that has to be removed.
     */
    private function remove_from_unread($message_id){
        $this->statement = $this->dbh->prepare(
            "DELETE FROM Unread WHERE message_id = :message_id"
        );
        $this->statement->execute(array('message_id' => $message_id));
    }

    /**
     *
     * Deletes a row from the Users table.
     *
     * @param $username the username of the row that has to be removed from the Users table.
     */
    public function delete_user($username) {
        $this->statement = $this->dbh->prepare(
            "DELETE FROM Users WHERE username = :username"
        );
        $this->statement->execute(array('username' => $username));
    }

    /**
     * @return integer indicates the number of entries in the Users table.
     */
    public function total_users() {
        $this->statement = $this->dbh->prepare(
            "SELECT Count(*) FROM Users"
        );
        $this->statement->execute();
        return $this->statement->fetchColumn();
    }

    /**
     * @return integer indicates the number of entries in the Messages table.
     */
    public function total_messages() {
        $this->statement = $this->dbh->prepare(
            "SELECT Count(*) FROM Messages"
        );
        $this->statement->execute();
        return $this->statement->fetchColumn();
    }

    /**
     * @return integer indicates the number of entries in the Unread table.
     */
    public function total_unread() {
        $this->statement = $this->dbh->prepare(
            "SELECT Count(*) FROM Unread"
        );
        $this->statement->execute();
        return $this->statement->fetchColumn();
    }


}