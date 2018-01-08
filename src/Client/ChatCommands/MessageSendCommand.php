<?php
/**
 * Sends a message to the server.
 * Each message has a recipient and a sender.
 *
 * @package    chat_server
 * @author     Dimitri
 */

namespace ChatApplication\Client\ChatCommands;

use ChatApplication\Client\RemoteRequest;
use const PHP_EOL;

class MessageSendCommand implements ChatCommand
{
    private $remote_request;
    private $arguments;

    /**
     * MessageSendCommand constructor.
     * @param $remote_request RemoteRequest
     * @param $arguments
     */
    public function __construct($remote_request, $arguments) {
        $this->remote_request = $remote_request;
        $this->arguments = $arguments;
    }

    /**
     * Executes the command and sends a request to the server to add the message to Messages and Unread table.
     * A message body and recipient are necessary.
     *
     * @param $username
     * @return boolean true if sending the message was successful, false if sending failed.
     */
    public function execute($username) {
        if (count($this->arguments) !== 2) {
            echo 'Need a username and body to send a message!' . PHP_EOL;
            return false;
        }
        $payload = [
            'sender_name' => $username,
            'receiver_name' => $this->arguments[0],
            'body' => $this->arguments[1]
        ];
        //Decodes the server response.
        $result = json_decode($this->remote_request->post_to_endpoint('/messages', $payload), true);
        //Displays the error if anything went wrong.
        if (!$result['ok']) {
            echo $result['error'] . PHP_EOL;
            return false;
        }
        return true;
    }

}