<?php

namespace App\Controller;

class Api extends \Base\AbstractController
{
    public function getAuthorPosts()
    {
        $userId = (int)$_GET['user_id'] ?? 0;
        if (!$userId) {
            return $this->response(['error' => 'no_user_id']);
        }
        $posts = \App\Model\Post::getAuthorPosts($userId);
        if (!$posts) {
            return $this->response(['error' => 'no_posts']);
        }

        $data = array_map(function (\App\Model\Post $post) {
            return $post->getData();
        }, $posts);

        return $this->response(['posts' => $data]);
    }

    public function response(array $data)
    {
        header('Content-type: application/json charset=utf-8');
        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }
}