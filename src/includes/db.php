<?php
class Database {
    public static function connect() {
        static $conn;
        if ($conn === null) {
            $dsn = "pgsql:host=db;port=5432;dbname=db;";
            $conn = new PDO($dsn, "docker", "docker", [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);
        }
        return $conn;
    }
}
