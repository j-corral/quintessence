<?php

namespace Core\Database;

use Core\Config;
use Core\Helper\Debug;
use \PDO;
use PDOException;


/**
 * Class Database
 * Permet de manipuler PDO plus simplement
 */
class MysqlDatabase extends Database {

	private $db_name;

	private $host;

	private $port;

	private $user;

	private $password;

	private static $pdo;


	public function __construct( $db_name, $host = 'localhost', $user = 'root', $password = 'root', $port = '3306' ) {
		$this->db_name  = $db_name;
		$this->host     = $host;
		$this->user     = $user;
		$this->password = $password;
		$this->port     = $port;
	}


	/**
	 * Récupère l'objet PDO, affiche les erreurs
	 */
	public function getPDO() {

		if ( is_null(self::$pdo)) {
			self::$pdo = $this->initPDO($this->user, $this->password);
		}

		return self::$pdo;
	} // getPDO()


	/**
	 * Se connecte avec le bon utilisateur SQL
	 * @param $auth_id
	 */
	public function changePDO($auth_id) {

		$query = "SELECT user FROM auth where id = ?";

		$user = $this->prepare ($query, [$auth_id], null, true);

		$config = Config::getInstance();

		$password = $config->get('db_users', $user);

		self::$pdo = $this->initPDO($user, $password);
	}


	/**
	 * Initie une connexion PDO
	 * @param $user
	 * @param $password
	 *
	 * @return null|PDO
	 */
	private function initPdo($user,$password) {

		$pdo = null;

		try {
			$pdo = new PDO(
				"mysql:dbname={$this->db_name};host={$this->host};port={$this->port}",
				$user,
				$password
			);

			return $pdo;
		} catch (PDOException $e) {

			if ( Config::getInstance()->get( 'debug' ) > 0 ) {
				//$pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
				die('PDO ERROR : ' . $e->getMessage() . " !<br />");
			} else {
				die('PDO ERROR !');
			}
		} /*finally {
			die('PDO : Une erreur est survenue !');
		}*/

	}


	/**
	 * Function query()
	 * Renvoie le résultat de la requête
	 *
	 * @param $statement
	 * @param null $classname
	 * @param bool|false $one
	 *
	 * @return array|mixed
	 */
	public function query( $statement, $classname = null, $one = false ) {

		$req = $this->getPDO()->query( $statement );

		if ( $classname === null ) {
			$req->setFetchMode( PDO::FETCH_OBJ );
		} else {
			$req->setFetchMode( PDO::FETCH_CLASS, $classname );
		}

		if ( $one ) {
			$data = $req->fetch();
		} else {
			$data = $req->fetchAll();
		}

		return $data;
	} // query()


	public function insert($statement) {
		$req = $this->getPDO()->query( $statement );
	}


	/**
	 * Function prepare()
	 * Permet d'executer une requête paramétrée
	 *
	 * @param $statement
	 * @param $attributes
	 * @param null $classname
	 * @param bool|false $one
	 *
	 * @return array|mixed
	 */
	public function prepare( $statement, $attributes, $classname = null, $one = false ) {

		$req = $this->getPDO()->prepare( $statement );

		//$req->debugDumpParams ();

		$req->execute( $attributes );

		if ( $classname === null ) {
			$req->setFetchMode( PDO::FETCH_OBJ );
		} else {
			$req->setFetchMode( PDO::FETCH_CLASS, $classname );
		}


		if ( $one ) {
			$data = $req->fetch();
		} else {
			$data = $req->fetchAll();
		}

		return $data;

	} // prepare()


} // Database