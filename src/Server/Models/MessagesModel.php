<?php
/**
 * Created by PhpStorm.
 * User: dimitri
 * Date: 05/09/2017
 * Time: 21:48
 */

namespace ChatApplication\Server\Models;

require_once(__DIR__ . '/../../../vendor/autoload.php');

use ChatApplication\Server\DatabaseService\DatabaseService;

class MessagesModel implements Model
{
    private $_db;

    /**
     * MessagesModel constructor.
     * @param DatabaseService $db
     */
    public function __construct(DatabaseService $db) {
        $this->_db = $db;
    }

    public function get($json = '[]') {
        // TODO: Implement get() method.
    }

    public function post($json) {
        // TODO: Implement post() method.
    }

    public function put($json) {
        // TODO: Implement put() method.
    }

    public function delete($json) {
        // TODO: Implement delete() method.
    }

}