<?php
/**
 * Interface for the data model objects which can be retrieved from the database.
 *
 * Data models can convert their properties to arrays.
 * @package    chat_server
 * @author     Dimitri
 */

namespace ChatApplication\Models;


interface Model
{
    public function to_array();
}