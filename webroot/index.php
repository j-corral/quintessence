<?php
    // Définition des constantes pour accéder aux fichiers

    /* GLOBAL */
    define ('DS', DIRECTORY_SEPARATOR);
    define ('ROOT', dirname (__FILE__) . DS . '..' . DS);

    /* APP */
    define ('APP', 'app' . DS);
    define ('CORE', APP . 'core' . DS);

    use Content\Controller;
    use Content\Table;
    use Core\App;
    use Core\Config;

    // Chargement du bootstrap
    require_once ROOT . CORE . 'bootstrap.php';

    // Chargement de l'application
    require_once ROOT . CORE . 'App.php';

    App::load ();

    // Initialisation d'une instance APP unique et utilisable dans toute l'application
    $app = App::getInstance ();


    // Récupération de la Configuration
    $config = Config::getInstance ();

    // Affichage des erreurs à l'écran
    if ($config->get ('debug') > 0) {
        ini_set ("display_errors", 1);
        error_reporting (-1);
    } else {
        ini_set ("display_errors", 0);
        error_reporting (0);
    }

    $port = $_SERVER['SERVER_PORT'] != '80' ? ':'.$_SERVER['SERVER_PORT'] : '';

    $http = $config->get("ssl") === true ? 'https': 'http';

    // Constante URL
    define ('BASE_URL', "$http://" . $_SERVER['SERVER_NAME'] . $port . '/' . $config->get ('base_url'));

    // Titre du site
    $app->setTitle ($config->get ('title'));

    /* Récupération de l'instance de la BD */
    //$app->getDBInstance ();

    /* Lancement de l'application */
    $app->run ();