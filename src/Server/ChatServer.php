<?php
/**
 * A ChatServer is in charge of "routing" the request and payload to the corresponding Controller.
 * A ChatServer will relay a response back to the client with an HTTP status code.
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

    /**
     * ChatServer constructor.
     * @param $database_path
     */
    public function __construct($database_path) {
        $this->dbh = new SQLiteDatabase($database_path);
    }

    /**
     *
     * Loads the controller class corresponding to the request endpoint.
     * Returns true if the class could be loaded.
     * Returns false if the class could not be loaded.
     *
     * @return bool
     */
    private function load_controller() {
        $controller_name = '\\ChatApplication\\Server\\Controllers\\' . $this->request->get_endpoint() . "Controller";
        if (class_exists($controller_name)) {
            $this->controller = new $controller_name($this->dbh);
        } else {
            return false;
        }
        return true;
    }

    /**
     * Attempts to load the Controller corresponding to the request endpoint by invoking the load_controller method.
     * @see ChatServer::load_controller()
     *
     * If the controller exists and the method corresponding to the HTTP verb is implemented, the method will be
     * executed and the resulting data will be saved in a Response object.
     * @see Response
     *
     * If the controller or method doesn't exist, a Respons object with a 404 HTTP status code will be constructed.
     */
    public function handle() {
        $this->request = new Request($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
        $this->request->parse_payload();
        //short-circuits so method_exists won't be called if the controller isn't loaded,
        if ($this->load_controller() && method_exists($this->controller, $this->request->get_verb())) {
            $this->controller->{$this->request->get_verb()}($this->request->get_payload());
            $data = $this->controller->get_result_array();
            $this->response = new Response(200, $data);
        } else {
            //If the Controller couldn't be loaded, a 404 response is created.
            $message = sprintf('Page %s not found!', $this->request->get_endpoint());
            $this->response = new Response(404, $message);
        }
        $this->response->send();
    }
}