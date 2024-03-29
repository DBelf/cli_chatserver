<?php
/**
 * Used by a CLIChatClientApp to parse user input into commands and arguments for the commands.
 * New ChatCommands can be added in the ChatCommands namespace and they should automatically be processed by the
 * InputParser.
 *
 * @package    chat_server
 * @author     Dimitri
 */

namespace ChatApplication\Client;

require_once(__DIR__ . '/../../vendor/autoload.php');

use ChatApplication\Client\ChatCommands\ChatCommand;
use ChatApplication\Client\ChatCommands\UnknownCommand;
use function class_exists;

class InputParser
{
    private $remote_request;
    private $pattern = '/(?P<class>\w*) ?(?P<action>\w*)? ?(?P<argument1>\w*[-|_]?\w*)? ?(?P<rest>.*)?/';
    private $command_namespace = '\\ChatApplication\\Client\\ChatCommands\\';

    /**
     * InputParser constructor.
     * @param $remote_request RemoteRequest
     */
    public function __construct($remote_request) {
        $this->remote_request = $remote_request;
    }

    /**
     * Parses the input.
     * Input should always have a class, all other parts of the input are optional.
     * If the class or class + action combination does not match a defined class, an UnknownCommand
     * is returned.
     * Otherwise the corresponding ChatCommand is returned. @see ChatCommand implementations.
     *
     * @param string $input
     * @return ChatCommand
     */
    public function parse($input) {
        preg_match($this->pattern, $input, $matches);
        $command = ucfirst(strtolower($matches['class'])) . ucfirst(strtolower($matches['action']));
        $arguments = [$matches['argument1'], $matches['rest']];
        //Construct the correct classpath and name so it can be autoloaded.
        $command_class = $this->command_namespace . $command . 'Command';
        if (class_exists($command_class)) {
            return new $command_class($this->remote_request, $arguments);
        } else {
            return new UnknownCommand($command);
        }
    }
}