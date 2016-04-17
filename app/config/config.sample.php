<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 07/09/15
 * Time: 00:56
 */


/**
 * Fichier de configuration
 *
 * Permet d'effectuer la configuration de Quintessence Framework
 */


return array(

	// Si debug actif, affichage des erreurs PHP et SQL + temps execution page
	"debug"              => 1,

	// Layouts du site
	"layout"             => [
		"default" => "default",
		//"admin"   => "admin"
	],

	/**
	 * Url complémentaire du site (facultatif)
	 *
	 * Si votre application ne se trouve pas à la racine
	 * Exemple : si votre site est dans www.monsite.fr/dossier/MCMS
	 * alors "base_url" => "dossier/MCMS/webroot/"
	 * Sinon laissez base_url "webroot/".
	 * 
	 * !! Si vous utilisez un vhost, base_url doit etre vide.
	 * 
	 * */
	"base_url"           => "webroot/",

	// Si vous n'utilisez pas de https laissez false
	"ssl"                => false,

	// Titre du site
	"title"              => "Quintessence Framework",

	/**
	 * Connexion à la BD
	 *
	 * db_engine : mysql
	 *
	 * Laissez le port vide si vous utilisez une configuration par défaut
	 * de mysql (port par défaut : 3306)
	 * */
	"db_engine"          => "mysql",
	"db_name"            => "",
	"db_host"            => "",
	"db_port"            => "",
	"db_user"            => "",
	"db_password"        => "",


	// Email
	"email"              => "",

	// Twig configuration
	/**
	 * Chemin du dossier des templates twig relatif à la racine du projet
	 * Possibilité de mettre un tableau de dossiers.
	 * Twig cherchera les templates dans tous les dossiers renseignés dans l'ordre.
	 */
	"twig_template_path" => [
		'app/content/Layout',
		'app/content/View',
	],

	// Activer le mode debug de twig
	"debug_twig" => true,
	
	
);