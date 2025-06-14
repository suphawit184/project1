// ปรับใหม่ใน login.php
$stmt = $conn->prepare("SELECT user_id, username, role, password FROM user_info WHERE username = ?");
$stmt->bind_param("s", $user);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $row = $result->fetch_assoc();
    if (password_verify($pass, $row['password'])) {
        unset($row['password']); // ไม่ควรส่งรหัสกลับ
        echo json_encode(["status" => "success", "user" => $row]);
    } else {
        echo json_encode(["status" => "fail", "message" => "รหัสผ่านไม่ถูกต้อง"]);
    }
} else {
    echo json_encode(["status" => "fail", "message" => "ไม่พบผู้ใช้"]);
}
