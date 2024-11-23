<?php

class Database
{
    private static $servername = "localhost";
    private static $username = "root";
    private static $password = "";
    private static $dbname = "online_store";
    private static $conn;


    private function __construct()
    {
    }


    public static function connect()
    {
        if (!isset(self::$conn)) {
            try {
                self::$conn = new PDO("mysql:host=" . self::$servername . ";dbname=" . self::$dbname, self::$username, self::$password);

                self::$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                echo "Connection failed: " . $e->getMessage();
            }
        }
        return self::$conn;
    }


    public static function query($sql, $params = [])
    {
        try {
            $stmt = self::connect()->prepare($sql);
            $stmt->execute($params);


            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Query failed: " . $e->getMessage();
            return false;
        }
    }
}
