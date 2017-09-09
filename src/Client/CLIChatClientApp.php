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

class CLIChatClientApp
{
    private $client;
    private $remote_request;
    /**
     * @var InputParser
     */
    private $input_parser;
    private $input_stream;

    public function __construct($server_address, $server_port, $input_stream = 'php://stdin') {
        $this->remote_request = new RemoteRequest($server_address, $server_port);
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
        //$loopnumber = 0;
        while (!$this->client->prompt_user_for_username($this->remote_request)) ;

        while (true) {
            $stream = fopen($this->input_stream, 'r');

            $stream_array = array($stream);
            $write = array();
            $except = array();

            if (stream_select($stream_array, $write, $except, 1, 0)) {
                $input = trim(fgets($stream));
                if ($input) {
                    $command = $this->input_parser->parse($input);
                    $command->execute($this->client->get_username());
                }
            }
//            echo $loopnumber++ . ' iterations done!' . PHP_EOL;
            fclose($stream);
        }
    }
}