<?php

include 'connect.php';

$inputJSON = file_get_contents('php://input');
$input = json_decode($inputJSON, true);

// ตรวจสอบว่าข้อมูล JSON ถูกต้องหรือไม่
if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode(['status' => 'error', 'message' => 'รูปแบบข้อมูลไม่ถูกต้อง']);
    exit();
}

// ดึงข้อมูลจาก JSON
$username = isset($input['username']) ? trim($input['username']) : null;
$user_id = isset($input['user_id']) ? trim($input['user_id']) : null;
$password = isset($input['password']) ? $input['password'] : null;

// ตรวจสอบว่าข้อมูลครบถ้วนหรือไม่
if (empty($username) || empty($user_id) || empty($password)) {
    echo json_encode(['status' => 'error', 'message' => 'กรุณากรอกข้อมูลให้ครบถ้วน']);
    exit();
}

// กำหนด role ตาม user_id
$role = (strpos($user_id, '0001-') === 0) ? 'intern' : 'mentor';

// เข้ารหัสรหัสผ่านด้วย BCRYPT
$hashed_password = password_hash($password, PASSWORD_BCRYPT);

// ตรวจสอบว่ามีผู้ใช้งานซ้ำหรือไม่
try {
    $checkStmt = $con->prepare("SELECT COUNT(*) FROM user_info WHERE username = :username OR user_id = :user_id");
    $checkStmt->execute([
        ':username' => $username,
        ':user_id' => $user_id
    ]);
    $userExists = $checkStmt->fetchColumn();

    if ($userExists > 0) {
        echo json_encode(['status' => 'error', 'message' => 'ชื่อผู้ใช้หรือรหัสพนักงานนี้มีอยู่แล้ว']);
        exit();
    }

    // เตรียมคำสั่ง SQL สำหรับการเพิ่มข้อมูลผู้ใช้ใหม่
    $stmt = $con->prepare("INSERT INTO user_info (username, user_id, password, role) VALUES (:username, :user_id, :password, :role)");
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':password', $hashed_password);
    $stmt->bindParam(':role', $role);

    if ($stmt->execute()) {
        // ถ้าเป็น mentor เพิ่มข้อมูลในตาราง mentor_info
        if ($role === 'mentor') {
            $mentorStmt = $con->prepare("INSERT INTO mentor_info (user_id) VALUES (:user_id)");
            $mentorStmt->bindParam(':user_id', $user_id);
            
            if ($mentorStmt->execute()) {
                echo json_encode([
                    'status' => 'success',
                    'message' => 'register success and mentor info added',
                    'role' => $role,
                    'connection_status' => 'connected'
                ]);
            } else {
                echo json_encode([
                    'status' => 'success',
                    'message' => 'register success but failed to add mentor info',
                    'role' => $role,
                    'connection_status' => 'connected'
                ]);
            }
        } else {
            echo json_encode([
                'status' => 'success',
                'message' => 'register success',
                'role' => $role,
                'connection_status' => 'connected'
            ]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'เกิดข้อผิดพลาดในการลงทะเบียนผู้ใช้']);
    }

} catch (PDOException $e) {
    // จัดการข้อผิดพลาด
    echo json_encode(['status' => 'error', 'message' => 'ข้อผิดพลาด: ' . $e->getMessage()]);
}

// ปิดการเชื่อมต่อฐานข้อมูล
$con = null;
?>