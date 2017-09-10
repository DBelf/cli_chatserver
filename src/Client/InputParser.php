<?php
/**
 * Used by a CLIChatClientApp to parse user input into commands and arguments for the commands.
 *
 * Long description for file (if any)...
 *
 * @package    bunq_assignment
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
     * @param $input
     * @return ChatCommand
     */
    public function parse($input) {
        preg_match($this->pattern, $input, $matches);
        $command = ucfirst(strtolower($matches['class'])) . ucfirst(strtolower($matches['action']));
        $arguments = [$matches['argument1'], $matches['rest']];
        $command_class = $this->command_namespace . $command . 'Command';
        if (class_exists($command_class)) {
            return new $command_class($this->remote_request, $arguments);
        } else {
            return new UnknownCommand($command);
        }
    }
}