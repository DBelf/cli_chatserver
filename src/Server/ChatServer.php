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

require_once(__DIR__ . '/../../vendor/autoload.php');

use ChatApplication\Server\Controllers\Controller;
use ChatApplication\Server\DatabaseService\SQLiteDatabase;
use function class_exists;
use function method_exists;

class ChatServer
{
    /**
     * @var SQLiteDatabase
     */
    private $dbh;
    /**
     * @var Request
     */
    private $request;
    /**
     * @var Controller
     */
    private $controller;
    /**
     * @var Response
     */
    private $response;

    public function __construct($database_path) {
        $this->dbh = new SQLiteDatabase($database_path);
    }

    private function load_controller() {
        $controller_name = '\\ChatApplication\\Server\\Controllers\\'.$this->request->get_endpoint() . "Controller";
        if (class_exists($controller_name)) {
            $this->controller = new $controller_name($this->dbh);
        } else {
            return false;
        }
        return true;
    }

    public function handle() {
        $this->request = new Request($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
        //short-circuits so method_exists won't be called if the controller isn't loaded,
        if ($this->load_controller() && method_exists($this->controller, $this->request->get_verb())) {
            $this->controller->{$this->request->get_verb()}($this->request->get_payload());
            $data = $this->controller->get_result_array();
            $this->response = new Response(200, $data);
        } else {
            $message = sprintf('Page %s not found!', $this->request->get_endpoint());
            $this->response = new Response(404, $message);
        }
        $this->response->send();
    }
}