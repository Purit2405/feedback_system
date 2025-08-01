<?php 
session_start();
require_once 'config/conn_db.php';

if(isset($_POST['signup'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $c_password = $_POST['c_password'];
    $urole = 'user'; // กำหนด role เริ่มต้นเป็น user

    if (empty($username)) {
        $_SESSION['error'] = 'กรุณากรอก Username';
        header("location: index.php");
        exit;
    } else if (empty($email)) {
        $_SESSION['error'] = 'กรุณากรอก Email';
        header("location: index.php");
        exit;
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = 'รูปแบบอีเมลไม่ถูกต้อง';
        header("location: index.php");
        exit;
    } else if (empty($password)) {
        $_SESSION['error'] = 'กรุณากรอก Password';
        header("location: index.php");
        exit;
    } else if (strlen($_POST['password']) > 20 || strlen($_POST['password']) < 5) {
        $_SESSION['error'] = 'รหัสผ่านต้องมีความยาว 5-20 ตัวอักษร';
        header("location: index.php");
        exit;
    } else if (empty($c_password)) {
        $_SESSION['error'] = 'กรุณายืนยันรหัสผ่าน';
        header("location: index.php");
        exit;
    } else if ($password != $c_password) {
        $_SESSION['error'] = 'รหัสผ่านไม่ตรงกัน';
        header("location: index.php");
        exit;
    } else {
        try {
            // ตรวจสอบ Username ซ้ำ
            $check_username = $conn->prepare("SELECT username FROM users WHERE username = :username");
            $check_username->bindParam(":username", $username);
            $check_username->execute();
            $row_username = $check_username->fetch(PDO::FETCH_ASSOC);

            // ตรวจสอบ Email ซ้ำ
            $check_email = $conn->prepare("SELECT email FROM users WHERE email = :email");
            $check_email->bindParam(":email", $email);
            $check_email->execute();
            $row_email = $check_email->fetch(PDO::FETCH_ASSOC);

            // แก้ไขเงื่อนไขการตรวจสอบ
            if ($row_username) { // ถ้าพบ username ซ้ำ
                $_SESSION['warning'] = "มีUsernameนี้อยู่ในระบบแล้ว<a href='signin.php'>คลิ๊กที่นี่</a> เพื่อเข้าสู่ระบบ";
                header("location: index.php");
                exit;
            } else if ($row_email) { // ถ้าพบ email ซ้ำ
                $_SESSION['warning'] = "มีอีเมลนี้อยู่ในระบบแล้ว <a href='signin.php'>คลิ๊กที่นี่</a> เพื่อเข้าสู่ระบบ";
                header("location: index.php");
                exit;
            } else {
                // ถ้าไม่พบ Username และ Email ซ้ำ
                $passwordHash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("INSERT INTO users(username, email, password, urole) VALUES(:username, :email, :password, :urole)");
                $stmt->bindParam(":username", $username);
                $stmt->bindParam(":email", $email);
                $stmt->bindParam(":password", $passwordHash);
                $stmt->bindParam(":urole", $urole);
                $stmt->execute();
                
                $_SESSION['success'] = "สมัครสมาชิกเรียบร้อยแล้ว! <a href='signin.php' class='alert-link'>คลิ๊กที่นี่</a> เพื่อเข้าสู่ระบบ";
                header("location: index.php");
                exit;
            }

        } catch(PDOException $e) {
            echo $e->getMessage();
        }
    }
}
?>