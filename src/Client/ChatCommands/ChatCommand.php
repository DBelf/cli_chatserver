<?php
/**
 * ChatCommand interface.
 *
 * ChatCommands are used by the CLIClient to execute the user input.
 *
 * @package    chat_server
 * @author     Dimitri
 */

namespace ChatApplication\Client\ChatCommands;

interface ChatCommand
{
    public function execute($username);
}