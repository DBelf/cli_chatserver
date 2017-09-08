<?php
/**
 * Implementation of the Abstract Controller in charge of querying the database for messages.
 * @see AbstractController
 *
 * The MessageController supports get and post HTTP verbs.
 * Both of these actions require arguments to function and will return a response array
 * with the ok value set to false and an error message.
 *
 * The get verb will retrieve all unread messages from the Messages table with a receiver id corresponding to the
 * argument provided.
 * The post verb will insert the message into the Messages table and Unread table.
 *
 * Because the functionality of both HTTP verbs is reliant on the Unread table, this Controller can also
 * query the Unread table.
 *
 * @package    bunq_assignment
 * @author     Dimitri
 */

namespace ChatApplication\Server\Controllers;

require_once(__DIR__ . '/../../../vendor/autoload.php');

use ChatApplication\Models\Message;
use PDOException;

class MessagesController extends AbstractController
{
    /**
     * @var array
     */
    private $query_array = [
        'get' => 'SELECT m.id, m.receiver, m.body, m.timestamp, u.username AS sender_name 
                    FROM Messages m 
                    INNER JOIN Users u ON u.id = m.sender
                    INNER JOIN Unread ur ON ur.message_id = m.id
                    WHERE m.receiver = :receiver_id',
        'post_message' => 'INSERT INTO Messages (sender, receiver, body, timestamp)
                          VALUES(:sender_id, :receiver_id, :body, CURRENT_TIMESTAMP)',
        'post_unread' => 'INSERT INTO Unread (user_id, message_id) VALUES(:user_id, :message_id)'
    ];

    /**
     * Queries the Unread table for unread messages for the user_id provided in the arguments array.
     * If any rows are found, the corresponding messages will be fetched from the Messages table
     * and added to the result_array.
     *
     * If the query fails, the result_array will be updated with the corresponding error message.
     *
     * @param array $arguments contains the user_id of the receiver of the messages.
     * @return void
     */
    public function get($arguments = []) {
        //The query needs a user_id to send to the database.
        if (count($arguments) < 1) {
            $this->no_argument();
            return;
        }
        //Executes the query and fetches the results.
        $result = $this->dbh->query($this->query_array['get'], $arguments)->fetchAll();
        $this->result_array['messages'] = [];
        //Adds all messages to the result_array if there are multiple unread messages.
        foreach ($result as $row) {
            $message = new Message($row['id'], $row['sender_name'], $row['timestamp'], $row['body']);
            //Converts each message object to a key, value array represenataion.
            $this->result_array['messages'][] = $message->to_array();
        }
    }

    /**
     * Posts the message to the Messages table and updates the Unread table to also
     * point to the recently added message.
     * Both of these queries are done in a single transaction to maintain consistency of the
     * database.
     *
     * If the query fails, the result_array will be updated with the corresponding error message.
     *
     * @param array $arguments contains the id of the receiver, the id of the sender,
     * the body of the message and the timestamp of the message.
     * @return void
     */
    public function post($arguments = []) {
        //The query needs arguments to send to the database.
        if (count($arguments) < 1) {
            $this->no_argument();
            return;
        }
        try {
            $this->dbh->start_transaction();
            //Queries the Messages table.
            $this->dbh->query($this->query_array['post_message'], $arguments);
            $message_id = $this->dbh->get_last_insert_id();
            $unread_arguments = [
                'user_id' => $arguments['receiver_id'],
                'message_id' => $message_id
            ];
            //Queries the Unread table.
            $this->post_to_unread($unread_arguments);
            $this->dbh->commit();
        } catch (PDOException $e) {
            //If the transaction failed, the results array is updated.
            $this->result_array['ok'] = false;
            $this->result_array['error'] = $e->getMessage();
            $this->dbh->roll_back();
        }
    }

    /**
     * Used by the post method to also add a message to the Unread table.
     * @see MessagesController::post()
     * @param $arguments mixed contains the id of the receiver of the message and the id of the new message.
     */
    private function post_to_unread($arguments) {
        $this->dbh->query($this->query_array['post_unread'], $arguments);
    }

}