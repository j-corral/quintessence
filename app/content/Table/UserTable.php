<?php

namespace Content\Table;

use Core\App;
use Core\Database\FluentSelect;
use Core\Table\FluentTable;
use Core\Table\Table;

/**
 * Class UserTable
 */
class UserTable extends FluentTable {

    protected $table = 'users';

    /**
     * @return string
     */
    public function getTable() {
        return $this->table;
    }



}