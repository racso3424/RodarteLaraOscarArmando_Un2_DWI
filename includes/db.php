<?php
// ==============================================================================
// TechFix - Conexión a Base de Datos (PDO MySQL)
// ==============================================================================

// Cargar variables de entorno si existen (Soporte local y Cloud Hosting)
$dbHost = getenv('DB_HOST') ?: (getenv('MYSQLHOST') ?: '127.0.0.1');
$dbPort = getenv('DB_PORT') ?: (getenv('MYSQLPORT') ?: '3306');
$dbName = getenv('DB_NAME') ?: (getenv('MYSQLDATABASE') ?: 'techfix_db');
$dbUser = getenv('DB_USER') ?: (getenv('MYSQLUSER') ?: 'root');
$dbPass = getenv('DB_PASS') !== false ? getenv('DB_PASS') : (getenv('MYSQLPASSWORD') !== false ? getenv('MYSQLPASSWORD') : '');

$pdo = null;

try {
    // Intentar conexión directa a la base de datos especificada
    $dsn = "mysql:host={$dbHost};port={$dbPort};dbname={$dbName};charset=utf8mb4";
    $pdo = new PDO($dsn, $dbUser, $dbPass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
} catch (PDOException $e) {
    // Si la base de datos no existe en el entorno local, intentamos crearla e inicializarla automáticamente
    if ($e->getCode() == 1049 || strpos($e->getMessage(), 'Unknown database') !== false) {
        try {
            $dsnNoDb = "mysql:host={$dbHost};port={$dbPort};charset=utf8mb4";
            $pdoInit = new PDO($dsnNoDb, $dbUser, $dbPass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
            $pdoInit->exec("CREATE DATABASE IF NOT EXISTS `{$dbName}` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
            $pdoInit->exec("USE `{$dbName}`;");
            
            // Cargar y ejecutar database.sql
            $sqlFile = __DIR__ . '/../database.sql';
            if (file_exists($sqlFile)) {
                $sqlContent = file_get_contents($sqlFile);
                $pdoInit->exec($sqlContent);
            }
            
            // Reconectar con la base de datos creada
            $dsn = "mysql:host={$dbHost};port={$dbPort};dbname={$dbName};charset=utf8mb4";
            $pdo = new PDO($dsn, $dbUser, $dbPass, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (PDOException $ex) {
            $dbErrorMsg = $ex->getMessage();
        }
    } else {
        $dbErrorMsg = $e->getMessage();
    }
}

/**
 * Función helper para verificar conexión a BD
 */
function getDBConnection() {
    global $pdo;
    return $pdo;
}
