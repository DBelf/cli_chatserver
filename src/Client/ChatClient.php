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
    private $username;
    private $input_stream;
    private $output_stream;

    public function __construct($username, $input_stream, $output_stream) {
        $this->username = $username;
        $this->input_stream = $input_stream;
    }

}