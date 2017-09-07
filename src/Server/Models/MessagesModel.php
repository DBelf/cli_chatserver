<?php
/**
 * Short description for file
 *
 * Long description for file (if any)...
 *
 * @package    bunq_assignment
 * @author     Dimitri
 */

namespace ChatApplication\Server\Models;

require_once(__DIR__ . '/../../../vendor/autoload.php');

use ChatApplication\DataWrappers\Message;
use ChatApplication\Server\DatabaseService\DatabaseService;
use PDOException;

class MessagesModel implements Model
{
    private $dbh;
    private $result_array = ['ok' => true];
    private $query_array = [
        'get' => 'SELECT m.id, m.receiver, m.body, m.timestamp, u.username as sender_name 
                    FROM Messages m 
                    INNER JOIN Users u ON u.id = m.sender
                    INNER JOIN Unread ur ON ur.message_id = m.id
                    WHERE m.receiver = :receiver_id',
        'post_message' => 'INSERT INTO Messages (sender, receiver, body, timestamp)
                          VALUES(:sender_id, :receiver_id, :body, CURRENT_TIMESTAMP)',
        'post_unread' => 'INSERT INTO Unread (user_id, message_id) VALUES(:user_id, :message_id)'
    ];

    /**
     * MessagesModel constructor.
     * @param DatabaseService $db
     */
    public function __construct(DatabaseService $db) {
        $this->dbh = $db;
    }

    public function get($arguments = []) {
        if (count($arguments) < 1) {
            $this->no_argument();
            return;
        }
        $result = $this->dbh->query($this->query_array['get'], $arguments)->fetchAll();
        $this->result_array['messages'] = [];
        foreach ($result as $row) {
            $message = new Message($row['id'], $row['sender_name'], $row['timestamp'], $row['body']);
            $this->result_array['messages'][] = $message->to_array();
        }
    }

    public function post($arguments) {
        if (count($arguments) < 1) {
            $this->no_argument();
            return;
        }
        try {
            $this->dbh->start_transaction();
            $this->dbh->query($this->query_array['post_message'], $arguments);
            $message_id = $this->dbh->get_last_insert_id();
            $unread_arguments = [
                'user_id' => $arguments['receiver_id'],
                'message_id' => $message_id
            ];
            $this->post_to_unread($unread_arguments);
            $this->dbh->commit();
        } catch (PDOException $e) {
            echo $e->getMessage();
            $this->dbh->roll_back();
        }
    }

    private function post_to_unread($arguments) {
        $this->dbh->query($this->query_array['post_unread'], $arguments);
    }

    public function put($arguments = []) {
        $this->not_implemented();
    }

    public function delete($arguments = []) {
        $this->not_implemented();
    }

    private function not_implemented() {
        $this->result_array['ok'] = false;
        $this->result_array['error'] = 'Method not implemented!';
    }

    private function no_argument() {
        $this->result_array['ok'] = false;
        $this->result_array['error'] = 'No argument supplied!';
    }

    /**
     * @return mixed
     */
    public function get_result_array() {
        return $this->result_array;
    }

}