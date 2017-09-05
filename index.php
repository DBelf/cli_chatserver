<?php
/**
 * Short description for file
 *
 * Long description for file (if any)...
 *
 * @package    bunq_assignment
 * @author     Dimitri
 */
require_once(__DIR__ . '/vendor/autoload.php');
use ChatApplication\Server\Request;

$payload = file_get_contents('php://input');
$request = new Request($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD'], $payload);