<?php
/**
 * Short description for file
 *
 * Long description for file (if any)...
 *
 * @package    bunq_assignment
 * @author     Dimitri
 */

namespace ChatApplication\Client;

require_once(__DIR__ . '/../../vendor/autoload.php');

use function microtime;
use const PHP_EOL;
use function sprintf;

class ChatClient
{
    private $username;
    private $remote_request;
    /**
     * @var InputParser
     */
    private $input_parser;
    private $input_stream;

    public function __construct($username, $server_address, $server_port, $input_stream = 'php://stdin') {
        $this->username = $username;
        $this->remote_request = new RemoteRequest($server_address, $server_port);
        $this->input_stream = $input_stream;
    }

    /**
     * Endless loop to process the user input and poll the server.
     * Blocks when the user starts typing on the line.
     * If the user input started with a known command, the corresponding action will be taken.
     *
     * Found on:
     * @link https://stackoverflow.com/questions/21464457/why-stream-select-on-stdin-becomes-blocking-when-cmd-exe-loses-focus
     * Adapted to suit this project.
     */
    public function execute() {
        //$loopnumber = 0;
        while (true) {
            $stream = fopen($this->input_stream, 'r');

            $stream_array = array($stream);
            $write = array();
            $except = array();

            if (stream_select($stream_array, $write, $except, 1, 0)) {
                $input = trim(fgets($stream));
                if ($input) {
                    $command = $this->input_parser->parse($input);
                    $command->execute();
                }
            }
//            echo $loopnumber++ . ' iterations done!' . PHP_EOL;
            fclose($stream);
        }
    }

//    //Find the command and take the corresponding action.
//    private function parse_input($input) {
//
//        echo sprintf('> %s' . PHP_EOL, $input);
//        echo sprintf("Command unknown_command not supported!" . PHP_EOL);
//    }

//    private function poll_server_for_unread_messages() {
//        //Send a request to the server to retrieve all unread messages.
//        //Send a request to the server to remove each displayed message.
//    }
//
//    private function tell_server_to_remove_from_unread($message_id) {
//        //Send a request to the server to remove a message from the Unread table.
//    }
//
//    private function retrieve_user_list($input_string) {
//        //Send a request to the server to retrieve one or multiple users.
//        //Input string should look like /users <username>(optional)
//    }
//
//    private function message($input_string) {
//        //Split on recipient, and messagebody.
//        //Input string should look like /send <user> <body>
//    }
}
//
//$username = argv[1];
//$serer_address = argv[2];
//$server_port = argv[3];
//$client = new ChatClient($username, $serer_address, $server_port);
//$client->execute();