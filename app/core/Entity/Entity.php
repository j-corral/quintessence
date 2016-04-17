<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 09/09/15
 * Time: 00:15
 */

namespace Core\Entity;


class Entity {


    public function __get($key) {

        $method = 'get' . ucfirst($key);
        $this->key = $this->$method();

        return $this->key;
    }

}