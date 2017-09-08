<?php
/**
 * Abstract class for the Controllers that are in charge of interacting with the database.
 * This class implements the Controller interface @see Controller.
 *
 * Each controller should have at least one of the HTTP verbs implemented.
 * Concrete Controllers construct a result array containing the data of their query, if any.
 *
 * If the query was successful, the ok value of the response array will be set to true.
 * If the query was unsuccessful, the ok value of the response array will be set to false and
 * an error element will be added with a message containing the error message.
 *
 * @package    bunq_assignment
 * @author     Dimitri
 */

namespace ChatApplication\Server\Controllers;

use ChatApplication\Server\DatabaseService\DatabaseService;

abstract class AbstractController implements Controller
{
    /**
     * @var DatabaseService
     */
    protected $dbh;
    /**
     * @var array
     */
    protected $result_array = ['ok' => true];

    public function __construct(DatabaseService $dbh) {
        $this->dbh = $dbh;
    }

    protected function not_implemented() {
        $this->result_array['ok'] = false;
        $this->result_array['error'] = 'Method not implemented!';
    }

    protected function no_argument() {
        $this->result_array['ok'] = false;
        $this->result_array['error'] = 'No argument supplied!';
    }

    /**
     * @param array $arguments
     * @return mixed
     */
    public function get($arguments = []) {
        $this->not_implemented();
        return;
    }

    /**
     * @param $arguments
     * @return mixed
     */
    public function post($arguments = []) {
        $this->not_implemented();
        return;
    }

    /**
     * @param $arguments
     * @return mixed
     */
    public function put($arguments = []) {
        $this->not_implemented();
        return;
    }

    /**
     * @param $arguments
     * @return mixed
     */
    public function delete($arguments = []) {
        $this->not_implemented();
        return;
    }

    /**
     * @return mixed
     */
    public function get_result_array() {
        return $this->result_array;
    }
}