<?php
require_once __DIR__ . '/../includes/db.php';

class User {
    public static function register($email, $password, $name) {
        $db = Database::connect();
        $stmt = $db->prepare("SELECT 1 FROM users WHERE email = :email");
        $stmt->execute([':email' => $email]);
        if ($stmt->fetch()) return "Email already exists";

        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $db->prepare("INSERT INTO users (email, password_hash, name) VALUES (:email, :hash, :name)");
        $stmt->execute([':email' => $email, ':hash' => $hash, ':name' => $name]);
        return true;
    }

    public static function login($email, $password) {
        $db = Database::connect();
        $stmt = $db->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['role_id'] = $user['role_id'];
            return true;
        }
        return false;
    }
}
