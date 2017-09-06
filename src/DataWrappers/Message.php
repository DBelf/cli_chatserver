<?php
/**
 * Short description for file
 *
 * Long description for file (if any)...
 *
 * @package    bunq_assignment
 * @author     Dimitri
 */

namespace ChatApplication\DataWrappers;

use function sprintf;

class Message implements DataWrapper
{
    private $_message_id;
    private $_sender_name;
    private $_timestamp;
    private $_body;

    /**
     * Message constructor.
     * @param $message_id
     * @param $sender
     * @param $timestamp
     * @param $body
     */
    public function __construct($message_id, $sender, $timestamp, $body) {
        $this->_message_id = $message_id;
        $this->_sender_name = $sender;
        $this->_timestamp = $timestamp;
        $this->_body = $body;
    }

    public function id() {
        return $this->_message_id;
    }

    public function to_array() {
        $array = [
            'message_id' => $this->_message_id,
            'sender_name' => $this->_sender_name,
            'timestamp' => $this->_timestamp,
            'body' => $this->_body
        ];
        return $array;
    }

    //TODO get time formatted.
    public function display() {
        sprintf("%s[%d]: %s\n", $this->_sender_name, $this->_timestamp, $this->_body);
    }
}