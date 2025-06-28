<?php
include 'Comment.php';

class CommentController {

    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
  
    public function index($limit = 10, $offset = 0) {
        return Comment::all($this->pdo, $limit, $offset);
    }
  
    public function store($name, $comment) {
        Comment::create($this->pdo, $name, $comment);
    }
}
