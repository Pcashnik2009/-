<?php
include 'db.php';
include 'CommentController.php';

$controller = new CommentController($pdo);
$comments = $controller->index(10, 0);

header('Content-Type: application/json');
echo json_encode($comments);