<?php
/**
 * Short description for file
 *
 * Long description for file (if any)...
 *
 * @package    bunq_assignment
 * @author     Dimitri
 */

use ChatApplication\Server\ChatServer;

require_once(__DIR__ . '/vendor/autoload.php');

$chat_server = new ChatServer(__DIR__ . '/database/chat_server.db');
$chat_server->handle();