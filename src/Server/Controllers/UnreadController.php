<?php
/**
 * Short description for file
 *
 * Long description for file (if any)...
 *
 * @package    bunq_assignment
 * @author     Dimitri
 */

namespace ChatApplication\Server\Controllers;

class UnreadController extends AbstractController
{
    private $query_array = [
        'delete' => 'DELETE FROM Unread WHERE message_id = :message_id'
    ];

    /**
     * @param $arguments
     * @return mixed
     */
    public function delete($arguments = []) {
        $this->dbh->query($this->query_array['delete'], $arguments);
    }

}