<?php
namespace Base;

abstract class AbstractController
{
    /** @var View */
    protected $view;
    /** @var Session */
    protected $session;
    
    protected function redirect(string $url)
    {
        throw new RedirectException($url);
    }
    
    public function setView(View $view): void 
    {
        $this->view = $view;
    }
    
    public function getUser(): ?\App\Model\User
    {
        $userId = $this->session->getUserId();
        if (!$userId) {
            return null;
        }

        $user = \App\Model\User::getById($userId);
        if (!$user) {
            return null;
        }

        return $user;
    }

    public function getUserId()
    {
        $user = $this->getUser();
        if ($user) {
            return $user->getId();
        }
        
        return false;
    }
    
    public function setSession(Session $session)
    {
        $this->session = $session;
    }

}