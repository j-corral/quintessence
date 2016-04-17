<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 11/09/15
 * Time: 14:29
 */

namespace Core\Helper;


class Form extends Helper {

	protected $id;

	protected $csrfToken;

	protected $data;


	public function __construct( $id, $data = [ ] ) {
		$this->id   = ucfirst( $id );
		$this->data = $data;
	}


	/**
	 * Ouvre le formulaire
	 *
	 * @param array $options
	 *      title,
	 *      method,
	 *      action,
	 *      class,
	 *
	 * @return string
	 */
	public function start( $options = [ ] ) {

		$title  = isset( $options['title'] ) ? "{$options['title']}" : '';
		$method = isset( $options['method'] ) ? "{$options['method']}" : 'post';
		$action = isset( $options['action'] ) ? "{$options['action']}" : '';
		$class  = isset( $options['class'] ) ? "class=\"{$options['class']}\"" : '';

		$action = ! empty( $action ) ? Html::link( $action ) : '';
		$title  = ! empty( $title ) ? parent::surround( $title, 'h3' ) : '';

		$form = "<form id=\"{$this->id}\" method=\"{$method}\" action=\"{$action}\" class=\"form {$class}\"   > $title";


		$csrf = $this->init_csrf_token();

		$form .= $csrf;

		return $form;
	}


	/**
	 * Ferme le formulaire
	 * @return string
	 */
	public function end() {
		return "</form>";
	}


	/**
	 * Active le javascript pour l'affichage des erreurs
	 * @return string
	 */
	public function json() {
		return "<script src=\"". BASE_URL . JS . "showmessage.js\"></script>
		<script>showmessage(\"{$this->id}\");</script>";
	}

	public function error() {
		return "<div id=\"result\"></div>";
	}


	/**
	 * Génère un token pour se prémunir des failles CSRF
	 */
	private function generate_csrf_token() {
		$this->csrfToken      = uniqid( rand(), true );
		$_SESSION['csrf']     = $this->csrfToken;
		$_SESSION['csrfTime'] = time();
	}

	/**
	 * Ajoute un champs caché contenant le token
	 * @return string
	 */
	public function init_csrf_token() {

		$this->generate_csrf_token();

		$input = $this->input( 'token', [
			"type"  => "hidden",
			"value" => "$this->csrfToken",
		] );

		$csrf = "<input type=\"hidden\" name=\"data[{$this->id}][token]\" value=\"{$this->csrfToken}\" />";

		return $csrf;
	}


	/**
	 * Créé un input avec label et icone possible
	 *
	 * @param       $name
	 * @param array $options
	 *      type : input type (text,email, number, ...),
	 *      class : to stylish input (validate),
	 *      icon : to add a Google Material icon (phone),
	 *      label : To add a label (Phone)
	 *
	 * @return string
	 */
	public function input( $name, $options = [ ] ) {

		$data_error = isset( $options['data-error'] ) ? $options['data-error'] : '' ;

		$data_success = isset( $options['data-success'] ) ? $options['data-success'] : '' ;

		$label = isset( $options['label'] ) ? parent::surround(
			$options['label'],
			'label',
			[
				"for" => $name,
				"data-error" => $data_error,
				"data-success" => $data_success,
			]
		) : '';

		$id = "id=\"{$name}\"";

		$name = "name=\"data[$this->id][{$name}]\"";

		$type = isset( $options['type'] ) ? "type=\"{$options['type']}\"" : '';

		$class = isset( $options['class'] ) ? "class=\"{$options['class']}\"" : '';

		$value = isset( $options['value'] ) ? "value=\"{$options['value']}\"" : '';

		$placeholder = isset( $options['placeholder'] ) ? "placeholder=\"{$options['placeholder']}\"" : '';

		$attrs = '';
		if (isset( $options['attr'] ) && is_array( $options['attr'] )) {
			foreach ($options['attr'] as $attr => $val) {
				$attrs .= "{$attr}=\"{$val}\" ";
			}
		}

		$icon = isset( $options['icon'] ) ? parent::surround(
			$options['icon'],
			'i',
			[ "class" => "material-icons prefix", ]
		) : '';


		$input = "{$icon} <input {$id} {$name} {$type} {$value} {$class} {$placeholder} {$attrs} /> {$label}";

		return $input;
	}


	public function select( $name, $options = [ ] ) {

		$label = isset( $options['label'] ) ? parent::surround(
			$options['label'],
			'label',
			[
				"for" => $name,
			]
		) : '';

		$id = "id=\"{$name}\"";

		$name = "name=\"data[$this->id][{$name}]\"";

		$class = isset( $options['class'] ) ? "class=\"{$options['class']}\"" : '';

		$opt = '';
		if (  isset( $options['opt'] ) ) {
			$selectOptions = '';
			foreach ( $options['opt'] as $k => $v ) {
				$selectOptions .= '<option value="'.$k.'">'.$v.'</option>';
			}
			$opt = $selectOptions;
		}

		$value = isset( $options['value'] ) ? "{$options['value']}" : '';

		$placeholder = isset( $options['placeholder'] ) ? "placeholder=\"{$options['placeholder']}\"" : '';

		$icon = isset( $options['icon'] ) ? parent::surround(
			$options['icon'],
			'i',
			[ "class" => "material-icons prefix", ]
		) : '';


		$select = "{$icon} <select {$id} {$name} {$class} {$placeholder} />{$opt} {$value}</select> {$label}";

		return $select;
	}


	/**
	 * Créé un textarea avec label et icone possible
	 *
	 * @param       $name
	 * @param array $options
	 *      type : input type (text,email, number, ...),
	 *      class : to stylish input (validate),
	 *      icon : to add a Google Material icon (phone),
	 *      label : To add a label (Phone)
	 *
	 * @return string
	 */
	public function textarea( $name, $options = [ ] ) {

		$label = isset( $options['label'] ) ? parent::surround(
			$options['label'],
			'label',
			[
				"for" => $name,
			]
		) : '';

		$id = "id=\"{$name}\"";

		$name = "name=\"data[$this->id][{$name}]\"";

		$class = isset( $options['class'] ) ? "class=\"materialize-textarea {$options['class']}\"" : 'class="materialize-textarea "';

		$placeholder = isset( $options['placeholder'] ) ? "placeholder=\"{$options['placeholder']}\"" : '';

		$icon = isset( $options['icon'] ) ? parent::surround(
			$options['icon'],
			'i',
			[ "class" => "material-icons prefix", ]
		) : '';


		$textarea = "{$icon} <textarea {$id} {$name} {$class} {$placeholder} ></textarea> {$label}";


		return parent::surround( $textarea, 'div', [ 'class' => "input-field col s6" ] );
	}


	public function submit( $name, $options = [ ] ) {

		$button = parent::surround(
			$name,
			'button',
			$options = [
				"id"   => "submit",
				"type" => "submit",
				"name" => "action",
			]
		);

		return $button;
		/*return '
		<div class="row">
			<button id="submit" class="btn waves-effect waves-light col offset-s6 s2" type="submit" name="action">
				<i class="material-icons">lock_open</i>
				$name
			</button>
		</div>
	';*/

	}


	public function getValue( $index ) {
		return isset( $this->data[ $index ] ) ? $this->data[ $index ] : null;
	}


}