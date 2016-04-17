<?php
    /**
     * Created by PhpStorm.
     * User: jonathan
     * Date: 09/09/15
     * Time: 18:27
     */

    namespace Core\Database;


    class FluentDatabase extends \FluentPDO{

        /** Rewrited function to use FluentSelect instead of classical one
         * @param string $table  db table name
         * @param integer $primaryKey  return one row by primary key
         * @return FluentSelect
         */
        public function from($table, $primaryKey = null) {
            $query = new FluentSelect($this, $table);
            if ($primaryKey) {
                $tableTable = $query->getFromTable();
                $tableAlias = $query->getFromAlias();
                $primaryKeyName = $this->structure->getPrimaryKey($tableTable);
                $query = $query->where("$tableAlias.$primaryKeyName", $primaryKey);
            }
            return $query;
        }
    }