<?php

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$host = "sql309.infinityfree.com";
$dbname = "if0_42677220_kont_db";
$username = "if0_42677220";
$password = ""; // пароль от моей базы данных

try {

    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
        ]
    );

    $json = file_get_contents("php://input");
    $data = json_decode($json, true);

    if (!$data) {
        echo json_encode([
            "success" => false,
            "message" => "Некорректные данные"
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $name = trim($data["name"] ?? "");
    $phone = trim($data["phone"] ?? "");
    $message = trim($data["message"] ?? "");

    if ($name === "" || $phone === "" || $message === "") {
        echo json_encode([
            "success" => false,
            "message" => "Заполните все поля"
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $stmt = $pdo->prepare("
        INSERT INTO consultations (name, phone, message)
        VALUES (:name, :phone, :message)
    ");

    $stmt->execute([
        ":name" => $name,
        ":phone" => $phone,
        ":message" => $message
    ]);

    echo json_encode([
        "success" => true,
        "message" => "Заявка успешно отправлена"
    ], JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {

    echo json_encode([
        "success" => false,
        "message" => "Ошибка базы данных: " . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);

}

?>