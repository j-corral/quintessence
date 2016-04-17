<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 29/03/16
 * Time: 15:10
 */

namespace Content\Controller\Admin;

use Content\Table\PermissionTable;

use Core\App;
use Core\Auth\DBAuth;

/**
 * Class PermissionsController
 * @package Content\Controller\Admin
 * @property PermissionTable $Permission
 */
class PermissionsController extends AppController {

	public function index( $page = 1 ) {

		if ( ! App::getAuth()->hasAccess( 'permissions.index' ) ) {
			throw new \Exception( 'Access denied !' );
		}

		$nbPerPage = ( isset( $this->request->get->perPage ) ? $this->request->get->perPage : 25 );

		// orderby sur les <> collones
		$orderby = ( isset( $this->request->get->orderby ) ? $this->request->get->orderby : "0" );
		// Pagination
		$paginate = $this->paginate( 'admin.permissions.index', $nbPerPage, $page, $orderby );

		$permissions = $paginate['req'];
		$nbPages     = $paginate['nbPages'];
		$page        = $paginate['page'];
		$total       = $paginate['total'];

		// vue
		$this->render( 'admin.permissions.index', compact( 'permissions', 'nbPages', 'page', 'limitelem', 'total' ) );
	}

	public function read( $id ) {
		if ( ! App::getAuth()->hasAccess( 'permissions.read' ) ) {
			throw new \Exception( 'Access denied !' );
		}

		$permission = $this->Permission->findById( $id );


		$this->render( 'admin.permissions.read', compact( 'permission' ) );
	}

	public function delete( $id ) {

		if ( ! App::getAuth()->hasAccess( 'permissions.delete' ) ) {
			throw new \Exception( 'Access denied !' );
		}

		if ( ! $this->Permission->deleteRow( $id ) ) {
			die( 'Impossibe de supprimer ' . $id );
		}

		App::getInstance()->redirect( 'admin.permissions.index' );
	}


	public function deleteselection() {

		if ( ! App::getAuth()->hasAccess( 'permissions.delete' ) ) {
			throw new \Exception( 'Access denied !' );
		}
		// tableau des id selectionnés
		$data = (array) $this->request->post->delete;

		if ( ! $this->Permission->deleteRowSelection( $data ) ) {
			die( 'Impossibe de supprimer ' );
		}

		App::getInstance()->redirect( 'admin.permissions.index' );
	}


	public function save() {
		$form = $this->request->post->Permission;

		if ( ! $this->check_csrf( $form->token ) ) {
			App::getInstance()->redirect( 'admin.permissions.index' );
		}

		$auth_id    = filter_var( $form->auth_id, FILTER_SANITIZE_STRING );
		$permission = filter_var( $form->permission, FILTER_SANITIZE_STRING );

		if ( ! isset( $form->id ) ) {


			$this->Permission->insertRow( compact( 'auth_id', 'permission' ) );
		} else {
			$this->Permission->updateRow( compact( 'auth_id', 'permission' ), $form->id );
		}

		App::getInstance()->redirect( 'admin.permissions.index' );
		//$this->render( 'admin.permissions.save' );
	}

	public function getFormData( $data ) {
		$formData = [ ];

		$auth_id    = "";
		$permission = "";

		// si $data vide --> create et non update
		if ( ! empty( $data ) ) {
			$id         = $data->id;
			$permission = $data->permission;
			$auth_id    = intval( $data->auth_id );

			$formData[] = [
				'type' => 'input',
				'name' => 'id',
				'args' => [
					'type'  => 'text',
					'value' => $id,
					'label' => 'Id',
					'attr'  => [
						'readonly' => ''
					]
				]
			];

		}

		$formData[] = [
			'type' => 'input',
			'name' => 'auth_id',
			'args' => [
				'type'  => 'number',
				'value' => $auth_id,
				'label' => 'Auth_id'
			]
		];

		$formData[] = [
			'type' => 'input',
			'name' => 'permission',
			'args' => [
				'type'  => 'text',
				'value' => $permission,
				'label' => 'Permission'
			]
		];

		$formData[] = [
			'type' => 'submit',
			'name' => 'valider',
			'args' => [
			]
		];


		return $formData;
	}

	public function getForm() {
		return [
			'name' => 'Permission',
			'data' => [
				"title"  => "Ajouter une permission",
				"method" => "post",
				"action" => "admin.permissions.save"
			]
		];
	}

	public function getFormEdit() {
		return [
			'name' => 'Permission',
			'data' => [
				"title"  => "Editer une Permission",
				"method" => "post",
				"action" => "admin.permissions.save"
			]
		];
	}


	public function update( $id ) {

		if ( ! App::getAuth()->hasAccess( 'permissions.update' ) ) {
			throw new \Exception( 'Access denied !' );
		}

		$permission = $this->Permission->findById( $id );

		$formData = $this->getFormData( $permission );
		$form     = $this->getFormEdit( $id );


		$this->render( 'admin.crud.edit', compact( 'formData', 'form', 'permission' ) );
	}

}