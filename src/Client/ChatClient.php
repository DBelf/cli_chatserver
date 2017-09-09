<?php
/**
 * Short description for file
 *
 * Long description for file (if any)...
 *
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
     * @param $remote_request RemoteRequest
     * @return bool
     */
    public function prompt_user_for_username($remote_request) {
        echo 'Enter username:' . PHP_EOL;
        $handle = fopen($this->input_stream, 'r');
        $payload = ['username' => trim(fgets($handle))];
        $result = json_decode($remote_request->post_to_endpoint('/users', $payload), true);
        fclose($handle);
        if (!$result['ok']) {
            echo $result['error'];
            return false;
        }
        $this->username = $result['username'];
        return true;
    }

    /**
     * @param $remote_request RemoteRequest
     * @return bool
     */
    public function poll_for_unread_messages($remote_request) {
        $payload = ['receiver' => $this->username];
        $result = json_decode($remote_request->get_from_endpoint('/messages', $payload), true);

        $messages = $this->construct_messages_list($result['messages']);
        foreach ($messages as $message) {
            $message->display();
        }
        return true;
    }

    private function construct_messages_list($result) {
        $messages = [];
        foreach ($result as $message) {
            $messages[] = new Message(
                $message['message_id'], $message['sender_name'], $message['timestamp'], $message['body']);
        }
        return $messages;
    }
}