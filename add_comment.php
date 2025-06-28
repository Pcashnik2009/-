<?php
include 'db.php';
include 'CommentController.php';

$controller = new CommentController($pdo);
$input = json_decode(file_get_contents('php://input'), true);
$controller->store($input['name'], $input['comment']);  

header('Content-Type: application/json');
echo json_encode(['status' => 'success']);
