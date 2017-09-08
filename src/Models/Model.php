<?php
/**
 * Interface for the data model objects which can be retrieved from the database.
 *
 * Data models can convert their properties to arrays.
 * Data model objects are in charge of displaying themselves on the view (in this case the CLI).
 *
 * @package    bunq_assignment
 * @author     Dimitri
 */

namespace ChatApplication\Models;


interface Model
{
    public function display();

    public function to_array();
}