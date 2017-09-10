<?php
/**
 * Retrieves a list of one or all connected users from the server.
 * If the arguments array contains empty strings, all users will be retrieved.
 * Otherwise the information of the username specified in the first element of the arguments array is retrieved.
 *
 * @package    bunq_assignment
 * @author     Dimitri
 */

namespace ChatApplication\Client\ChatCommands;

use ChatApplication\Client\RemoteRequest;
use ChatApplication\Models\User;
use const PHP_EOL;

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
     * Executes the command.
     * Polls the server for the user specified in the first element of the arguments array.
     * Or polls the server for all users in the Users table if no user was specified.
     * Displays an error if the requested user doesn't exist.
     *
     * @param $username
     * @return boolean true if the request was successful, false if anything went wrong.
     */
    public function execute($username) {
        $payload = [];
        if ($this->arguments[0] !== '') {
            $payload = [
                'username' => $this->arguments[0],
            ];
        }
        //Decodes the server response.
        $result = json_decode($this->remote_request->get_from_endpoint('/users', $payload), true);
        //Displays the error if anything went wrong.
        if (!$result['ok']) {
            echo 'Username doesn\'t exist!' . PHP_EOL;
            return false;
        }
        $users = $this->construct_user_list($result['users']);
        //Prints all retrieved users on the CLI.
        foreach ($users as $user) {
            echo (string)$user;
        }
        return true;
    }

    /**
     * @param $users_list array containing the users sent back from the server.
     * @return array of User objects. @see User.
     */
    private function construct_user_list($users_list) {
        $users = [];
        foreach ($users_list as $user) {
            $users[] = new User($user['id'], $user['username']);
        }
        return $users;
    }
}