<?php
/**
 * Message model.
 *
 * Message models are wrappers for the information of the communication between two users.
 * Message objects are used by the MessageController to transform the data to arrays @see MessagesController.
 * Messages objects used by the ChatClient to display the message @see ChatClient.
 *
 * @package    bunq_assignment
 * @author     Dimitri
 */

namespace ChatApplication\Models;

use const PHP_EOL;
use function sprintf;

class Message implements Model
{
    /**
     * @var integer
     */
    private $message_id;
    /**
     * @var string
     */
    private $sender_name;
    /**
     * @var integer
     */
    private $timestamp;
    /**
     * @var string
     */
    private $body;

    /**
     * Message constructor.
     * @param $message_id
     * @param $sender
     * @param $timestamp
     * @param $body
     */
    public function __construct($message_id, $sender, $timestamp, $body) {
        $this->message_id = $message_id;
        $this->sender_name = $sender;
        $this->timestamp = $timestamp;
        $this->body = $body;
    }

    public function get_id() {
        return $this->message_id;
    }

    public function to_array() {
        $array = [
            'message_id' => $this->message_id,
            'sender_name' => $this->sender_name,
            'timestamp' => $this->timestamp,
            'body' => $this->body
        ];
        return $array;
    }

    //TODO: get time formatted.
    public function display() {
        sprintf('%s[%d]: %s' . PHP_EOL, $this->sender_name, $this->timestamp, $this->body);
    }
}