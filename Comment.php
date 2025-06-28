<?php
include 'db.php';

class Comment {

    public static function all($pdo, $limit = 10, $offset = 0) {
        $stmt = $pdo->prepare('SELECT * FROM comments ORDER BY created_at DESC LIMIT ? OFFSET ?');
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->bindValue(2, $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }
    
    public static function create($pdo, $name, $comment) {
        $stmt = $pdo->prepare('INSERT INTO comments (name, comment) VALUES (?, ?)');
        $stmt->execute([$name, $comment]);
    }
}