<?php

    namespace Core;


    /**
     * Class Autoloader
     * Permet de charger automatiquement toutes les classes de Core
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
         */
        static function autoload ($class) {

            if (strpos ($class, __NAMESPACE__ . '\\') === 0) {

                $class = str_replace (__NAMESPACE__ . '\\', '', $class);

                $class = str_replace ('\\', '/', $class);

                require __DIR__ . DS . $class . '.php';

            }


        }

    }