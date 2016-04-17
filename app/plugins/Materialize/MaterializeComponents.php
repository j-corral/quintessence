<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 13/03/16
 * Time: 17:21
 */

namespace Plugins\Materialize;


use Core\Helper\Html;

class MaterializeComponents {


	/**
	 * @param $action
	 * @param $page
	 * @param $nbPages
	 * @param string $color
	 * @param array $GET : Liste des GET supplémentaires autorisés
	 *
	 * @return string
	 */
	public function pagination($action, $page, $nbPages, $color = '', $GET) {

		$content = '<ul class="pagination">';

                    if($page > 1) {
	                    $url = Html::link($action, [$page - 1], $GET);
	                    $content .= '<li class="waves-effect">
                            <a href="'.$url.'" class="tooltipped" data-position="top" data-delay="150" data-tooltip="Précédent">
                                <i class="material-icons">chevron_left</i>
                            </a>
                        </li>';
                    }
                    $minP = $page - 4;

					if($minP <= 0) {
						$minP = 1;
					}

					$maxP = $page + 4;

					if($maxP > $nbPages) {
						$maxP = $nbPages;
					}

					for($p = $minP; $p < $maxP; ++$p) {
						if($p == $page) {
							$url = Html::link($action, [$page], $GET);
							$content .= '<li class="active '.$color.'"><a href="'.$url.'">'.$page.'</a></li>';
						} else {
							$url = Html::link($action, [$p], $GET);
							$content .= '<li class="waves-effect"><a href="'.$url.'">'.$p.'</a></li>';
						}
					}


					if($page < $nbPages) {
						$url = Html::link($action, [$page + 1], $GET);
						$content .= '<li class="waves-effect">
                            <a href="'.$url.'" class="tooltipped" data-position="top" data-delay="150" data-tooltip="Suivant">
                                <i class="material-icons">chevron_right</i>
                            </a>
                        </li>';

					}
		
				$content.= '<li><input type="number" name="nbrelem" id="nbrelem" placeholder="Élements par page"></li>';
                $content .= '</ul>';

		return $content;
	}




}