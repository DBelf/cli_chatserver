<?php
/**
 * Short description for file
 *
 * Long description for file (if any)...
 *
 * @package    bunq_assignment
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
     * @param $username
     * @return mixed
     */
    public function execute($username) {
        if (!count($this->arguments) === 2) {
            echo 'Need a username and a body to send a message' . PHP_EOL;
        }

        $payload = [
            'sender_name' => $username,
            'receiver_name' => $this->arguments[0],
            'body' => $this->arguments[1]
        ];
        $result = json_decode($this->remote_request->post_to_endpoint('/messages', $payload), true);
        if (!$result['ok']) {
            echo $result['error'] . PHP_EOL;
            return false;
        }
        return true;
    }

}