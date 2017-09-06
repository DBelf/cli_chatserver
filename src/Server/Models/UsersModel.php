<?php
/**
 * Short description for file
 *
 * Long description for file (if any)...
 *
 * @package    bunq_assignment
 * @author     Dimitri
 */

namespace ChatApplication\Server\Models;

require_once(__DIR__ . '/../../../vendor/autoload.php');

use ChatApplication\Server\DatabaseService\DatabaseService;
use ChatApplication\DataWrappers\User;
use PDOException;
use function print_r;

class UsersModel implements Model
{
    protected $_dbh;
    protected $_result_array = ["ok" => true];
    protected $query_array = [
        'get' => "SELECT * FROM Users WHERE username = :username",
        'get_all' => "SELECT * FROM Users",
        'post' => "INSERT INTO Users (username) VALUES(:username)",
        'put' => "UPDATE Users SET username = :new_username WHERE username = :old_username",
        'delete' => "DELETE FROM Users WHERE username = :username"
    ];


    public function __construct(DatabaseService $dbh) {
        $this->_dbh = $dbh;
    }

    public function get($arguments = []) {
        if (count($arguments) < 1) {
            $this->_result_array['users'] = $this->get_all();
        }
        else {
            $this->_result_array['users'] = $this->get_single($arguments);
        }
    }

    private function get_single($arguments) {
        $result = $this->_dbh->query($this->query_array['get'], $arguments)->fetchAll()[0];
        $user = new User($result['id'], $result['username']);
        return $user;
    }

    private function get_all() {
        $result = $this->_dbh->query($this->query_array['get_all'])->fetchAll();
        $users = [];
        foreach($result as $row) {
            $users[] = new User($row['id'], $row['username']);
        }
        return $users;
    }

    public function post($arguments) {
        try {
            $this->_dbh->query($this->query_array['post'], $arguments);
            $last_id = $this->_dbh->get_last_insert_id();
            $this->_result_array['user_id'] = $last_id;
        } catch (PDOException $e) {
            //We've got a duplicate entry for the username.
            if ($e->errorInfo[1] == 19) {
                $this->_result_array['ok'] = false;
                $this->_result_array['error'] = "Duplicate entry found";
            } else {
                echo $e->getMessage();
            }
        }
    }

    public function put($arguments) {
        try {
            $this->_dbh->query($this->query_array['put'], $arguments);
            $this->_result_array['new_username'] = $arguments['new_username'];
        } catch(PDOException $e) {
            //We've got a duplicate entry for the username.
            if ($e->errorInfo[1] == 19) {
                $this->_result_array['ok'] = false;
                $this->_result_array['error'] = "Duplicate entry found";
            } else {
                echo $e->getMessage();
            }
        }
    }

    public function delete($arguments) {
        $this->_dbh->query($this->query_array['delete'], $arguments);
    }

    public function get_results_array() {
        return $this->_result_array;
    }
}