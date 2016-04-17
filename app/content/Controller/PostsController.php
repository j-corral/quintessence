<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 07/09/15
 * Time: 11:09
 */

namespace Content\Controller;


use Core\App;

class PostsController extends AppController {

    public function index() {

        if (!App::getAuth()->hasAccess('posts.index')) {
            throw new \Exception( 'Access denied !' );
        }

        $this->render('posts.index');
    }

}