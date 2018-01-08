<?php
/**
 * Implementation of the Abstract Controller in charge of querying the database for messages.
 * @see AbstractController
 *
 * The UsersController supports get, post, put and delete HTTP verbs.
 *
 * The get verb will retrieve a list of all users in the Users table if no argument was provided.
 * The get verb will retrieve a single user from the Users table if an argument was provided.
 * The post verb will insert a new user into the Users table with the provided username.
 * The put verb will update the row of the user with the corresponding username in the Users table..
 * The delete verb will remove the row of the user with the corresponding username from the Users table.
 *
 * @package    chat_server
 * @author     Dimitri
 */

namespace ChatApplication\Server\Controllers;

require_once(__DIR__ . '/../../../vendor/autoload.php');

use ChatApplication\Models\User;
use PDOException;

class UsersController extends AbstractController
{
    private $query_array = [
        'get' => 'SELECT * FROM Users WHERE username = :username',
        'get_all' => 'SELECT * FROM Users',
        'post' => 'INSERT INTO Users (username) VALUES(:username)',
        'put' => 'UPDATE Users SET username = :new_username WHERE username = :old_username',
        'delete' => 'DELETE FROM Users WHERE username = :username'
    ];

    /**
     * Queries the Users table for a list of the users and adds this to the
     * result_array.
     * Dispatches to the get_single function if an argument was provided.
     * @see UsersController::get_single()
     *
     * Dispatches to the get_all function if no argument was provided.
     * @see UsersController::get_all()
     *
     * @param array $arguments
     * @return void
     */
    public function get($arguments = []) {
        //If no argument was provided, all users will be retrieved.
        if (count($arguments) < 1) {
            $this->result_array['users'] = $this->get_all();
        } else {
            $this->result_array['users'][] = $this->get_single($arguments);
        }
    }

    /**
     * Queries the Users table for the row of a single user.
     *
     * @param array $arguments contains the usename of the user that should be retrieved.
     * @return array containing the User object in key, value pairs.
     */
    private function get_single($arguments = []) {
        $result = $this->dbh->query($this->query_array['get'], $arguments)->fetchAll()[0];
        $user = new User($result['id'], $result['username']);
        return $user->to_array();
    }

    /**
     * Queries the Users table for all the rows.
     *
     * @return array containing the User objects of all users in the Users table in key, value pairs.
     */
    private function get_all() {
        $result = $this->dbh->query($this->query_array['get_all'], array())->fetchAll();
        $users = [];
        foreach ($result as $row) {
            $user = new User($row['id'], $row['username']);
            $users[] = $user->to_array();
        }
        return $users;
    }

    /**
     * Queries the Users table to add a user.
     * Adds the id of the user to the results_array upon succes.
     *
     * Updates the results_array with the error message upon failure.
     * @param array $arguments username of the user that should be added to the Users table.
     * @return void
     */
    public function post($arguments = []) {
        try {
            //Attempt to inser the new user.
            $this->dbh->query($this->query_array['post'], $arguments);
            $last_id = $this->dbh->get_last_insert_id();
            $this->result_array['username'] = $arguments['username'];
            $this->result_array['user_id'] = $last_id;
        } catch (PDOException $e) {
            $this->result_array['ok'] = false;
            if ($e->errorInfo[1] == 19) {
                //We've got a duplicate entry for the username.
                $this->result_array['error'] = 'Username already exists!';
            } else {
                //Other error.
                $this->result_array['error'] = $e->getMessage();
            }
        }
    }

    /**
     * Queries the Users table to update the row of a user.
     * Adds the new username of the user to the results_array upon succes.
     *
     * Updates the results_array with the error message upon failure.
     * @param array $arguments new username and old username of the row that should be updated.
     * @return void
     */
    public function put($arguments = []) {
        try {
            //Attempt to update the row.
            $this->dbh->query($this->query_array['put'], $arguments);
            $this->result_array['new_username'] = $arguments['new_username'];
        } catch (PDOException $e) {
            $this->result_array['ok'] = false;
            if ($e->errorInfo[1] == 19) {
                //We've got a duplicate entry for the username.
                $this->result_array['error'] = 'Username already exists!';
            } else {
                //Other error.
                $this->result_array['error'] = $e->getMessage();
            }
        }
    }

    /**
     * Queries the database to remove a user from the Users table.
     *
     * @param array $arguments contains the username of the user which should be removed.
     * @return void
     */
    public function delete($arguments = []) {
        $this->dbh->query($this->query_array['delete'], $arguments);
    }
}