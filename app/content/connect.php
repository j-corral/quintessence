<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 31/01/16
 * Time: 16:53
 */

use Core\Router\Router;

/**
 * PREFIXES
 * Define prefixes here
 * use Router::prefix()
 */

/* Ajout d'un préfix admin au Router */
Router::prefix( 'admin' );


/**
 * ROUTES
 * Define manual routes here
 * use $router->get() or $router->post()
 */
//$router->get( 'alias', 'Controller.action', 'Prefix\\' = null );

$router->get( 'login', 'Users.login' );
$router->get( 'logout', 'Users.logout' );


$router->get( 'admin', 'Posts.index', 'Admin\\' );

