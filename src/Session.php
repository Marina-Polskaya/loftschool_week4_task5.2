<?php
namespace Base;

class Session
{
    public  function init()
    {
        session_start();
    }
    
    public function authUser(int $id)
    {
        $_SESSION['id'] = $id;
    }
    
    public function getUserId()
    {
        return $_SESSION['id'] ?? false;
    }
}