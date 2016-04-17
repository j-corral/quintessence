<?php
    /**
     * Created by PhpStorm.
     * User: jonathan
     * Date: 10/09/15
     * Time: 00:02
     */

    namespace Core\Database;


    class QueryBuilder {

        private $fields = [];

        private $table = [];

        private $conditions = [];


        public function select () {
            $this->fields = func_get_args ();

            return $this;
        }


        public function from ($table, $alias = null) {

            if (is_null ($alias))
                $this->table[] = $table;
            else
                $this->table[] = "$table AS $alias";

            return $this;
        }


        public function where () {

            foreach (func_get_args () as $arg)
                $this->conditions[] = $arg;

            return $this;
        }


        public function __toString () {
            $select = 'SELECT ' . implode (', ', $this->fields);
            $from = ' FROM ' . implode (', ', $this->table);
            $where = ' WHERE ' . implode (' AND ', $this->conditions);

            return $select . $from . $where;
        }


    }