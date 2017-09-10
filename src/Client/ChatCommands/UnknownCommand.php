<?php

use ChatApplication\Client\CLIChatClientApp;

/**
 * Unknown command.
 * Used by the CLIChatClientApp to notify the user that their command was not recognized.
 * @see CLIChatClientApp.
 *
 * @package    bunq_assignment
 * @author     Dimitri
 */

namespace ChatApplication\Client\ChatCommands;

use function sprintf;

class UnknownCommand implements ChatCommand
{
    private $argument;

    /**
     * UnknownCommand constructor.
     * @param $argument
     */
    public function __construct($argument) {
        $this->argument = $argument;
    }

    /**
     * Displays the error.
     *
     * @param $username
     * @return void
     */
    public function execute($username) {
        echo sprintf("Command %s not supported!" . PHP_EOL, $this->argument);
    }

}