<?php
/**
 * Sends a request to the server to update the username of the user.
 * Fails if the new username already exists.
 *
 * @package    chat_server
 * @author     Dimitri
 */

namespace ChatApplication\Client\ChatCommands;

use ChatApplication\Client\RemoteRequest;
use const PHP_EOL;
use function sprintf;

class UsersUpdateCommand implements ChatCommand
{
    private $remote_request;
    public $arguments;

    /**
     * UsersUpdateCommand constructor.
     * @param $remote_request RemoteRequest
     * @param $arguments
     */
    public function __construct($remote_request, $arguments) {
        $this->remote_request = $remote_request;
        $this->arguments = $arguments;
    }

    /**
     * Executes the command.
     * Sends a request to the server to update the old username to the one provided in the first element of the
     * arguments array. Displays an error if the first element of the arguments array is an empty string.
     * Also fails if the new username already exists in the database.
     *
     * @param string $username the old username.
     * @return boolean true if the update was successful, false if it failed.
     */
    public function execute($username) {
        if ($this->arguments[0] === '') {
            echo 'Need a username to update!' . PHP_EOL;
            return false;
        }
        $payload = [
            'old_username' => $username,
            'new_username' => $this->arguments[0],
        ];
        //Decodes the server response.
        $result = json_decode($this->remote_request->put_on_endpoint('/users', $payload), true);
        //Displays the error if anything went wrong.
        if (!$result['ok']) {
            echo $result['error'] . PHP_EOL;
            return false;
        }
        echo sprintf('Username successfully updated to %s!', $this->arguments[0]) . PHP_EOL;
        return true;
    }
}