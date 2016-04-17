<?php
/**
 * Created by PhpStorm.
 * User: crozet
 * Date: 28/01/2016
 * Time: 19:42
 */

namespace Plugins\Twig;


use Core\App;
use Core\Auth\DBAuth;
use Core\Config;

class TwigHelpers extends \Twig_Extension
{

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'TwigHelpers';
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('call_static',  [$this, 'callStatic']),
            new \Twig_SimpleFunction('app_function', [$this, 'appFunction']),
            new \Twig_SimpleFunction('config_get',   [$this, 'configGet']),
            new \Twig_SimpleFunction('isLogged',   [$this, 'isLogged']),
            new \Twig_SimpleFunction('isAdmin',   [$this, 'isAdmin']),
            new \Twig_SimpleFunction('hasAccess',   [$this, 'hasAccess']),
            new \Twig_SimpleFunction('getAuth',   [$this, 'getAuth']),
            new \Twig_SimpleFunction('url',   [$this, 'url']),
            new \Twig_SimpleFunction('img',   [$this, 'img']),
            new \Twig_SimpleFunction('js',   [$this, 'js']),
            new \Twig_SimpleFunction('css',   [$this, 'css']),
            new \Twig_SimpleFunction('getTitle',   [$this, 'getTitle']),
            new \Twig_SimpleFunction('getBaseUrl',   [$this, 'getBaseUrl']),
            new \Twig_SimpleFunction('getDebug',   [$this, 'getDebug']),
        ];
    }

    public function callStatic($class, $func, $args = []){
        return call_user_func_array([$class, $func], $args);
    }

    public function appFunction($func, $args = []){
        $app = App::getInstance();
        return call_user_func_array([$app, $func], $args);
    }

    public function configGet($key){
        $config = Config::getInstance();
        return $config->get($key);
    }

    public function isLogged() {
        $auth = new DBAuth(App::getDBInstance());
        return $auth->logged();
    }

    public function isAdmin() {
        $auth = new DBAuth(App::getDBInstance());
        return $auth->isAdmin();
    }

    public function hasAccess($access) {
        if(App::getAuth()->hasAccess($access)) {
            return true;
        }

        return false;
    }

    public function getAuth($key) {
        return App::getAuth()->getAuth($key);
    }

    public function url($link, $args = [], $get = []) {
        return $this->callStatic('Core\\Helper\\Html', 'link', [$link, $args, $get]);
    }

    public function css($link) {
        return $this->callStatic('Core\\Helper\\Html', 'css', [$link]);
    }

    public function img($link) {
        return $this->callStatic('Core\\Helper\\Html', 'img', [$link]);
    }

    public function js($link) {
        return $this->callStatic('Core\\Helper\\Html', 'js', [$link]);
    }


    public function getTitle() {
        return $this->appFunction('getTitle');
    }


    public function getBaseUrl() {
        return $this->appFunction('getBaseUrl');
    }


    public function getDebug() {
        return $this->appFunction('getDebug');
        //return $this->call_static('Core\\Helper\\Debug', 'getDebug');
    }

}