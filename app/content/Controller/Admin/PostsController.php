<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 09/09/15
 * Time: 21:51
 */

namespace Content\Controller\Admin;


use Core\App;
use Core\Database\QueryBuilder;

class PostsController extends AppController {

    public function index() {
        $this->render('admin.posts.index');
    }

}
