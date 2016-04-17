<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 16/09/15
 * Time: 16:10
 */

namespace Core\Router;


use Core\App;
use Core\Helper\Debug;

class Route {

    private $path;
    private $callable;
    private $prefix;
    private $matches = [];
    private $params = [];

    /**
     * Route constructor.
     * @param $path
     * @param $callable
     * @param $prefix
     */
    public function __construct($path, $callable, $prefix = '') {
        $this->path = trim($path, '/');
        $this->callable = $callable;
        $this->prefix = $prefix;
    }


    public function match($url) {

        $url = trim($url, '/');
        $path = str_replace("\\", "/",preg_replace_callback('#:([\w]+)#', [$this, 'paramMatch'], $this->path)); // problem windows !!
        $regex = "#^$path$#i";

        if(!preg_match($regex, $url, $matches))
            return false;

        array_shift($matches);

        $this->matches = $matches;


        return true;
    }


    private function paramMatch($match) {
        if(isset($this->params[$match[1]]))
            return  '(' . $this->params[$match[1]] . ')';

        return '([^/]+)';
    }


    public function call() {

        Debug::add('CallableRoute', $this->callable);

        if(is_string($this->callable)) {

            // Si page == 'home => on affiche la page d'accueil
            if($this->callable === 'home') {
                $twigEngine = App::getInstance()->getTwig();

                $layout = $twigEngine->loadTemplate("default" . '.twig');
                $vars['_layout'] = $layout;

                echo $twigEngine->render("home" . '.twig', $vars);
                exit;
            }

            $page = strtolower(str_ireplace('\\', '/', $this->prefix) . str_ireplace('.', DS, $this->callable));

            /*if (!file_exists (ROOT . VIEW . $page . ".twig")) {
               App::getInstance()->redirect('');
            } else {*/

                $params = explode('.', $this->callable);

                $controller = "Content\\Controller\\" . $this->prefix . $params[0] . 'Controller';

                $controller = new $controller();

                return call_user_func_array([$controller, $params[1]], $this->matches);
            //}
        } else {
            return call_user_func_array($this->callable, $this->matches);
        }

        return false;
    }


    public function with($param, $regex) {
        $this->params[$param] = str_replace('(', '(?:', $regex);

        return $this;
    }


}