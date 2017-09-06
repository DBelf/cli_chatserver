<?php
/**
 * Short description for file
 *
 * Long description for file (if any)...
 *
 * @package    bunq_assignment
 * @author     Dimitri
 */

namespace ChatApplication\DataWrappers;


interface DataWrapper
{
    public function display();
    public function to_array();
}