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

use ChatApplication\Server\DatabaseService\DatabaseService;

abstract class AbstractController implements Model
{
    protected $dbh;
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
    }

    /**
     * @param $arguments
     * @return mixed
     */
    public function post($arguments = []) {
        $this->not_implemented();
    }

    /**
     * @param $arguments
     * @return mixed
     */
    public function put($arguments = []) {
        $this->not_implemented();
    }

    /**
     * @param $arguments
     * @return mixed
     */
    public function delete($arguments = []) {
        $this->not_implemented();
    }

    /**
     * @return mixed
     */
    public function get_result_array() {
        return $this->result_array;
    }
}