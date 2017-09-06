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

use ChatApplication\Server\DatabaseService\DatabaseService;
use PDOException;

class MessagesModel implements Model
{
    private $db;
    private $result_array = ['ok' => true];
    private $query_array = [
        'get' => 'SELECT m.id, m.receiver, m.body, m.timestamp, u.username as sender_name 
                    FROM Messages m 
                    INNER JOIN Users u ON u.id = m.sender
                    INNER JOIN Unread ur ON ur.message_id = m.id
                    WHERE m.receiver IN (SELECT id FROM Users WHERE username = :receiver_name)',
        'post_message' => 'INSERT INTO Messages (sender, receiver, body, timestamp)
                          VALUES(:sender_id, :receiver_id, :body, CURRENT_TIMESTAMP)',
        'post_unread' => 'INSERT INTO Unread (user_id, message_id) VALUES(:user_id, :message_id)',
        'delete' => 'DELETE FROM Unread WHERE message_id = :message_id'
    ];

    /**
     * MessagesModel constructor.
     * @param DatabaseService $db
     */
    public function __construct(DatabaseService $db) {
        $this->db = $db;
    }

    public function get($arguments = []) {

    }

    public function post($arguments) {
        try {
            $this->db->start_transaction();
            $this->db->query($this->query_array['post_message'], $arguments);
            $message_id = $this->db->get_last_insert_id();
            $unread_arguments = [
                'user_id' => $arguments['receiver_id'],
                'message_id' => $message_id
            ];
            $this->post_to_unread($unread_arguments);
            $this->db->commit();
        } catch (PDOException $e) {
            echo $e->getMessage();
            $this->db->roll_back();
        }
    }

    private function post_to_unread($arguments) {
        $this->db->query($this->query_array['post_unread'], $arguments);
    }

    public function put($arguments) {
        // TODO: Implement put() method.
    }

    public function delete($arguments) {
        // TODO: Implement delete() method.
    }

    /**
     * @return mixed
     */
    public function get_result_array() {
        return $this->result_array;
    }

}