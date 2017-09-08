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

interface Controller
{
    public function get($arguments = []);
    public function post($arguments);
    public function put($arguments);
    public function delete($arguments);
    public function get_result_array();
}