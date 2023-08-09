<?php
namespace App\Model;

class Post
{
    private $author;
    private $id;
    private $text;
    private $authorId;
    private $createdAt;
    private $image;
    
    public function __constructor($data = [])
    {
        if ($data) {
            $this->id = $data['id'];  
            $this->text = $data['text'];
            $this->authorId = $data['author_id'];
            $this->createdAt = $data['created_at'];
            $this->image = $data['image'];
        }
    }
 
    public function save() : int
    {
        $db = \Base\Db::getInstance();
        $query = 'INSERT INTO `posts` (`author_id`, `text`, `created_at`, `image`) VALUES (:author_id, :text, :created_at, :image)';
        $db->exec($query, [
            ':author_id' => $this->authorId,
            ':text' => $this->text,
            ':created_at' => date('Y-m-d H:i:s'),
            ':image' => $this->image
        ], __METHOD__);
        
        $id = $db->lastInsertId();
        $this->id = $id;
        
        return $id;
    }
    
    public static function delete(int $postId) : int
    {
        $db = \Base\Db::getInstance();
        $query = 'DELETE FROM posts WHERE id = :postId';
        return $db->exec($query, [':postId' => $postId], __METHOD__);
    }
    
    public static function getLimitPosts(int $limit = 20) : array
    {
        $db = \Base\Db::getInstance();
        $query = 'SELECT * FROM posts ORDER BY id DESC LIMIT ' . $limit ;
        $data = $db->fetchAll($query, __METHOD__);
        if(!$data) {
            return [];
        }
        $posts = [];
        foreach ($data as $elem) {
            $post = new self($elem['id'], $elem['author_id'], $elem['text'], $elem['created_at'], $elem['image']);
            $post->id = $elem['id'];
            $post->authorId = $elem['author_id'];
            $post->text = $elem['text'];
            $post->createdAt = $elem['created_at'];
            $post->image = $elem['image'];
            $posts[] = $post;
        }
        
        return $posts;        
    }
    
    public static function getAuthorPosts(int $author_id) : array
    {
        $limit = 20;
        $db = \Base\Db::getInstance();
        $query = 'SELECT * FROM posts WHERE author_id = :author_id ORDER BY id DESC LIMIT ' . $limit ;
        $data = $db->fetchAll($query, __METHOD__, [':author_id' => $author_id]);
        if(!$data) {
            return [];
        }
        $posts = [];
        foreach ($data as $elem) {
            $post = new self($elem['id'], $elem['author_id'], $elem['text'], $elem['created_at'], $elem['image']);
            $post->id = $elem['id'];
            $post->authorId = $elem['author_id'];
            $post->text = $elem['text'];
            $post->createdAt = $elem['created_at'];
            $post->image = $elem['image'];
            $posts[] = $post;
        }
        
        return $posts;        
    }
    
    public function getPostByPostId(int $postId) : ?self
    {
        $db = \Base\Db::getInstance();
        $query = 'SELECT * FROM posts WHERE `id` = :id';
        $post = $db->fetchOne($query, [':id' => $postId], __METHOD__);
        if (!$post) {
            return null;
        }
        return new self($post);
    }
    
    public static function getAuthorNameById(int $authorId) : ?string
    {
        $db = \Base\Db::getInstance();
        $select = "SELECT `name` FROM users WHERE id = :author_id";
        $name = $db->fetchOne($select, [':author_id' => $authorId], __METHOD__);
        
        if (!$name) {
            return null;
        }
        
        return $name['name'];
    }
    
    public function loadFile(string $file) : void
    {
        if (file_exists($file)) {
            $this->image = $this->createFileName();
            move_uploaded_file($file, getcwd() . '/Images/' . $this->image);
        }
    }
    
     private function createFileName() : string
    {
        return sha1(microtime(1) . mt_rand(1, 10000000)) . '.jpg';
    }
    
    public function getAuthor()
    {
        return $this->author;
    }
    
    public function getText() : string
    {
        return $this->text;
    }
    
    public function getAuthorId() : int
    {
        return $this->authorId;
    }

    public function getId() : ?int
    {
        return $this->id;
    }

    public function getCreatedAt() : string
    {
        return $this->createdAt;
    }
    
    public function getImage() : ?string
    {
        return $this->image;
    }
    
    public function setAuthor($user): self 
    {
        $this->author = $user;
        return $this;
    }

        public function setText($text): self
    {
        $this->text = $text;
        return $this;
    }

    public function setAuthorId($userId): self
    {
        $this->authorId = $userId;
        return $this;
    }  
    
    public function setId($id): self
    {
        $this->id = $id;
        return $this;
    }

    public function setCreatedAt($createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }
    
    public function setImage($image): self 
    {
        $this->image = $image;
        return $this;
    }
    
    public function getData() : array
    {
        return [
            'id' => $this->id,
            'author_id' => $this->authorId,
            'text' => $this->text,
            'created_at' => $this->createdAt,
            'image' => $this->image
        ];
    }
}