<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 09/09/15
 * Time: 22:52
 */

namespace Core\Auth;


use Core\App;
use Core\Database\Database;
use Core\Database\FluentDatabase;

/**
 * Class DBAuth
 * @package Core\Auth
 *
 * Permet de gérer l'authentification par les sessions
 */
class DBAuth {

	protected $db;

	/**
	 * @var $fpdo FluentDatabase
	 */
	protected $fpdo;


	public function __construct( Database $db ) {
		$this->db = $db;
		$this->fpdo = new FluentDatabase($db->getPDO());
	}


	/**
	 * Function login()
	 * Cherche un utilisateur en BD et le connecte s'il existe et que le mot de passe correspond
	 *
	 * @param $email
	 * @param $password
	 *
	 * @return bool
	 */
	public function login( $email, $password ) {

		$query = "SELECT * FROM users where email = ?";

		$user = $this->db->prepare( $query, [ $email ], null, true );

		if ( empty( $user ) ) {
			return false;
		}

		if ( $this->checkPassword( $password, $user->salt, $user->password ) ) {

			// Authentification
			return $this->authenticate( $user->id, $user->auth_id );
		}

		return false;
	}


	/**
	 * Function checkPassword()
	 * Vérifie que password entré par l'utilisateur correspond au password hashé en BD concernant cet utilisateur.
	 *
	 * @param String $password : mot de passe à tester
	 * @param String $salt : salt de l'utilisateur enregistré en BD ($user->salt)
	 * @param String $userPassword : mot de passe hashé en BD de l'utilisateur ($user->password)
	 *
	 * @return bool
	 */
	private function checkPassword( $password, $salt, $userPassword ) {

		$sha512Password = hash( "sha512", $password . $salt );

		return $sha512Password === $userPassword;
	}


	/**
	 * Function authenticate()
	 * Enregistre l'utilisateur en session
	 *
	 * @param $id : id de l'utilisateur
	 * @param $level : niveau de compte (1:user, 2:admin)
	 *
	 * @return mixed
	 */
	private function authenticate( $id, $level ) {
		return $_SESSION['Auth'] = [
			'id'     => $id,
			'level'  => $level,
			'dbuser' => $this->getDBUser( $level ),
			'permissions' => $this->getAccess($level)
		];

	}


	/**
	 * function logged()
	 * Vérifie si un utilisateur est connecté
	 *
	 * Renvoie faux ou l'id de l'utilisateur connecté
	 * @return bool || user_id
	 */
	public function logged() {

		if ( isset( $_SESSION['Auth'] ) && ! empty( $_SESSION['Auth'] ) ) {
			//var_dump($_SESSION['Auth']);
			return $_SESSION['Auth']['id'];
		}

		return false;
	}

	public function getAuth($key) {
		if ( isset( $_SESSION['Auth'][$key] ) && ! empty( $_SESSION['Auth'][$key] ) ) {

			return $_SESSION['Auth'][$key];
		}

		return 0;
	}


	/**
	 * Function logout()
	 * Déconnecte un utilisateur
	 *
	 */
	public function logout() {

		$_SESSION = [ ];
		session_destroy();

	}


	/**
	 * @return bool
	 */
	public function isAdmin() {
		if ( isset( $_SESSION['Auth'] ) && ! empty( $_SESSION['Auth'] ) ) {

			if ( $_SESSION['Auth']['level'] >= 1 ) {
				return $_SESSION['Auth'];
			}
		}

		return false;
	}


	private function getDBUser( $auth_id ) {
		$query   = "SELECT user FROM auth where id = ?";
		$db_user = $this->db->prepare( $query, [ $auth_id ], null, true );

		if ( empty( $db_user ) ) {
			return null;
		}

		return $db_user->user;
	}


	private function getAccess($level) {
		$query = $this->fpdo->from('auth_permissions')
							->where(['auth_id' => $level])
							->fetchAll();

		if(empty($query)) {
			$query = null;
		}

		$permissions = [];
		foreach($query as $perm) {
			array_push($permissions, $perm['permission']);
		}

		return $permissions;
	}

	/**
	 * Verifie l'access à une page
	 * @param $action
	 * @return bool
	 */
	public function hasAccess($action) {
		if (!isset($_SESSION['Auth']['level'])) return false;
		$userLevel = (int) $_SESSION['Auth']['level'];

		$permissions = $_SESSION['Auth']['permissions'];
		foreach ($permissions as $permission) {
			if ($this->checkAuth($permission, $action)) return true;
		}

		return false;
	}

	/**
	 * @param $tested string user permission
	 * @param $wanted string asked permission
	 * @return bool
	 */
	private function checkAuth($tested, $wanted) {
		if ($tested == '*.*') return true; // *.*
		$w = explode('.', $wanted);
		$t = explode('.', $tested);

		if ($t[0] == '*'   && $t[1] == $w[1]) return true; // *.xxx
		if ($t[0] == $w[0] && $t[1] == '*'  ) return true; // xxx.*
		if ($t[0] == $w[0] && $t[1] == $w[1]) return true; // xxx.xxx

		return false;
	}

}