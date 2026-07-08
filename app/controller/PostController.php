<?php
namespace Mawufemor\Techandfun\Controller;
use Mawufemor\Techandfun\model\Post;

if (!defined('ROOT')) {
    die("Direct access not allowed");
}

use PDO;

class PostController
{
    private Post $postModel;

    public function __construct(private PDO $pdo)
    {
        $this->postModel = new Post($this->pdo);
    }

    public function getPostModel(): Post
    {
        return $this->postModel;
    }
}
?>