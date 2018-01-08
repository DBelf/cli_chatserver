<?php
/**
 * Displays the available commands for a user.
 *
 * @package    chat_server
 * @author     Dimitri
 */

namespace ChatApplication\Client\ChatCommands;

use const PHP_EOL;
use function sprintf;

class HelpCommand implements ChatCommand
{
    /**
     * Echoes all available commands.
     * @param $username
     */
    public function execute($username) {
        echo sprintf('Current username is: %s', $username) . PHP_EOL;
        echo 'Sending messages: message send {username} {body}' . PHP_EOL;
        echo 'Getting all user info: users get' . PHP_EOL;
        echo 'Getting all info of a single user: users get {username}' . PHP_EOL;
        echo 'Changing your username: users update {new_username}' . PHP_EOL;
    }
}