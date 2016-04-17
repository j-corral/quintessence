<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 12/09/15
 * Time: 15:47
 */

namespace Core\Router;


use Content\Controller\StaticsController;
use Core\App;
use Core\Helper\Debug;
use ReflectionClass;
use Core\Config;

class Router {

	private $url;
	private $routes = [ ];
	private $namedRoutes = [ ];

	private static $prefixes = [ ];

	private static $_Router;


	public function __construct( $url ) {
		$this->url = $url;
	}


	/**
	 * @param $url
	 *
	 * @return Router
	 */
	public static function getInstance( $url ) {

		if ( self::$_Router == null ) {
			self::$_Router = new Router( $url );
		}

		return self::$_Router;
	}


	/**
	 * Ajoute un prefix au Routing
	 *
	 * @param $prefix
	 */
	public static function prefix( $prefix ) {
		array_push( self::$prefixes, $prefix );
	}


	public function autopost( $p ) {

		$prefix   = '';
		$params   = [ ];
		$pathArgs = null;

		// on enlève le '/' eventuellement présent en fin d'url (pour ne pas faire un bug avec le explode d'après)
		$p = trim( $p, '/' );

		// On sépare notre $p (qui coresspond à $_GET['p'])
		$parts = explode( '/', $p );

		// Si la taille de $parts > 2, il y a peut être un $prefix et/ou des $params
		if ( sizeof( $parts ) > 2 ) {

			// On vérifie s'il y a un prefix qui correspond
			$prefix = in_array( $parts[0], self::$prefixes ) ? ucfirst( $parts[0] ) . '\\' : '';

			// Si un prefix est défini
			if ( ! empty( $prefix ) ) {

				// on enlève le prefix de $parts
				array_shift( $parts );

			}

			// On définit le Controller
			$controller = ucfirst( $parts[0] ); // . 'Controller';

			// on enlève le controller de $parts
			array_shift( $parts );

			// On vérifie s'il y a une action
			if ( sizeof( $parts > 0 ) ) {

				// On définit l'action
				$action = $parts[0];

				// on enlève l'action de $parts
				array_shift( $parts );


				// On vérifie s'il y a des paramètres
				if ( sizeof( $parts > 0 ) ) {

					// On définit les paramètres
					foreach ( $parts as $k => $v ) {
						$params[ $k ] = $v;
					}

				}

			} else {

				// On définit l'action par défaut
				$action = 'index';
			}


			// On définit le $callable
			$callable = $controller . '.' . $action;

			// On récupère les arguments de l'action
			$pathArgs = $this->findArgs( $prefix, $controller, $action );


			// On définit le $path complet
			$path = strtolower( trim( $prefix, '\\' ) . DS . $controller . DS . $action );

		} else { // Si il n'y a pas de prefix ni de paramètres dans l'url

			// On définit le $callable
			$callable = ucfirst( implode( '.', $parts ) );


			// On récupère les arguments de l'action
			if ( sizeof( $parts ) > 1 ) {
				$pathArgs = $this->findArgs( $prefix, ucfirst( $parts[0] ), $parts[1] );
			} else {
				$pathArgs = $this->findArgs( $prefix, ucfirst( $parts[0] ) );
			}

			// On définit le $path
			$path = &$p;

		}


		// On supprime les résidus de $parts
		unset( $parts );


		// Permet d'accéder aux pages statiques sans 'statics/' dans l'url
		$path = str_ireplace('statics/', '', $path);

		// Si le callable est composé d'un seul mot (donc pas de la forme "Controller.action")
		if(!preg_match('/\./', $callable)) {
			// On vérifie si cette action existe dans les pages statiques
			$callable = $this->checkStaticAction($callable);
		}


		// On enregistre nos routes de type POST
		if ( ! empty( $pathArgs ) ) {
			foreach ( $pathArgs as &$args ) {
				$this->post( $path . $args, $callable, $prefix );
			}
		} else {
			$this->post( $path, $callable, $prefix );
		}

	}


	/**
	 * Function autoget()
	 * Détermine automatiquement les routes
	 * !! Perte de performances !!
	 * Il est recommandé de définir les routes manuellement avec la fonction get() pour un meilleur temps de réponse
	 *
	 * @param $p : $_GET['p']
	 */
	public function autoget( $p ) {

		$prefix   = '';
		$params   = [ ];
		$pathArgs = null;

		// on enlève le '/' eventuellement présent en fin d'url (pour ne pas faire un bug avec le explode d'après)
		$p = trim( $p, '/' );

		// On sépare notre $p (qui coresspond à $_GET['p'])
		$parts = explode( '/', $p );

		// Si la taille de $parts > 2, il y a peut être un $prefix et/ou des $params
		if ( sizeof( $parts ) > 2 ) {

			// On vérifie s'il y a un prefix qui correspond
			$prefix = in_array( $parts[0], self::$prefixes ) ? ucfirst( $parts[0] ) . '\\' : '';

			// Si un prefix est défini
			if ( ! empty( $prefix ) ) {

				// on enlève le prefix de $parts
				array_shift( $parts );

			}

			// On définit le Controller
			$controller = ucfirst( $parts[0] ); // . 'Controller';

			// on enlève le controller de $parts
			array_shift( $parts );

			// On vérifie s'il y a une action
			if ( sizeof( $parts > 0 ) ) {

				// On définit l'action
				$action = $parts[0];

				// on enlève l'action de $parts
				array_shift( $parts );


				// On vérifie s'il y a des paramètres
				if ( sizeof( $parts > 0 ) ) {

					// On définit les paramètres
					foreach ( $parts as $k => $v ) {
						$params[ $k ] = $v;
					}

				}

			} else {

				// On définit l'action par défaut
				$action = 'index';
			}


			// On définit le $callable
			$callable = $controller . '.' . $action;


			// On récupère les arguments de l'action
			$pathArgs = $this->findArgs( $prefix, $controller, $action );


			// On définit le $path complet
			$path = strtolower( trim( $prefix, '\\' ) . DS . $controller . DS . $action );

		} else { // Si il n'y a pas de prefix ni de paramètres dans l'url

			// On définit le $callable
			$callable = ucfirst( implode( '.', $parts ) );


			// On récupère les arguments de l'action
			if ( sizeof( $parts ) > 1 ) {
				$pathArgs = $this->findArgs( $prefix, ucfirst( $parts[0] ), $parts[1] );
			} else {
				$pathArgs = $this->findArgs( $prefix, ucfirst( $parts[0] ) );
			}

			// On définit le $path
			$path = &$p;

		}


		// On supprime les résidus de $parts
		unset( $parts );


		// Permet d'accéder aux pages statiques sans 'statics/' dans l'url
		$path = str_ireplace('statics/', '', $path);

		// Si le callable est composé d'un seul mot (donc pas de la forme "Controller.action")
		if(!preg_match('/\./', $callable)) {
			// On vérifie si cette action existe dans les pages statiques
			$callable = $this->checkStaticAction($callable);
		}


		// On enregistre nos routes de type GET
		if ( ! empty( $pathArgs ) ) {

			// Si des arguments optionnels sont trouvés à la fin
			$fullArgs = implode('', $pathArgs);
			if(preg_match_all('/:opt[0-9]+$/', $fullArgs)) {
				// Ajout de la route sans argument
				$this->get( $path, $callable, $prefix );
			}

			foreach ( $pathArgs as $args ) {
				$this->get( $path . $args, $callable, $prefix );
			}
		} else {
			$this->get( $path, $callable, $prefix );
		}

	}


	/**
	 * Function findArgs()
	 * Permet de chercher dans l'action d'un controller tous les arguments
	 *
	 * @param $prefix
	 * @param $controller
	 * @param $action
	 *
	 * @throws RouterException
	 *
	 * @return array : Tableau contenant toutes les possibilités
	 */
	private function findArgs( $prefix, $controller, $action = null ) {

		$pathArgs = '';
		$data     = [ ];


		// Si pas d'action, pas d'arguments.
		if ( $action == null ) {
			return $data;
		}


		// On récupère les paramètres de l'action
		$controllerClass = "Content\\Controller\\" . $prefix . $controller . 'Controller';

		// old usage
		//$reflect = new \ReflectionMethod( $controllerClass, $action );

		$reflect = new \ReflectionClass( $controllerClass);

		if(!$reflect->hasMethod($action)) {
			if ( Config::getInstance()->get( 'debug' ) > 0 ) {
				throw new RouterException("Unknown Method : $action");
			} else {
				App::getInstance()->redirect( '' );
			}
		} else {
			$reflect = new \ReflectionMethod( $controllerClass, $action );
		}

		// Nombre de paramètres requis
		$nbParams = $reflect->getNumberOfRequiredParameters();

		// Nombre de paramètres max
		$nbMaxParams = $reflect->getNumberOfParameters();


		// On ajoute les éventuels arguments au $path
		if ( $nbParams > 0 ) {
			for ( $i = 0; $i < $nbParams; $i ++ ) {
				$pathArgs .= DS . ":arg$i";
			}

			// Ajout de $pathArgs à $data
			array_push( $data, $pathArgs );
		}


		// Si il y a des arguments optionnels
		if ( $nbParams != $nbMaxParams ) {
			for ( $i = 0; $i < ( $nbMaxParams - $nbParams ); $i ++ ) {

				// On enrichi $pathArgs avec un parametre optionnel supplémentaire
				$pathArgs .= DS . ":opt$i";

				// Ajout de $pathArgs à $data
				array_push( $data, $pathArgs );
			}
		}


		return $data;
	}


	/**
	 * Check if passed action exists in StaticsController
	 * @param $action
	 * @return string : callable
	 */
	private function checkStaticAction($action) {

		// On récupère la liste des méthodes publiques définies dans le StaticsController
		$class = new ReflectionClass('Content\Controller\StaticsController');
		$class_methods = $class->getMethods(\ReflectionMethod::IS_PUBLIC);


		$class_methods_names = [];

		// On conserve seulement le nom des méthodes
		foreach($class_methods as $m) {
			$class_methods_names[] = $m->name;
		}

		// Liste des méthodes non autorisées
		$banned = array(
			"__construct",
			"render",
			"renderPhp",
			"renderTwig",
			"loadModel"
		);

		// Liste des méthodes autorisées
		$allowed_methods = array_diff($class_methods_names, $banned);

		// Si l'action n'est pas dans la liste autorisée
		if(!in_array(strtolower($action), $allowed_methods)) {
			// On renvoie le callable original
			return $action;
		}

		// Sinon on renvoie le callable statique
		return "Statics." . strtolower($action);
	}


	/**
	 * Function get()
	 * Défini une route en GET
	 *
	 * @param $path
	 * @param $callable
	 * @param string $prefix
	 * @param null $name
	 *
	 * @return Route
	 */
	public function get( $path, $callable, $prefix = '', $name = null ) {
		return $this->add( $path, $callable, $name, 'GET', $prefix );
	}


	/**
	 * Function post()
	 * Défini une route en POST
	 *
	 * @param $path
	 * @param $callable
	 * @param null $name
	 * @param $prefix
	 *
	 * @return Route
	 */
	public function post( $path, $callable, $prefix = '', $name = null ) {
		return $this->add( $path, $callable, $name, 'POST', $prefix );
	}


	/**
	 * Function add()
	 * Ajoute une route selon la méthode GET ou POST
	 *
	 * @param $path
	 * @param $callable
	 * @param $name
	 * @param $method
	 * @param string $prefix
	 *
	 * @return Route
	 */
	private function add( $path, $callable, $name, $method, $prefix = '' ) {
		$route                     = new Route( $path, $callable, $prefix );
		$this->routes[ $method ][] = $route;
		if ( is_string( $callable ) && $name === null ) {
			$name = $callable;
		}
		if ( $name ) {
			$this->namedRoutes[ $name ] = $route;
		}

		return $route;
	}

	public function run() {

		Debug::add( 'routes', $this->routes );

		if ( ! isset( $this->routes[ $_SERVER['REQUEST_METHOD'] ] ) ) {
			if ( Config::getInstance()->get( 'debug' ) > 0 ) {
				throw new RouterException("REQUEST_METHOD does not exist : " . $_SERVER['REQUEST_METHOD']);
			} else {
				App::getInstance()->redirect( '' );
			}
		}
		foreach ( $this->routes[ $_SERVER['REQUEST_METHOD'] ] as $route ) {

			if ( $route->match( $this->url ) ) {
				return $route->call();
			}
		}

		//App::getInstance()->redirect('');
		throw new RouterException('No matching routes');
	}

	public function url( $name, $params = [ ] ) {
		if ( ! isset( $this->namedRoutes[ $name ] ) ) {
			throw new RouterException( 'No route matches this name' );
		}

		return $this->namedRoutes[ $name ]->getUrl( $params );
	}

}