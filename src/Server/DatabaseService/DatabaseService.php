<?php
/**
 * Short description for file
 *
 * Long description for file (if any)...
 *
 * @package    bunq_assignment
 * @author     Dimitri
 */

namespace ChatApplication\Server\DatabaseService;


interface DatabaseService
{
    public function query($statement, $arguments);
    public function get_last_insert_id();
}