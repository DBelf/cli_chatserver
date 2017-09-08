<?php
/**
 * Short description for file
 *
 * Long description for file (if any)...
 *
 * @package    bunq_assignment
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

     public function get($arguments = []) {
        if (count($arguments) < 1) {
            $this->result_array['users'] = $this->get_all();
        }
        else {
            $this->result_array['users'][] = $this->get_single($arguments);
        }
    }

    private function get_single($arguments = []) {
        $result = $this->dbh->query($this->query_array['get'], $arguments)->fetchAll()[0];
        $user = new User($result['id'], $result['username']);
        return $user->to_array();
    }

    private function get_all() {
        $result = $this->dbh->query($this->query_array['get_all'], array())->fetchAll();
        $users = [];
        foreach($result as $row) {
            $user = new User($row['id'], $row['username']);
            $users[] = $user->to_array();
        }
        return $users;
    }

    public function post($arguments = []) {
        try {
            $this->dbh->query($this->query_array['post'], $arguments);
            $last_id = $this->dbh->get_last_insert_id();
            $this->result_array['user_id'] = $last_id;
        } catch (PDOException $e) {
            //We've got a duplicate entry for the username.
            if ($e->errorInfo[1] == 19) {
                $this->result_array['ok'] = false;
                $this->result_array['error'] = 'Duplicate entry found';
            } else {
                echo $e->getMessage();
            }
        }
    }

    public function put($arguments = []) {
        try {
            $this->dbh->query($this->query_array['put'], $arguments);
            $this->result_array['new_username'] = $arguments['new_username'];
        } catch(PDOException $e) {
            //We've got a duplicate entry for the username.
            if ($e->errorInfo[1] == 19) {
                $this->result_array['ok'] = false;
                $this->result_array['error'] = 'Duplicate entry found';
            } else {
                echo $e->getMessage();
            }
        }
    }

    public function delete($arguments = []) {
        $this->dbh->query($this->query_array['delete'], $arguments);
    }
}