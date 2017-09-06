<?php
/**
 * Created by PhpStorm.
 * User: dimitri
 * Date: 05/09/2017
 * Time: 21:48
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
    protected $query_array = [
        'get' => "SELECT * FROM Users WHERE username = :username",
        'get_all' => "SELECT * FROM Users",
        'post' => "INSERT INTO Users (username) VALUES(:username)",
        'put' => "UPDATE Users SET username = :new_username WHERE username = :username",
        'delete' => "DELETE FROM Users WHERE username = :username"
    ];


    public function __construct(DatabaseService $dbh) {
        $this->_dbh = $dbh;
    }

    private function disconnect() {
        $this->_dbh = null;
    }

    public function get($arguments = []) {
        if (count($arguments) < 1) {
            return $this->get_all();
        }
        else {
            return $this->get_single($arguments);
        }
    }

    private function get_single($arguments) {
        $result = $this->_dbh->query($this->query_array['get'], $arguments)->fetchAll()[0];
        $user = new User($result['id'], $result['username']);
        $this->disconnect();
        return $user;
    }

    private function get_all() {
        $result = $this->_dbh->query($this->query_array['get_all'])->fetchAll();
        $users = [];
        foreach($result as $row) {
            $users[] = new User($row['id'], $row['username']);
        }
        $this->disconnect();
        return $users;
    }

    public function post($arguments) {
        try {
            $this->_dbh->query($this->query_array['post'], $arguments);
            $last_id = $this->_dbh->get_last_insert_id();
            $this->disconnect();
            return $last_id;
        } catch (PDOException $e) {
            $this->disconnect();
            //We've got a duplicate entry.
            if ($e->errorInfo[1] == 1062) {
                echo 'Duplicate username';
                return false;
            } else {
                echo $e->getMessage();
            }
        }
    }

    public function put($arguments) {
        // TODO: Implement put() method.
        $this->disconnect();
    }

    public function delete($arguments) {
        // TODO: Implement delete() method.
        $this->disconnect();
    }

}