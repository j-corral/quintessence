<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 07/09/15
 * Time: 09:48
 */

namespace Core\Helper;


class Helper {


    /**
     * Entoure du contenu HTML avec des balises
     *
     * @param string $html    contenu à entourer
     * @param string $tag     type de balise englobante
     * @param array  $options id et classes de la balise
     *
     * @return string
     */
    protected function surround ($html, $tag = 'div', $options = array ()) {

        $id = isset($options['id']) ? " id=\"{$options['id']}\"" : '';

        $class = isset($options['class']) ? " class=\"{$options['class']}\"" : '';

        $type = isset($options['type']) ? " type=\"{$options['type']}\"" : '';

        $name = isset($options['name']) ? " name=\"{$options['name']}\"" : '';

        $for = isset($options['for']) ? " for=\"{$options['for']}\"" : '';

        $success = isset($options['data-success']) ? " data-success=\"{$options['data-success']}\"" : '';

        $error = isset($options['data-error']) ? " data-error=\"{$options['data-error']}\"" : '';


        return "<{$tag}{$id}{$type}{$for}{$name}{$class}{$success}{$error}>{$html}</{$tag}>";
    }

}