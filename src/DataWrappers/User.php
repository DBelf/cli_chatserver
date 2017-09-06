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

use function sprintf;

class User implements DataWrapper
{
    private $_id;
    private $_username;

    /**
     * User constructor.
     * @param $id
     * @param $username
     */
    public function __construct($id, $username) {
        $this->_id = $id;
        $this->_username = $username;
    }

    public function to_array() {
        $array = [
            'id' => $this->_id,
             'username' => $this->_username
        ];
        return $array;
    }

    public function display() {
        sprintf("%s\n", $this->_username);
    }


}