<?php
/**
 * Short description for file
 *
 * Long description for file (if any)...
 *
 * @package    bunq_assignment
 * @author     Dimitri
 */

namespace ChatApplication;

require_once(__DIR__ . '/../../vendor/autoload.php');

use ChatApplication\Messages;

class ChatClient
{
    private $_username;
    private $_input_stream;
    private $_output_stream;

    public function __construct($username, $input_stream, $output_stream) {
        $this->_username = $username;
        $this->_input_stream = $input_stream;
    }

}