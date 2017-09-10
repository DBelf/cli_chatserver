<?php

/**
 * The main execution loop of the CLI client chat application.
 *
 * This class is in charge of reading commands from the command line and passing it
 * to the input parser.
 * The output from the input parser will then be executed.
 *
 * The main loop calculates the elapsed time and tells the Client to poll the server for new messages
 * every 10 seconds. @see ChatClient::poll_for_unread_messages().
 *
 * Is executable from the command line with two arguments:
 *  - the address of the server
 *  - the port of the server
 *
 * @package    bunq_assignment
 * @author     Dimitri
 */

namespace ChatApplication\Client;

use const PHP_EOL;
use function microtime;

require_once(__DIR__ . '/../../vendor/autoload.php');

class CLIChatClientApp
{
    /**
     * @var ChatClient
     */
    private $client;
    /**
     * @var RemoteRequest
     */
    private $remote_request;
    /**
     * @var InputParser
     */
    private $input_parser;
    /**
     * @var string
     */
    private $input_stream;

    public function __construct($server_address, $server_port, $input_stream = 'php://stdin') {
        $this->remote_request = new RemoteRequest($server_address, $server_port);
        $this->input_parser = new InputParser($this->remote_request);
        $this->input_stream = $input_stream;
        $this->client = new ChatClient();
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
        while (!$this->client->prompt_user_for_username($this->remote_request)) ;
        $start_time = microtime(true);
        while (true) {
            $stream = fopen($this->input_stream, 'r');
            $stream_array = array($stream);
            $write = array();
            $except = array();

            //Blocks when the user is typing.
            if (stream_select($stream_array, $write, $except, 1, 0)) {
                $input = trim(fgets($stream));
                //Parse the input and execute it.
                if ($input) {
                    $command = $this->input_parser->parse($input);
                    $command->execute($this->client->get_username());
                }
            }
            $current_time = microtime(true);
            //Convert the time difference to seconds.
            $time_diff = ($current_time - $start_time) * 10000000;
            if ($time_diff > 10) {
                //Poll the server for new messages every 10 seconds.
                $this->client->poll_for_unread_messages($this->remote_request);
            }
            fclose($stream);
        }
    }
}

if (count($argv) !== 3) {
    echo 'Need both the server name and server port to initialize a chat client app.' . PHP_EOL;
    echo 'Example use: php CLIChatClientApp.php \<servername\> \<serverport\>' . PHP_EOL;
    die();
}

$CLIApp = new CLIChatClientApp($argv[1], $argv[2]);
$CLIApp->execute();