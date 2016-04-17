<?php
    /**
     * Created by PhpStorm.
     * User: jonathan
     * Date: 07/09/15
     * Time: 00:49
     */

    namespace Core;


    class Config {

        private $settings = [];

        private static $_Config;


        public function __construct () {

            // Chargement de la configuration principale
            $config = require_once ROOT . CONFIG . 'config.php';

            // Chargement de la liste des utilisateurs SQL
            $users = [];

            if(file_exists(ROOT . CONFIG . 'usersDB'. DS .'users.config.php')) {
                $users = require_once ROOT . CONFIG . 'usersDB'. DS .'users.config.php';
            }

            $this->settings = array_merge($config, $users);
        }


        /**
         * Renvoie une instance unique de la configuration
         * @return Config
         */
        public static function getInstance () {
            if (self::$_Config == null)
                self::$_Config = new Config();

            return self::$_Config;
        }


        /**
         * Récupère la valeur associée à la clef
         *
         * @param $key
         * @param $index
         *
         * @return null | $value
         */
        public function get ($key, $index = null) {

            if (!isset($this->settings[$key])) {
                return null;
            }

            if($index != null) {
                return $this->settings[$key][$index];
            } else {
                return $this->settings[$key];
            }

        }

    }