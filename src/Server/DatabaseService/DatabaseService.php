<?php
/**
 * Interface of the DatabaseServices.
 * Concrete DatabaseServices are PDO wrappers with different database types.
 *
 * @package    bunq_assignment
 * @author     Dimitri
 */

namespace ChatApplication\Server\DatabaseService;

use PDOStatement;

interface DatabaseService
{
    public function start_transaction();

    public function commit();

    public function roll_back();

    /**
     * @param $statement
     * @param $arguments
     * @return PDOStatement
     */
    public function query($statement, $arguments);

    public function get_last_insert_id();
}