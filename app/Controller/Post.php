<?php
namespace App\Controller;

use App\Model\Post as PostModel;

class Post extends \Base\AbstractController
{
    public function new()
    {
        if (!$this->getUser()) {
            $this->redirect('/user/login');
        }
        
        return $this->view->render('Post/new.phtml', [
            'user' => $this->getUser()
        ]);
    }
    public function createNewPost()
    {
        $authorId = $this->session->getUserId();
        if (!$authorId) {
            $this->redirect('/user/login');
        }
        $text = (filter_input(INPUT_POST, 'text'));

        if ($text) {
            $post = (new PostModel())->setAuthorId($authorId)->setText($text);
            
            if (isset($_FILES['image']['tmp_name'])) {
                    $post->loadFile($_FILES['image']['tmp_name']);
            }  
            $post->save();
            
        } else { 
            $this->view->assign('error', 'Сообщение не может быть пустым');
            return $this->view->render('Post/new.phtml', [
                'user' => \App\Model\User::getById($authorId)
            ]);
        }
        
        $this->redirect('/blog');
    }
    
    public function delete() : void
    {
        if ($this->getUser() || $this->getUser()->isAdmin()) {
            $postId = (int)$_GET['id'];
            \App\Model\Post::delete($postId);
            $this->redirect('/blog');
        }
    }
    
    public function getPostsByUserId(int $id) : ?self
    {
        $db = \Base\Db::getInstance();
        $select = 'SELECT * FROM posts WHERE user_id = :id';
        $data = $db->fetchAll($select, [':id' => $id], __METHOD__);
        
        if (!$data) {
            return null;
        }
        
        return new self($data);
    }    
}