<?php
session_start();
require_once 'config/conn_db.php';

if (!isset($_SESSION['user_login'])) {
    header('Location: signin.php');
    exit;
}

$user_id = $_SESSION['user_login'];
$rating = isset($_POST['rating']) ? intval($_POST['rating']) : 0;
$comment = isset($_POST['comment']) ? trim($_POST['comment']) : '';

if ($rating < 1 || $rating > 5) {
    $_SESSION['error_submit'] = 'กรุณาให้คะแนนระหว่าง 1 ถึง 5 ดาว';
    header('Location: feedback.php');
    exit;
}

if (empty($comment)) {
    $_SESSION['error_submit'] = 'กรุณากรอกความคิดเห็นเพิ่มเติม';
    header('Location: feedback.php');
    exit;
}

// บันทึกฟีดแบคลงฐานข้อมูล
$stmt = $conn->prepare("INSERT INTO feedback (user_id, rating, comment) VALUES (?, ?, ?)");
$stmt->execute([$user_id, $rating, $comment]);

$_SESSION['success_submit'] = 'ส่งฟีดแบคเรียบร้อยแล้ว!';
header('Location: feedback.php');
exit;
