<?php
/**
 * Chatclients are in charge of requesting a username from the user.
 * Chatclients can also poll the server for new unread messages.
 *
 * ChatClient objects are used by the CLIChatClientApp. @see CLIChatClientApp
 * @package    bunq_assignment
 * @author     Dimitri
 */

namespace ChatApplication\Client;

use ChatApplication\Models\Message;
use const PHP_EOL;
use function json_decode;

class ChatClient
{
    private $username = '';
    private $input_stream;

    public function __construct($input_stream = 'php://stdin') {
        $this->input_stream = $input_stream;
    }

    /**
     * @return string
     */
    public function get_username() {
        return $this->username;
    }

    /**
     * Prompts the user to enter a username.
     * Sends a request to the server to add the selecte username to the Users table.
     * Displays the error if the username already exists.
     *
     * @param $remote_request RemoteRequest
     * @return bool true if the username is added, false if the username already exists.
     */
    public function prompt_user_for_username($remote_request) {
        echo 'Enter username:' . PHP_EOL;
        //Read from stdin.
        $handle = fopen($this->input_stream, 'r');
        $payload = ['username' => trim(fgets($handle))];
        //Decodes the response from the server.
        $result = json_decode($remote_request->post_to_endpoint('/users', $payload), true);
        fclose($handle);
        //Displays the error if anything went wrong.
        if (!$result['ok']) {
            echo $result['error'] . PHP_EOL;
            return false;
        }
        $this->username = $result['username'];
        return true;
    }

    /**
     * Polls the server for pending unread messages.
     * If any messages are found, a new message list will be constructed.
     * The messages are then displayed on the CLI.
     * A request to remove the corresponding row from the Unread table is read for each message displayed.
     *
     * @param $remote_request RemoteRequest
     * @return bool
     */
    public function poll_for_unread_messages($remote_request) {
        $payload = ['receiver' => $this->username];
        //Decodes the response from the server.
        $result = json_decode($remote_request->get_from_endpoint('/messages', $payload), true);

        $messages = $this->construct_messages_list($result['messages']);
        foreach ($messages as $message) {
            echo $message;
            $this->server_delete_unread($message->get_id(), $remote_request);
        }
        return true;
    }

    /**
     * Sends a request to the server to delete a row from the Unread table.
     *
     * @param $message_id integer
     * @param $remote_request RemoteRequest
     */
    private function server_delete_unread($message_id, $remote_request) {
        $payload = ['message_id' => $message_id];
        $remote_request->delete_from_endpoint('/unread', $payload);
    }

    /**
     * @param $message_list
     * @return array of Message objects. @see Message.
     */
    private function construct_messages_list($message_list) {
        $messages = [];
        foreach ($message_list as $message) {
            $messages[] = new Message(
                $message['message_id'], $message['sender_name'], $message['timestamp'], $message['body']);
        }
        return $messages;
    }
}