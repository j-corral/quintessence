<?php

    namespace Content;
    use Core\App;
    use Core\Config;
    use Core\Router\RouterException;


    /**
     * Class Autoloader
     * Permet de charger automatiquement toutes les classes de Content
     * @package app
     */
    class Autoloader {


        /**
         * Enregistre l'autoloader
         */
        static function register () {
            spl_autoload_register (array (__CLASS__, 'autoload'));
        }


        /**
         * Inclue le fichier correspondant à la classe
         *
         * @param $class String : Nom de la classe à charger
         *
         * @throws RouterException
         */
        static function autoload ($class) {

            if (strpos ($class, __NAMESPACE__ . '\\') === 0) {

                $class = str_replace (__NAMESPACE__ . '\\', '', $class);

                $class = str_replace ('\\', '/', $class);

                if(! file_exists(__DIR__ . DS . $class . '.php')) {
                    if ( Config::getInstance()->get( 'debug' ) > 0 ) {
                        throw new RouterException("CLASS does not exist : " . $class . '.php');
                    } else {
                        App::getInstance()->redirect( '' );
                    }
                }

                require_once __DIR__ . DS . $class . '.php';

            }


        }

    }