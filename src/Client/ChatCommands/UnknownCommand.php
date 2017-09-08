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
    private $command;

    /**
     * UnknownCommand constructor.
     * @param string $command
     */
    public function __construct($command) {
        $this->command = $command;
    }

    /**
     * @return mixed
     */
    public function execute() {
        echo sprintf("Command %s not supported!" . PHP_EOL, $this->command);
    }

}