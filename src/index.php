<?php
$host = getenv('DB_HOST') ?: 'db';
$db   = getenv('DB_NAME') ?: 'devops_db';
$user = getenv('DB_USER') ?: 'user_devops';
$pass = getenv('DB_PASS') ?: 'pass_devops';
$ver  = getenv('APP_VERSION') ?: 'v1';
$msg  = getenv('COMMIT_MSG') ?: 'No message';
$author = "Fabián Capellino";

$conn = null;
for ($i = 0; $i < 10; $i++) {
    $conn = @new mysqli($host, $user, $pass, $db);
    if (!$conn->connect_error) break;
    sleep(2);
}

$conn->query("CREATE TABLE IF NOT EXISTS publicaciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100),
    version VARCHAR(50),
    mensaje TEXT,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

$stmtCheck = $conn->prepare("SELECT id FROM publicaciones WHERE version = ?");
$stmtCheck->bind_param("s", $ver);
$stmtCheck->execute();
if ($stmtCheck->get_result()->num_rows === 0) {
    $stmtIns = $conn->prepare("INSERT INTO publicaciones (nombre, version, mensaje) VALUES (?, ?, ?)");
    $stmtIns->bind_param("sss", $author, $ver, $msg);
    $stmtIns->execute();
}

$result = $conn->query("SELECT * FROM publicaciones ORDER BY id ASC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Fabián Capellino</title>
    <style>
        body { font-family: sans-serif; margin: 40px; }
        table { border-collapse: collapse; width: 100%; max-width: 800px; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: left; }
        th { background-color: #f4f4f4; }
    </style>
</head>
<body>
    <h1>Fabián Capellino</h1>
    <table>
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Versión</th>
                <th>Mensaje del Commit</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['nombre']) ?></td>
                <td><?= htmlspecialchars($row['version']) ?></td>
                <td><?= htmlspecialchars($row['mensaje']) ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>