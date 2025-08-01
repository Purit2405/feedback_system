<?php 

session_start();
// แก้ไขบรรทัดนี้: เปลี่ยนจาก 'config/db.php' เป็น 'config/conn_db.php'
require_once 'config/conn_db.php'; 

if (isset($_POST['signin'])) {
    // รับค่าจากฟอร์ม
    $email = $_POST['email']; 
    $password = $_POST['password'];

    // ตรวจสอบความถูกต้องของข้อมูลเบื้องต้น
    if (empty($email)) {
        $_SESSION['error'] = 'กรุณากรอก Email';
        header("location: signin.php");
        exit;
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = 'รูปแบบ Email ไม่ถูกต้อง';
        header("location: signin.php");
        exit;
    } else if (empty($password)) {
        $_SESSION['error'] = 'กรุณากรอก รหัสผ่าน';
        header("location: signin.php");
        exit;
    } else if (strlen($password) > 20 || strlen($password) < 5) {
        $_SESSION['error'] = 'รหัสต้องมีความยาวระหว่าง 5ถึง 20 ตัวอักษร';
        header("location: signin.php");
        exit;
    } else {
        try {
            $check_data = $conn->prepare("SELECT * FROM users WHERE email = :email");
            $check_data->bindParam(":email", $email);
            $check_data->execute();
            $row = $check_data->fetch(PDO::FETCH_ASSOC);

            if ($check_data->rowCount() > 0) {
                if (password_verify($password, $row['password'])) {
                    if ($row['urole'] == 'admin') {
                        $_SESSION['admin_login'] = $row['id'];
                        header("location: admin.php");
                        exit;
                    } else {
                        $_SESSION['user_login'] = $row['id'];
                        header("location: user.php");
                        exit;
                    }
                } else {
                    $_SESSION['error'] = 'รหัสผ่านไม่ถูกต้อง';
                    header("location: signin.php");
                    exit;
                }
            } else {
                $_SESSION['error'] = "ไม่พบข้อมูลอีเมลนี้ในระบบ";
                header("location: signin.php");
                exit;
            }
        } catch(PDOException $e) {
            echo $e->getMessage();
        }
    }
}
?>