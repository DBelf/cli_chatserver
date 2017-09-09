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
        // TODO: Implement execute() method.
    }

}