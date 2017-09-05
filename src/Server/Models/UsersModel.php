<?php
/**
 * Created by PhpStorm.
 * User: dimitri
 * Date: 05/09/2017
 * Time: 21:48
 */

namespace ChatApplication\Server\Models;


class UsersModel implements Model
{
    protected $_dbh;

    public function __construct($dbh) {
        $this->_dbh = $dbh;

    }

    private function connect() {
        $this->_dbh->connect();
    }

    private function disconnect() {
        $this->_dbh = null;
    }

    public function get($json = '[]') {

        // TODO: Implement get() method.
    }

    public function post($json) {
        $this->connect();
        $this->_statement = $this->_dbh->prepare(
            "INSERT INTO Users (username) VALUES(:username)"
        );
        $this->_statement->execute(array(
            'username' => $json['username']
        ));
        $this->disconnect();
    }

    public function put($json) {
        // TODO: Implement put() method.
    }

    public function delete($json) {
        // TODO: Implement delete() method.
    }

}