<?php
/**
 * User model.
 *
 * User models are wrappers for the information of one user.
 * User objects are used by the UsersController to transform the data to arrays @see MessagesController.
 * User objects are used by the ChatClient to display the users @see ChatClient.
 *
 * @package    chat_server
 * @author     Dimitri
 */

namespace ChatApplication\Models;

use const PHP_EOL;
use function sprintf;

class User implements Model
{
    /**
     * @var integer
     */
    private $id;
    /**
     * @var string
     */
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

    /**
     * Converts a User object to a key, value array.
     * @return array
     */
    public function to_array() {
        $array = [
            'id' => $this->id,
            'username' => $this->username
        ];
        return $array;
    }

    public function __toString() {
        return sprintf('%s: %d' . PHP_EOL, $this->username, $this->id);
    }
}