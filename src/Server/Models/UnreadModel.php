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
    protected $query_array = [
        'delete' => "DELETE FROM Unread WHERE message_id = :message_id"
    ];

    /**
     * @param array $arguments
     * @return mixed
     */
    public function get($arguments = []) {
        // TODO: Implement get() method.
    }

    /**
     * @param $arguments
     * @return mixed
     */
    public function post($arguments) {
        // TODO: Implement post() method.
    }

    /**
     * @param $arguments
     * @return mixed
     */
    public function put($arguments) {
        // TODO: Implement put() method.
    }

    /**
     * @param $arguments
     * @return mixed
     */
    public function delete($arguments) {

    }

    /**
     * @return mixed
     */
    public function get_result_array() {
        // TODO: Implement get_result_array() method.
    }


}