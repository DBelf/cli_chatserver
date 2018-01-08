<?php
/**
 * Implementation of the Abstract Controller in charge of querying the database for unread messages.
 * @see AbstractController
 *
 * The UnreadController supports the delete HTTP verb.
 *
 * The delete verb will delete the row of the unread message from the Unread table.
 *
 * @package    chat_server
 * @author     Dimitri
 */

namespace ChatApplication\Server\Controllers;

class UnreadController extends AbstractController
{
    /**
     * @var array
     */
    private $query_array = [
        'delete' => 'DELETE FROM Unread WHERE message_id = :message_id'
    ];

    /**
     * Queries the database to delete the unread message row with the id which is provided in
     * the arguments array.
     *
     * @param array $arguments
     * @return void
     */
    public function delete($arguments = []) {
        $this->dbh->query($this->query_array['delete'], $arguments);
    }

}