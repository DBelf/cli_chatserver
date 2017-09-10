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
use ChatApplication\Models\User;

class UsersGetCommand implements ChatCommand
{
    private $remote_request;
    private $arguments;

    /**
     * UsersGetCommand constructor.
     * @param $remote_request RemoteRequest
     * @param $arguments array
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
        $payload = [];
        if ($this->arguments[0] !== '') {
            $payload = [
                'username' => $this->arguments[0],
            ];
        }
        $result = json_decode($this->remote_request->get_from_endpoint('/users', $payload), true);
        if (!$result['ok']) {
            echo 'Username doesn\'t exist!' . PHP_EOL;
            return false;
        }
        $users = $this->construct_user_list($result['users']);
        foreach ($users as $user) {
            $user->display();
        }
        return true;
    }

    private function construct_user_list($result) {
        $users = [];
        foreach ($result as $user) {
            $users[] = new User($user['id'], $user['username']);
        }
        return $users;
    }
}