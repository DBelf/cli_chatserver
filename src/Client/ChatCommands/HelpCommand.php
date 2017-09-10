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


use const PHP_EOL;

class HelpCommand implements ChatCommand
{
    public function execute($username) {
        echo 'Sending messages: message send {username} {body}' . PHP_EOL;
        echo 'Getting all user info: users get' . PHP_EOL;
        echo 'Getting all info of a single user: users get {username}' . PHP_EOL;
        echo 'Changing your username: users update {new_username}' . PHP_EOL;
    }
}