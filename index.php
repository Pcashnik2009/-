<?php 
ob_start();

$uploadDir = 'uploads/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

$search = $_POST['search'] ?? '';
$comments = [];

// Обработка добавления комментария
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['comment'])) {
    $comment = trim($_POST['comment']);
    $files = $_FILES['media'] ?? null;
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'video/mp4', 'video/webm', 'video/ogg'];
    $fileEntries = [];

    if ($files && isset($files['name']) && is_array($files['name'])) {
        for ($i = 0; $i < count($files['name']); $i++) {
            if (!empty($files['name'][$i]) && $files['error'][$i] === 0 && in_array($files['type'][$i], $allowedTypes)) {
                $ext = pathinfo($files['name'][$i], PATHINFO_EXTENSION);
                $fileName = uniqid('upload_', true) . '.' . $ext;
                $filePath = $uploadDir . $fileName;
                if (move_uploaded_file($files['tmp_name'][$i], $filePath)) {
                    $fileEntries[] = $filePath . '::' . $files['type'][$i];
                }
            }
        }
    }

    if ($comment !== '') {
        $entry = $comment;
        if (!empty($fileEntries)) {
            $entry .= '||' . implode('||', $fileEntries);
        }
        file_put_contents('comments.txt', $entry . PHP_EOL, FILE_APPEND);
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }
}

// Загрузка комментариев
if (file_exists('comments.txt')) {
    $rawComments = file('comments.txt', FILE_IGNORE_NEW_LINES);
    foreach ($rawComments as $line) {
        if ($search && stripos($line, $search) === false) continue;
        $parts = explode('||', $line);
        $text = array_shift($parts);
        $files = [];
        foreach ($parts as $filePart) {
            if (strpos($filePart, '::') !== false) {
                list($path, $type) = explode('::', $filePart);
                if (file_exists($path)) {
                    $files[] = ['path' => $path, 'type' => $type];
                }
            }
        }
        $comments[] = ['text' => $text, 'files' => $files];
    }
}
ob_end_flush();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<title>Арендный дозор.uz</title>
<link rel="stylesheet" href="styles.css">
<link rel="icon" href="ChatGPT.png" type="image/png">
<link rel="apple-touch-icon" sizes="180x180" href="apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="favicon-16x16.png">
<style>
body {
    font-family: Arial, sans-serif;
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
    line-height: 1.6;
}
.search-container {
    border: 2px solid #4CAF50;
    border-radius: 10px;
    padding: 15px;
    margin: 20px 0;
    background-color: #f9f9f9;
    text-align: center;
}
.comment-form {
    margin: 30px 0;
    padding: 20px;
    background: #f0f0f0;
    border-radius: 8px;
}
.comment-item {
    color: #333;
    margin: 20px 0;
    padding: 20px;
    border-left: 4px solid #4CAF50;
    background: #f8f8f8;
    font-size: 18px;
    line-height: 1.7;
}
.comment-text {
    margin-bottom: 15px;
    font-weight: bold;
    font-size: 20px;
    color: #006400;
}
.media-container {
    margin-top: 15px;
    clear: both;
}
input[type="text"], input[type="file"] {
    width: 100%;
    padding: 12px;
    margin: 10px 0;
    box-sizing: border-box;
    font-size: 16px;
}
button, input[type="submit"] {
    background-color: #4CAF50;
    color: white;
    padding: 12px 20px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 16px;
    margin-top: 10px;
}
h1, h2, h3 {
    color: #006400;
}
img {
    max-width: 100%;
    height: auto;
    margin-top: 15px;
    border-radius: 5px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}
video {
    max-width: 100%;
    margin-top: 15px;
    border-radius: 5px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}
</style>
</head>
<body>
<h1>Арендный дозор</h1>
<h2>Чёрный список арендодателей</h2>

<!-- Поиск (только один, в рамке) -->
<div class="search-container">
    <h3>Поиск комментариев</h3>
    <form method="post">
        <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Введите ФИО или другие данные">
        <button type="submit">Найти</button>
    </form>
</div>

<!-- Инструкция -->
<div class="comment-form">
    <h3>Добавить в чёрный список</h3>
    <p style="font-size: 18px;"><strong>Внимание!</strong> Указывайте данные правильно:</p>
    <ul style="font-size: 17px;">
        <li>ФИО (полностью)</li>
        <li>Возраст</li>
        <li>Причина внесения в список</li>
        <li>Контактные данные (если известны)</li>
    </ul>
    
    <form action="" method="POST" enctype="multipart/form-data">
        <input name="comment" type="text" placeholder="Пример: Иванов Иван Иванович, 45 лет, не вернул депозит в размере 5 млн. сумов, телефон: +99890..." required>
        <input type="file" name="media[]" accept="image/*,video/*" multiple>
        <input type="submit" value="Добавить">
    </form>
</div>

<!-- Список комментариев -->
<div class="comments-list">
    <?php foreach ($comments as $com): ?>
        <div class="comment-item">
            <div class="comment-text"><?= nl2br(htmlentities($com['text'])) ?></div>
            <div class="media-container">
                <?php foreach ($com['files'] as $file): ?>
                    <?php if (strpos($file['type'], 'image') !== false): ?>
                        <img src="<?= $file['path'] ?>">
                    <?php elseif (strpos($file['type'], 'video') !== false): ?>
                        <video controls>
                            <source src="<?= $file['path'] ?>" type="<?= $file['type'] ?>">
                        </video>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>
</body>
</html>