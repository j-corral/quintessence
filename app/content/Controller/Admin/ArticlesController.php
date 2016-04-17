<?php
/**
 * Created by PhpStorm.
 * User: franck
 * Date: 10/03/2016
 * Time: 15:31
 */

namespace Content\Controller\Admin;

use Content\Table\ArticleTable;
use Core\App;

/**
 * Class ArticlesController
 * @package Content\Controller\Admin
 * @property ArticleTable $Article
 */
class ArticlesController extends AppController {

	public function index( $page = 1 ) {

		if ( ! App::getAuth()->hasAccess( 'articles.index' ) ) {
			throw new \Exception( 'Access denied !' );
		}

		$nbPerPage = ( isset( $this->request->get->perPage ) ? $this->request->get->perPage : 25 );

		// orderby sur les <> collones
		$orderby = ( isset( $this->request->get->orderby ) ? $this->request->get->orderby : "0" );

		// Pagination
		$paginate = $this->paginate( 'admin.articles.index', $nbPerPage, $page, $orderby );


		$articles = $paginate['req'];
		$nbPages  = $paginate['nbPages'];
		$page     = $paginate['page'];
		$total    = $paginate['total'];

		// vue
		$this->render( 'admin.articles.index', compact( 'articles', 'nbPages', 'page', 'limitelem', 'total' ) );
	}

	public function read( $id ) {

		if ( ! App::getAuth()->hasAccess( 'articles.read' ) ) {
			throw new \Exception( 'Access denied !' );
		}

		$article = $this->Article->findById( $id );

		$this->render( 'admin.articles.read', compact( 'article' ) );
	}

	public function delete( $id ) {

		if ( ! App::getAuth()->hasAccess( 'articles.delete' ) ) {
			throw new \Exception( 'Access denied !' );
		}

		if ( ! $this->Article->deleteRow( $id ) ) {
			die( 'Impossibe de supprimer ' . $id );
		}

		App::getInstance()->redirect( 'admin.articles.index' );
	}


	public function deleteselection() {

		if ( ! App::getAuth()->hasAccess( 'articles.delete' ) ) {
			throw new \Exception( 'Access denied !' );
		}
		// tableau des id selectionnés
		$data = (array) $this->request->post->delete;

		if ( ! $this->Article->deleteRowSelection( $data ) ) {
			die( 'Impossibe de supprimer les articles sélectionnés !' );
		}

		App::getInstance()->redirect( 'admin.articles.index' );
	}


	public function save() {

		$form = $this->request->post->Article;

		if ( ! $this->check_csrf( $form->token ) ) {
			App::getInstance()->redirect( 'admin.articles.index' );
		}

		$title        = filter_var( $form->title, FILTER_SANITIZE_STRING );
		$picture      = filter_var( $form->picture, FILTER_SANITIZE_STRING );
		$content      = filter_var( $form->content, FILTER_SANITIZE_STRING );
		$newspaper_id = filter_var( $form->newspaper_id, FILTER_SANITIZE_STRING );
		$type         = filter_var( $form->type, FILTER_SANITIZE_STRING );


		if ( ! isset( $form->id ) ) {
			$this->Article->insertRow( compact( 'title', 'picture', 'content', 'newspaper_id', 'type' ) );
		} else {
			$this->Article->updateRow( compact( 'title', 'picture', 'content', 'newspaper_id', 'type' ), $form->id );
		}

		App::getInstance()->redirect( 'admin.articles.index' );
		//$this->render( 'admin.articles.save' );
	}


	public function getFormData( $data ) {
		$formData = [ ];

		$title        = "";
		$picture      = "";
		$content      = "";
		$newspaper_id = "";
		$type         = "";

		// si $data vide --> create et non update
		if ( ! empty( $data ) ) {
			$id           = $data->id;
			$title        = $data->title;
			$picture      = $data->picture;
			$content      = $data->content;
			$newspaper_id = intval( $data->newspaper_id );
			$type         = $data->type;

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
			'name' => 'title',
			'args' => [
				'type'  => 'text',
				'value' => $title,
				'label' => 'Titre'
			]
		];
		$formData[] = [
			'type' => 'input',
			'name' => 'picture',
			'args' => [
				'type'  => 'text',
				'value' => $picture,
				'label' => 'Image'
			]
		];
		$formData[] = [
			'type' => 'input',
			'name' => 'content',
			'args' => [
				'type'  => 'text',
				'value' => $content,
				'label' => 'Contenu'
			]
		];
		$formData[] = [
			'type' => 'input',
			'name' => 'newspaper_id',
			'args' => [
				'type'  => 'number',
				'value' => $newspaper_id,
				'label' => 'Journal'
			]
		];
		$formData[] = [
			'type' => 'input',
			'name' => 'type',
			'args' => [
				'type'  => 'text',
				'value' => $type,
				'label' => 'Type'
			]
		];


		$formData[] = [
			'type' => 'submit',
			'name' => 'Enregistrer',
			'args' => [
			]
		];


		return $formData;
	}

	public function getForm() {
		return [
			'name' => 'Article',
			'data' => [
				"title"  => "Ajouter un article",
				"method" => "post",
				"action" => "admin.articles.save"
			]
		];
	}

	public function getFormEdit() {
		return [
			'name' => 'Article',
			'data' => [
				"title"  => "Editer un article",
				"method" => "post",
				"action" => "admin.articles.save"
			]
		];
	}

	public function update( $id ) {

		if ( ! App::getAuth()->hasAccess( 'articles.update' ) ) {
			throw new \Exception( 'Access denied !' );
		}

		$article = $this->Article->findById( $id );

		$formData = $this->getFormData( $article );
		$form     = $this->getFormEdit( $id );


		$this->render( 'admin.crud.edit', compact( 'formData', 'form', 'article' ) );
	}

}