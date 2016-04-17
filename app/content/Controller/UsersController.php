<?php
    /**
     * Created by PhpStorm.
     * User: jonathan
     * Date: 07/09/15
     * Time: 11:09
     */

    namespace Content\Controller;


    use Content\Table\UserTable;
    use Core\App;
    use Core\Auth\DBAuth;
    use Core\Database\FluentDatabase;
    use Core\Helper\Debug;
    use Core\Helper\Html;

    class UsersController extends AppController {


        public function index () {

            if(!App::getAuth()->hasAccess('users.index')) {
                throw new \Exception('Access denied !');
            }

            $users = $this->User->All(true);

            $this->render ('users.index', compact ('users'));
        }


        public function login () {

            $auth = new DBAuth(App::getDBInstance ());

            if ($auth->logged ()) {
                $this->setBackoffice();
            }

            
            if (isset($_POST['data']) && !empty($_POST['data'])) {

                if (!$this->check_csrf ($_POST['data']['User']['token'])) {
                    return false;
                }

                if ($auth->login ($_POST['data']['User']['email'], $_POST['data']['User']['password'])) {
                    $this->showMessage('', "ok", Html::link('admin'));
                } else {
                    $this->showMessage('Email ou mot de passe invalide !');
                }
            }
            
            $this->render ('users.login');
        }


        public function logout () {
            $app = App::getInstance ();
            $auth = new DBAuth($app->getDBInstance ());
            $auth->logout ();
            return $app->redirect ('users.login');
        }


        /**
         * Détermine sur quel backoffice aller selon les droits de l'utilisateur
         */
        public function setBackOffice() {

            // Si une session d'authentification est définie
            if(isset($_SESSION['Auth']) && !empty($_SESSION['Auth'])) {

                $app = App::getInstance ();

                // Redirection sur backoffice admin
                if($_SESSION['Auth']['level'] >= 1) {
                    return $app->redirect('admin');
                } else {
                    // Sinon redirection sur backoffice utilisateur
                    return $app->redirect('home');
                }

            }



        }

    }