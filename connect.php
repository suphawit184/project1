<?php
header("Access-Control-Allow-Origin: 137.116.132.150:3511, 10.51.248.165:3511");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json; charset=UTF-8");

$uid = "root"; // ชื่อผู้ใช้ xampp
$pwd = "123456789"; // รหัสผ่าน xampp
$serverName = "localhost"; // ชื่อเซิร์ฟเวอร์ SQL Server
$database = "intern"; // ชื่อฐานข้อมูล

try {
    // เชื่อมต่อกับฐานข้อมูลโดยปิดการเข้ารหัส SSL
    $con = new PDO("sqlsrv:Server=$serverName;Database=$database;Encrypt=false", $uid, $pwd);

    // ตั้งค่าการจัดการข้อผิดพลาด
    $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // ส่งผลลัพธ์การเชื่อมต่อสำเร็จในรูปแบบ JSON
    // echo json_encode(array("status" => "success", "message" => "Connected successfully"));
} catch (PDOException $e) {
    // ส่งผลลัพธ์การเชื่อมต่อล้มเหลวในรูปแบบ JSON
    echo json_encode(array("status" => "error", "message" => "Connection failed: " . $e->getMessage()));
}
?>