<?php
/**
 * Interface for the Controllers that are in charge of interacting with the database.
 *
 * Controllers can convert the results of queries to arrays to make the transportation of data
 * more straightforward.
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