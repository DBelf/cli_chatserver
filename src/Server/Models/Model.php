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
    public function get($arguments = []);
    public function post($arguments);
    public function put($arguments);
    public function delete($arguments);
}