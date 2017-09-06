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

class MessagesModel implements Model
{
    private $_db;
    private $_result_array = ['ok' => true];
    protected $_query_array = [
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
        $this->_db = $db;
    }

    public function get($arguments = []) {

    }

    public function post($arguments) {
        // TODO: Implement post() method.
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
        // TODO: Implement get_result_array() method.
    }

}