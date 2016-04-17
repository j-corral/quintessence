<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 07/09/15
 * Time: 09:49
 */

namespace Core\Helper;


class Html extends Helper {


	/**
	 * Permet de charger facilement un fichier CSS
	 *
	 * @param $filename
	 *
	 * @return null|string
	 */
	public static function css( $filename ) {

		if ( file_exists( ROOT . WEBROOT . CSS . $filename . '.css' ) ) {
			return BASE_URL . CSS . $filename . '.css';
		}

		return null;
	}


	/**
	 * Permet de charger facilement un fichier JS
	 *
	 * @param $filename
	 *
	 * @return null|string
	 */
	public static function js( $filename ) {

		if ( file_exists( ROOT . WEBROOT . JS . $filename . '.js' ) ) {
			return BASE_URL . JS . $filename . '.js';
		}

		return null;
	}


	/**
	 * Function img()
	 * Permet de charger facilement une image
	 *
	 * @param $filename
	 *
	 * @return null|string
	 */
	public static function img( $filename ) {

		if ( file_exists( ROOT . WEBROOT . IMG . $filename ) ) {
			return BASE_URL . IMG . $filename;
		}

		return null;
	}


	/**
	 * Function link()
	 * Génère un lien
	 *
	 * @param String $link
	 * @param mixed $args
	 * @param mixed $opt : liste des GET supplementaires (orderby, perPage,...)
	 *
	 * @return string
	 */
	public static function link( $link, $args = [], $opt = [] ) {
		$link = str_replace( '.', '/', $link );

		$params = isset( $args ) && !empty($args) ? '/' . implode( '/', $args ) : '';

		$optGET = self::optGET($link . $params, $opt);

		return BASE_URL . $link . $params . $optGET;
	}


	/**
	 * Récupère les GET supplémentaires, en vue d'un transfert sur une autre page par exemple.
	 * @param String $link
	 * @param array $options
	 *
	 * @return string
	 */
	private static function optGET($link, $options) {
		
		if(substr($link, -1) != '/') {
			if(strstr($link, '?')) {
				$operator = '&';
			} else {
				$operator = '/?';
			}
		} else {
			$operator = '?';
		}


		$params = '';

		if(isset($_GET) && !empty($options)) {


			foreach ($options as $opt) {

				if(isset($_GET[$opt]) && !empty($_GET[$opt])) {
					$params .= $operator . $opt . '=' . $_GET[$opt];

					$operator = '&';
				}

			}


		}


		return $params;
	}

}