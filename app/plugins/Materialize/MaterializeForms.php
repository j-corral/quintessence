<?php
    /**
     * Created by PhpStorm.
     * User: jonathan
     * Date: 27/12/15
     * Time: 16:20
     */
    
    namespace Plugins\Materialize;
    
    
    use Core\Helper\Form;
    use Core\Helper\Html;

    class MaterializeForms extends Form {

        public function __construct($id, $data = []) {
            parent::__construct($id, $data);
        }

        public function setId($id) {
            $this->id = $id;
        }


        public function start($options = []) {

            $title = isset($options['title']) ? "{$options['title']}" : '';
            $method = isset($options['method']) ? "{$options['method']}" : 'post';
            $action = isset($options['action']) ? "{$options['action']}" : '';
            $class = isset($options['class']) ? "class=\"{$options['class']}\"" : '';

            $action = !empty($action) ? Html::link($action) : '';
            $title = !empty($title) ? parent::surround($title, 'h3', ["class" => "col offset-s0 s12 offset-m2 m8 offset-l3 l6"]) : '';

            $form = "<form id=\"{$this->id}\" method=\"{$method}\" action=\"{$action}\" {$class}  > $title";

            $csrf = $this->init_csrf_token();

            $form .= $csrf;

            return $form;
        }


        public function end() {
            return parent::end();
        }

        public function json() {
            return parent::json();
        }

        public function error() {
            return parent::error();
        }


        public function input($name, $options = []) {

            $parent_class = isset( $options['parent_class'] ) && !empty($options['parent_class']) ? $options['parent_class'] : 'col offset-s0 s12 offset-m2 m8 offset-l3 l6';
            unset($options['parent_class']);

            $input = parent::input($name, $options);

            return parent::surround($input, 'div', [
                'class' => "input-field $parent_class",

            ]);


        }


        public function select($name, $options = []) {

            $parent_class = isset( $options['parent_class'] )  && !empty($options['parent_class']) ? $options['parent_class'] : 'col offset-s0 s12 offset-m2 m8 offset-l3 l6';
            unset($options['parent_class']);

            $input = parent::select($name, $options);

            return parent::surround($input, 'div', [
                'class' => "input-field $parent_class",
            ]);
        }

        public function textarea($name, $options = []) {
            return parent::textarea($name, $options);
        }

        public function submit($name, $options = []) {

            $icon = !empty($options['icon']) ? parent::surround(
                $options['icon'],
                'i',
                [
                    "class" => "material-icons right",
                ]
            ) : '';

            $class = isset($options['class']) ? "{$options['class']}" : '';

            $button = parent::surround(
                $name . ' ' . $icon,
                'button',
                $options = [
                    "id"    => "submit",
                    "type"  => "submit",
                    "name"  => "action",
                    "class" => "btn waves-effect waves-light $class",
                ]
            );

            return parent::surround($button, 'div', ['class' => 'row']);
        }

        public function getValue($index) {
            return parent::getValue($index);
        }
        
    }