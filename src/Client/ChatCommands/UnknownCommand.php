<?php
/**
 * Short description for file
 *
 * Long description for file (if any)...
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
     * @param $username
     * @return mixed
     */
    public function execute($username) {
        echo sprintf("Command %s not supported!" . PHP_EOL, $this->argument);
    }

}