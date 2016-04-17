<?php
/**
 * Created by PhpStorm.
 * User: crozet
 * Date: 26/02/16
 * Time: 09:00
 */

namespace Core\Table;


use Core\App;
use Core\Database\Database;
use Core\Database\FluentDatabase;
use Core\Database\FluentSelect;

class FluentTable
{
    /**
     * @var $fpdo FluentDatabase
     */
    protected $fpdo;

    protected $req;

    protected $table;

    /**
     * @param Database $db
     * @param null $table
     * @param null $fpdo
     * @throws \Exception
     */
    public function __construct(Database $db = null, $table = null, $fpdo = null)
    {
        if ($db == null) $db = App::getDBInstance();

        $this->initTable();

        $this->initFPDO($db, $fpdo);

        $this->initReq();

    }

    /**
     * magic method to make auto joins
     * @param $name
     * @return object|false
     */
    public function __get($name)
    {
//        var_dump($name);
        $realTable = false;
        if (substr($name, -3) == '_id') return false;

        $tableName = '\Content\Table\\' . ucfirst($name) . 'Table';
        if (is_callable([new $tableName(), 'getTable'])) {
            $realTable = call_user_func([new $tableName(), 'getTable']);
        }
        if (!$realTable) {
            $realTable = $name;
        }
        $nameId = $name . '_id';
        if (!empty($this->$nameId)) {
            $this->$name = $this->fpdo->from($realTable, $this->$nameId)->fetchClass('\stdClass');
            return $this->$name;
        }
        return false;
    }

    public function __isset($name)
    {
        if (substr($name, -6) == '_id_id') return false; // bricolage, à revoir
        $nameId = $name . '_id';
        if (!empty($this->$nameId)) {
            return true;
        }
        return false;
    }


    /**
     * Initialisation du nom de la table
     */
    private function initTable() {

        /*if (is_null($table)) {
            $table = strtolower(substr((new \ReflectionClass($this))->getShortName(), 0, -5)) . 's';
            if ($table == 'fluent') throw new \Exception('nope !');
        }*/

        //$this->table = $table;

        if(is_null($this->table)) {

            $parts = explode('\\', get_class($this));
            $classname = end($parts);

            $this->table = strtolower(str_replace('Table', '', $classname)) . 's';
        }
    }


    /**
     * Initialisation de $fpdo
     * @param $db
     * @param $fpdo
     */
    private function initFPDO($db, $fpdo) {
        if (is_null($fpdo)) {
            $this->fpdo = new FluentDatabase($db->getPDO());
        } else {
            $this->fpdo = $fpdo;
        }
    }


    /**
     * Initialisation de $req
     */
    private function initReq() {
        $this->req = new FluentSelect($this->fpdo, $this->table);
    }


    /**
     * Renvoie tous les tuples de la table
     * @param bool $foreign : renvoie aussi les tuples des clefs etrangeres
     * @return FluentSelect
     */
    public function All($foreign = false)
    {
        if($foreign) {
            return $this->fpdo->from($this->table)->fetchAllClass(get_class($this));
        } else {
            return $this->fpdo->from($this->table);
        }
    }


    public function findFirst() {

    }

    /**
     * Renvoie le tuple correspondant à l'id
     * @param $id
     *
     * @return FluentSelect
     */
    public function findById($id) {
        return $this->fpdo->from($this->table, $id)->fetchClass(get_class($this));
    }


    /**
     * Ajoute un tuple à la table courante
     * @param $values ["field1" => "value1", "field2" => "value2", ...]
     *
     * @return int
     */
    public function insertRow($values) {
        return $this->fpdo->insertInto($this->table)->values($values)->execute();
    }


    /**
     * Supprime un tuple
     * @param $id du tuple à supprimer
     *
     * @return bool
     */
    public function deleteRow($id) {
        return $this->fpdo->deleteFrom($this->table, $id)->execute();
    }


    /**
     * Met à jour un tuple
     * @param $values ["field1" => "value1", "field2" => "value2", ...]
     * @param $id du tuple à modifier
     *
     * @return bool|int|\PDOStatement
     */
    public function updateRow($values, $id) {
        return $this->fpdo->update($this->table, $values, $id)->execute();
    }

    /**
     * @param bool $into
     *
     * @return bool|object
     */
    public function One($into = false) {

        if ($into) {
            return $this->fpdo->from($this->table)->fetchClass($this);
        } else {
            return $this->fpdo->from($this->table)->fetchClass(get_class($this));
        }
    }



    /**
     * @param Null
     *
     * @return Nbre de tuple
     */
    public function Count() {
        return $this -> fpdo -> from ($this->table) -> count ('*');
    }

    /**
     * @param $nbElem
     * @param  $start
     *
     * @return tout les tuples de xx à xx ( pagination )
     */

    public function AllLimit($nbElem, $start = 0) {

        return $this->fpdo->from($this->table)->limit($nbElem)->offset($start);
    }

    /**
     * @param $nbElem
     * @param  $start
     * @param  $orderby
     *
     * @return tout les tuples de xx à xx ( pagination ) & orderby DESC/ACS du champ passer dans la variable orderby
     */
    public function AllLimitOrderby($nbElem, $start = 0,$orderby) {

        //
        $values= explode("-",$orderby);
        $values = $values[0]." ".$values[1];

        return $this->fpdo->from($this->table)->orderBy($values)->limit($nbElem)->offset($start);
    }


    /**
     * Supprime les utple des id passé en param
     * @param $data array des tuples à supprimer
     *
     * @return bool
     */
    public function deleteRowSelection($data) {
        return $this->fpdo->deleteFrom($this->table)->where('id',$data)->execute();
    }

}




/**
 * @return FluentSelect
 */
/*public function query()
{
    return $this->fpdo->from();
}*/

