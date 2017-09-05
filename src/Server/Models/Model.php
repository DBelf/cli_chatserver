<?php
/**
 * Created by PhpStorm.
 * User: dimitri
 * Date: 05/09/2017
 * Time: 21:42
 */

namespace ChatApplication\Server\Models;


interface Model
{
    public function get($json = '[]');
    public function post($json);
    public function put($json);
    public function delete($json);
}