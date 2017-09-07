<?php
/**
 * Short description for file
 *
 * Long description for file (if any)...
 *
 * @package    bunq_assignment
 * @author     Dimitri
 */

namespace ChatApplication\Models;

use function sprintf;

class User implements Model
{
    private $id;
    private $username;

    /**
     * User constructor.
     * @param $id
     * @param $username
     */
    public function __construct($id, $username) {
        $this->id = $id;
        $this->username = $username;
    }

    public function to_array() {
        $array = [
            'id' => $this->id,
             'username' => $this->username
        ];
        return $array;
    }

    public function display() {
        sprintf('%s\n', $this->username);
    }


}