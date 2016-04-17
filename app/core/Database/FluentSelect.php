<?php
/**
 * Created by PhpStorm.
 * User: crozet
 * Date: 25/02/2016
 * Time: 14:22
 */

namespace Core\Database;


class FluentSelect extends \SelectQuery
{
    /**
     * fetchs the statement as or into a class
     * @param string|object $class string : the name of the class | object instance in which the results are fetched
     * @return bool|object
     */
    public function fetchClass($class) {
        $return = $this->execute();
        if ($return === false) {
            return false;
        }

        if (is_object($class)) {
            $return->setFetchMode(\PDO::FETCH_INTO, $class);
            $fetch = $return->fetch();
        } else {
            $return->setFetchMode(\PDO::FETCH_CLASS, $class);
            $fetch = $return->fetch();
        }

        return $fetch;
    }

    /**
     * fetchs the statement as or into a class
     * @param string $class the name of the class
     * @return bool|object
     */
    public function fetchAllClass($class) {
        $return = $this->execute();
        if ($return === false) {
            return false;
        }

        return $return->fetchAll(\PDO::FETCH_CLASS, $class);
    }
}