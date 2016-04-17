<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 09/09/15
 * Time: 21:51
 */

namespace Content\Controller\Admin;


use Core\App;
use Core\Auth\DBAuth;

class AppController extends \Content\Controller\AppController{

    public function __construct(){


        parent::__construct();

        $app = App::getInstance();

        $this->layout = $this->loadLayout('admin');

        $auth = new DBAuth($app->getDBInstance());

        if(!$auth->logged()) {
            header('HTTP/1.0 401 Forbidden');
            //$app->findController('users.login');
            $app->redirect('login');
            exit;
        } elseif(!$auth->isAdmin()) {
            header('HTTP/1.0 403 Forbidden');
            $app->redirect('logout');
            exit;
        }


    }

    public function create() {

        if ( ! App::getAuth()->hasAccess( '*.create' ) ) {
            throw new \Exception( 'Access denied !' );
        }

        $formData = $this->getFormData(null);
        $form = $this->getForm();

        $this->render('admin.crud.edit', compact('formData', 'form'));
    }



}