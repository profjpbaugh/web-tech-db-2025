<?php
class Database {
  public static function connect() {
    require_once __DIR__ . '/../protected/config.inc.php';
    $pdo = new PDO(DBCONNSTRING, DBUSER, DBPASS, [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    return $pdo;
  }
}
