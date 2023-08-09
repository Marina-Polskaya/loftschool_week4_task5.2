<?php
namespace App\Controller;

use App\Model\User as UserModel;

class User extends \Base\AbstractController
{
    public function index()
    {
        if ($this->getUser()) {
            $this->redirect('/blog');
        }
        return $this->view->render(
            'index.phtml',
            [
                'title' => 'Главная',
                'user' => $this->getUser(),
            ]
        );
    }
    
    /*public function index()
    {
        if ($this->getUser()) {
            $this->redirect('/blog');
        }
        return $this->view->render(
            'login.phtml',
            [
                'title' => 'Главная',
                'user' => $this->getUser(),
            ]
        );
    }*/
    
    public function login()
    {
        $email = trim(filter_input(INPUT_POST, 'email')); //выдаёт trim null сразу
        
        if ($email) {
            $user = UserModel::getByEmail($email);
            $password = trim(filter_input(INPUT_POST, 'password'));
                
            if (!$user) {
                $this->view->assign('error', 'Неверные логин и пароль');
            } elseif ($user->getPassword() != UserModel::getPasswordHash($password)) {
                $this->view->assign('error', 'Неверные логин и пароль'); //сообщить, что введен неверный пароль
            } else {
                $_SESSION['id'] = $user->getId();
                $this->redirect('/blog'); //$this->redirect('/blog/index');
            }
        }
        return $this->view->render('User/login.phtml', [
            'user' => UserModel::getById((int)filter_input(INPUT_POST, 'id'))
        ]);
    }
    
    public function register()
    {
        $name = trim(filter_input(INPUT_POST, 'name'));
        $email = trim(filter_input(INPUT_POST, 'email'));
        $password = trim(filter_input(INPUT_POST, 'password'));
        $repeatPassword = trim(filter_input(INPUT_POST, 'repeat_password'));
        $success = true;

        if (isset($_POST['name'])){
            
            if (!$name) {
                $this->view->assign('error', 'Введите имя');
                $success = false;
            }
            if (!$email) {
                $this->view->assign('error', 'Введите адрес электронной почты');
                $success = false;
            }

            if (!$password) {
                $this->view->assign('error', 'Введите пароль');
                $success = false;
            } elseif (mb_strlen($password) < 4) {
                $this->view->assign('error', 'Пароль слишком короткий');
                $success = false;
            }
            
            if ($password != $repeatPassword) {
                $this->view->assign('error', 'Повторите пароль');
                $success = false;
            }
            
            $user = UserModel::getByEmail($email);
            if ($user) {
                $this->view->assign('error', 'Пользователь с таким email уже существует.');
                $success = false;
            }

            if ($success) {
                $user = (new UserModel())
                        ->setName($name)
                        ->setEmail($email)
                        ->setPassword(UserModel::getPasswordHash($password));

                $user->save();
                $this->session->authUser($user->getId());

                $this->redirect('/blog');//$this->redirect('/blog');
            }
        }
        return $this->view->render('User/register.phtml', [
            'user' => UserModel::getById((int)filter_input(INPUT_POST, 'id'))
        ]);
    }
    
    public function logout() 
    {
        session_destroy();
        $this->redirect('/user/login');
    }
    
}