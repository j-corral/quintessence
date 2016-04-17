<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 07/09/15
 * Time: 11:05
 */

namespace Core\Controller;

use Core\App;
use Core\Config;
use Core\Router\Router;
use Core\Router\RouterException;
use Core\Tools\HtmLawed;

class Controller {

	protected $viewPath;

	protected $layout;

	protected $Model;

	protected $request;


	public function __construct( $static = false ) {

		$config = Config::getInstance();

		$this->viewPath = ROOT . VIEW;

		$this->layout = $config->get( "layout", "default" );


		if ( ! $static ) {
			if ( is_null( $this->Model ) ) {
				$classname   = explode( '\\', get_class( $this ) );
				$this->Model = str_replace( 'sController', '', end( $classname ) );
			}


			$this->loadModel( $this->Model );
		}


		$this->request       = new \stdClass();
		$this->request->post = isset( $_POST['data'] ) ? $this->a2o( $_POST['data'] ) : new \stdClass();
		$this->request->get  = isset( $_GET ) ? $this->a2o( $_GET ) : new \stdClass();
	}


	/**
	 * Function Render()
	 * Transmet les informations du controller à la vue et l'intègre dans le Layout
	 *
	 * @param String $view : Vue à récupérer
	 * @param array $vars : Variables passées à la vue
	 * @param string $engine : Moteur de rendu (twig par défaut)
	 */
	public function render( $view, $vars = [ ], $engine = 'twig' ) {

		$view = str_replace( '.', '/', $view );

		switch ( $engine ) {
			case 'twig':
				$this->renderTwig( $view, $vars );
				break;
			case 'php':
				$this->renderPhp( $view, $vars );
				break;
			default:
				$this->renderTwig( $view, $vars );
		}
	}

	public function renderPhp( $view, $vars ) {
		/*$preview = str_replace('Content\Controller\\', '', get_class($this));
		$preview = strtolower(str_replace('Controller', '', $preview));*/

		$ext = '.php';
		$this->checkView($view, $ext);

		ob_start();

		extract( $vars );

		$filePath = $view . $ext;

		$twigEngine = App::getInstance()->getTwig();
		echo $twigEngine->render( $filePath, $vars );

		$content = ob_get_clean();

		require_once ROOT . LAYOUT . $this->layout . '.php';
	}

	public function renderTwig( $view, $vars ) {

		$ext = '.twig';

		$this->checkView($view, $ext);

		$twigEngine = App::getInstance()->getTwig();

		$layout          = $twigEngine->loadTemplate( $this->layout . '.twig' );
		$vars['_layout'] = $layout;

		echo $twigEngine->render( $view . $ext, $vars );
	}


	/**
	 * @param $view
	 * @param string $ext
	 *
	 * @return bool
	 * @throws RouterException
	 */
	private function checkView($view, $ext = ".php") {

		if($ext[0] != ".") {
			$ext = '.'.$ext;
		}


		if (!file_exists (ROOT . VIEW . $view . $ext)) {
			throw new RouterException("Unknown View : $view.$ext");
		}

		return true;
	}


	// Charge un Model et permet de l'utiliser sous la forme : $this->ModelName
	public function loadModel( $modelName ) {
		$app                = App::getInstance();
		$this->{$modelName} = $app->getTable( $modelName );
	}


	/**
	 * Vérifie le token passé en paramètre
	 *
	 * @param string $token
	 * @param int $duration temps avant expiration (defaut 15min)
	 *
	 * @return bool
	 */
	protected function check_csrf( $token, $duration = 15 ) {

		if ( isset( $_SESSION['csrf'] ) && isset( $_SESSION['csrfTime'] ) ) {

			if ( $_SESSION['csrf'] == $token ) {
				$expired = time() - ( $duration * 60 );

				if ( $_SESSION['csrfTime'] >= $expired ) {
					return true;
				}

			}

		}

		return false;
	}


	/**
	 * Renvoie le nom du layout configuré dans config.php
	 *
	 * @param $name
	 *
	 * @return string
	 */
	protected function loadLayout( $name ) {
		$config = Config::getInstance();

		return $config->get( 'layout', $name );
	}


	/**
	 * Convertion de tableau en objet
	 *
	 * @param array $array
	 *
	 * @return object
	 */
	protected function a2o( array $array ) {
		foreach ( $array as $key => $value ) {
			if ( is_array( $value ) ) {
				$array[ $key ] = $this->a2o( $value );
			}
		}

		return (object) $array;
	}


	protected function showMessage( $msg, $send = "", $redirection = "" ) {
		// Les messages d"erreurs ci-dessus s'afficheront si Javascript est désactivé
		header( 'Cache-Control: no-cache, must-revalidate' );
		header( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' );
		header( 'Content-type: application/json' );

		$retour['msg']         = $msg;
		$retour['send']        = $send;
		$retour['redirection'] = $redirection;
		echo json_encode( $retour );
		exit;

	}


	/**
	 * @param $action String like 'prefix.controller.action'
	 * @param int $nbPerPage
	 * @param int $page
	 * @param string $orderby
	 *
	 * @return mixed result
	 */
	protected function paginate( $action, $nbPerPage = 30, $page = 1, $orderby = "0") {

		// Si ce n'est pas un nombre on défini la page par defaut
		if ( ! is_numeric( $page )) {
			$page = 1;
		} else {
			$page = (int) $page;
		}

		// Nombre de tuples dans la BD
		$nbElem = $this->{$this->Model}->Count();

		if($nbElem == 0) {
			return [
				"req"     => '',
				"page"    => $page,
				"nbPages" => 0,
				"total"   => 0
			];
		}

		// Calcul du nombre de pages
		$nbPage = ceil( $nbElem / $nbPerPage );

		// Si la page n'existe pas
		if ($page < 1 ) {
			// On redirige sur la page 1
			App::getInstance()->redirect( $action, [ '1' ] );
		} elseif($page > $nbPage) {
			// On redirige sur la derniere page
			App::getInstance()->redirect( $action, [ $nbPage ] );
		}

		// Calcul du debut de la selection
		if ( $page == 1 ) {
			$start = 0;
		} else {
			$start = ( $page - 1 ) * $nbPerPage;
		}


		// Si pas ordonnée
		if ($orderby == "0") {
			$req = $this->{$this->Model}->AllLimit($nbPerPage, $start);
		} elseif($orderby) {
			$req = $this->{$this->Model}->AllLimitOrderby($nbPerPage, $start, $orderby);
		}

		$data = [
			"req"     => $req,
			"page"    => $page,
			"nbPages" => $nbPage,
			"total"   => $nbElem
		];

		return $data;
	}

}