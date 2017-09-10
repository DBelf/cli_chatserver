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
     * @param $username
     * @return mixed
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
        $result = json_decode($this->remote_request->put_on_endpoint('/users', $payload), true);
        if (!$result['ok']) {
            echo $result['error'] . PHP_EOL;
            return false;
        }
        echo sprintf('Username successfully updated to %s!', $this->arguments[0]) . PHP_EOL;
        return true;
    }
}