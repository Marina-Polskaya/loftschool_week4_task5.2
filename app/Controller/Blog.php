<?php
namespace App\Controller;

use App\Model\Post as PostModel;

class Blog extends \Base\AbstractController
{
    public function index()
    {
        if (!$this->getUser()) {
            $this->redirect('/user/login');
        }
        $posts = PostModel::getLimitPosts();
        
        return $this->view->render('Blog/index.phtml', [
            'posts' => $posts,
            'user' => $this->getUser()
        ]);
    }
}