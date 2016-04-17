<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 18/09/15
 * Time: 13:36
 */

namespace Core\Helper;

/**
 * Class Debug
 * Permet d'afficher proprement des variables à débugger.
 * !! Seulement avec MaterializeCSS !!
 *
 * Exemple : Debug::add('GET', $_GET);
 *           Debug::add('POST', $_POST);
 *
 * @package Core\Helper
 */
class Debug extends Helper {


    private static $debug = [];


    /**
     * Function add()
     * Permet d'ajouter une variable à débugger.
     *
     * @param string $title : titre de la variable
     * @param mixed $var : variable à debugger
     */
    public static function add($title, $var) {
        self::$debug[strtoupper($title)] =  $var;
    }


    /**
     * Function getDebug()
     * Récupère l'instance de $debug et affiche son contenu si pas vide.
     */
    public static function getDebug() {

        if(!empty(self::$debug))
            self::show(self::$debug);

    }


    /**
     * Met en forme et affiche $debug
     * @param $debugs
     */
    private static function show($debugs) {

        echo '<a class="waves-effect waves-light btn-floating btn-large modal-trigger blue lighten-1"
                 style="position: fixed; top: 75px; right: 20px;" href="#modal">
                    <i class="material-icons large">info</i>
              </a>';
        echo '<div id="modal" class="blue lighten-1 white-text modal bottom-sheet">
                    <div class="row">
                        <div class="section">
                            <div class="col s12">
                                <h4> DEBUG</h4>
                                <pre>';

                                    foreach($debugs as $k => &$debug) {
                                        echo '<blockquote>';
                                        echo '<h5>'.strtoupper($k).'</h5>';
                                        print_r($debug);
                                        echo '</blockquote><br><br>';
                                    }

        echo                    '</pre>
                            </div>
                        </div>
                    </div>
              </div>';

    }

}