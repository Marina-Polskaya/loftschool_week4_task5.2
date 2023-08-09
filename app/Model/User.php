<?php
namespace App\Model;

class User extends \Base\AbstractModel
{
    protected $id;
    private $name;
    private $email;
    private $password;
    private $repeatPassword;
    private $createdAt;
    
    public function __construct($data = []) 
    {
        if ($data) {
            $this->id = $data['id'];
            $this->name = $data['name'];
            $this->email = $data['email'];
            $this->password = $data['password'];
            $this->createdAt = $data['created_at'];
        }
    }
    
    public function save() : ?int
    {
        $db = \Base\Db::getInstance();
        $query = 'INSERT INTO users (`name`, `email`, `password`, `created_at`) VALUES (:name, :email, :password, :created_at)';
        $db->exec($query, [
            ':name' => $this->name,
            ':email' => $this->email,
            ':password' => $this->password,
            ':created_at' => date('Y-m-d H:i:s')
        ], __METHOD__);
        
        $id = $db->lastInsertId();
        $this->id = $id;
        
        return $id;
    }
    
    public static function getById(int $id) : ?self
    {
        $db = \Base\Db::getInstance();
        $select = "SELECT * FROM users WHERE id = :id";
        $data = $db->fetchOne($select, [':id' => $id], __METHOD__);
        
        if (!$data) {
            return null;
        }
        
        return new self($data);
    }
    
    public static function getByEmail(string $email) : ?self
    {
        $db = \Base\Db::getInstance();
        $select = "SELECT * FROM users WHERE `email` = :email";
        $data = $db->fetchOne($select, [':email' => $email], __METHOD__);
        
        if (!$data) {
            return null;
        }
        
        return new self($data);
    }
    
    public static function getPasswordHash(string $password)
    {
        return sha1('.,mnbv' . $password);
    }
    
    public function isAdmin() : bool
    {
        return in_array($this->id, ADMIN_IDS);
    }


    public function getId() : string
    {
        return $this->id;
    }
    
    public function setId(string $id): self 
    {
        $this->id = $id;
        return $this;
    }

    public function getName() : string
    {
        return $this->name;
    }
    
    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getEmail() : string
    {
        return $this->email;
    }
    
    public function setEmail(string $email): self 
    {
        $this->email = $email;
        return $this;
    }

    public function getPassword()
    {
        return $this->password;
    }
    
    public function setPassword($password): self
    {
        $this->password = $password;
        return $this;
    }
    
    public function getRepeatPassword()
    {
        return $this->password;
    }
    
    public function setRepeatPassword($repeatPassword): self
    {
        $this->repeatPassword = $repeatPassword;
        return $this;
    }

    public function getCreatedAt() 
    {
        return $this->createdAt;
    }
    
    public function setCreatedAt($createdAt): self 
    {
        $this->createdAt = $createdAt;
        return $this;
    }
    
}

