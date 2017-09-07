<?php
/**
 * Short description for file
 *
 * Long description for file (if any)...
 *
 * @package    bunq_assignment
 * @author     Dimitri
 */

namespace ChatApplication\Server;

use ChatApplication\Server\DatabaseService\SQLiteDatabase;
use function class_exists;

class ChatServer
{
    private $dbh;
    private $request;
    private $controller;

    public function __construct($database_path) {
        $this->dbh = new SQLiteDatabase($database_path);
    }

    private function load_controller(){
        if (class_exists($this->request->endpoint)){
            $this->controller = new $this->request->endpoint($this->dbh);
        } else {
            return false;
        }
        return true;
    }

    public function handle() {
        $this->request = new Request($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);

    }
}