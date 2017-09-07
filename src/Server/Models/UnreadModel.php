<?php
/**
 * Short description for file
 *
 * Long description for file (if any)...
 *
 * @package    bunq_assignment
 * @author     Dimitri
 */

namespace ChatApplication\Server\Models;

class UnreadModel implements Model
{
    private $result_array = ['ok' => true];
    private $query_array = [
        'delete' => 'DELETE FROM Unread WHERE message_id = :message_id'
    ];

    /**
     * @param array $arguments
     * @return mixed
     */
    public function get($arguments = []) {
        $this->not_implemented();
    }

    /**
     * @param $arguments
     * @return mixed
     */
    public function post($arguments) {
        $this->not_implemented();
    }

    /**
     * @param $arguments
     * @return mixed
     */
    public function put($arguments) {
        $this->not_implemented();
    }

    /**
     * @param $arguments
     * @return mixed
     */
    public function delete($arguments) {

    }

    private function not_implemented() {
        $this->result_array['ok'] = false;
        $this->result_array['error'] = 'Method not implemented!';
    }

    /**
     * @return mixed
     */
    public function get_result_array() {
        return $this->result_array;
    }

}