<?php
session_start();
require_once 'config/conn_db.php';

if (!isset($_SESSION['user_login'])) {
    $_SESSION['error'] = 'กรุณาเข้าสู่ระบบ!';
    header('Location: signin.php');
    exit;
}

$user_id = $_SESSION['user_login'];

// ดึงข้อมูลฟีดแบคทั้งหมด พร้อมชื่อผู้ใช้
$stmt = $conn->query("SELECT feedback.*, users.username 
                      FROM feedback 
                      JOIN users ON feedback.user_id = users.id 
                      ORDER BY feedback.created_at DESC");
$feedbacks = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>ฟีดแบค</title>
    <link rel="stylesheet" href="feedback.css"> <!-- CSS ส่วนตัว -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="feedback-form">
    <h2>ส่งฟีดแบคของคุณ</h2>

    <?php if (isset($_SESSION['error_submit'])): ?>
        <p class="error"><?= htmlspecialchars($_SESSION['error_submit']) ?></p>
        <?php unset($_SESSION['error_submit']); ?>
    <?php elseif (isset($_SESSION['success_submit'])): ?>
        <p class="success"><?= htmlspecialchars($_SESSION['success_submit']) ?></p>
        <?php unset($_SESSION['success_submit']); ?>
    <?php endif; ?>

    <form method="POST" action="submit_feedback.php">
        <label>ให้คะแนน (1–5 ดาว):</label>
        <div class="star-rating">
            <?php for ($i = 5; $i >= 1; $i--): ?>
                <input type="radio" id="star<?= $i ?>" name="rating" value="<?= $i ?>" required>
                <label for="star<?= $i ?>">★</label>
            <?php endfor; ?>
        </div>

        <label for="comment">ความเห็นเพิ่มเติม:</label>
        <textarea name="comment" id="comment" required></textarea>

        <button type="submit">ส่งฟีดแบค</button>
        <a href="user.php" class="btn btn-primary">กลับไปหน้าผู้ใช้งาน</a>
    </form>
</div>

<div class="feedback-list">
    <h2>ฟีดแบคจากผู้ใช้งาน</h2>
    <?php foreach ($feedbacks as $fb): ?>
        <div class="feedback">
            <strong><?= htmlspecialchars($fb['username']) ?></strong>:
            <span><?= str_repeat('⭐', $fb['rating']) ?></span>
            <p><?= nl2br(htmlspecialchars($fb['comment'])) ?></p>
            <small>เมื่อ <?= $fb['created_at'] ?></small>
        </div>
    <?php endforeach; ?>
</div>
</body>
</html>
