<?php 

session_start();
require_once 'config/conn_db.php';

// ตรวจสอบว่าผู้ใช้ได้เข้าสู่ระบบแล้วหรือไม่
if (!isset($_SESSION['user_login'])) {
    $_SESSION['error'] = 'กรุณาเข้าสู่ระบบ!';
    header('location: signin.php');
    exit; // เพิ่ม exit เพื่อหยุดการทำงานของสคริปต์
}

// โค้ดส่วนนี้จะทำงานเมื่อผู้ใช้เข้าสู่ระบบแล้วเท่านั้น
$user_id = $_SESSION['user_login'];

try {
    // ใช้ prepare statement เพื่อป้องกัน SQL Injection
    // แก้ไขชื่อตารางจาก 'user' เป็น 'users'
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = :id"); 
    
    // ผูกค่า id เข้ากับ placeholder :id
    $stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    
    // ดึงข้อมูลผู้ใช้
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    // ตรวจสอบว่าพบข้อมูลผู้ใช้หรือไม่
    if (!$row) {
        // ถ้าไม่พบข้อมูลผู้ใช้ในฐานข้อมูล ให้ลบ session และ redirect ไปหน้า login
        unset($_SESSION['user_login']);
        $_SESSION['error'] = 'ไม่พบข้อมูลผู้ใช้ในระบบ';
        header('location: signin.php');
        exit;
    }

} catch(PDOException $e) {
    // แสดงข้อผิดพลาดของฐานข้อมูล
    echo "Error: " . $e->getMessage();
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

</head>
<body>
    <div class="container">
        
        <h3 class="mt-4">Welcome, <?php echo htmlspecialchars($row['username']); ?></h3>
        
        <a href="logout.php" class="btn btn-danger">Logout</a>
        <a href="feedback.php" class="btn btn-primary">Feedback</a>

    </div>
</body>
</html>