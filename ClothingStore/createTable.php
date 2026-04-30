<?php
/**
 * createTable.php
 * Checks if tblUser exists → drops it → recreates it → loads userData.txt
 */

// Include the database connection
require_once 'DBConn.php';

$messages = [];

// ── 1. Drop tblUser if it exists ─────────────────────────────────────────────
$dropSQL = "DROP TABLE IF EXISTS tblUser";
if ($conn->query($dropSQL)) {
    $messages[] = "✅ tblUser dropped (or did not exist).";
} else {
    $messages[] = "❌ Error dropping tblUser: " . $conn->error;
}

// ── 2. Create tblUser ─────────────────────────────────────────────────────────
$createSQL = "
CREATE TABLE IF NOT EXISTS tblUser (
    userID      INT AUTO_INCREMENT PRIMARY KEY,
    fullName    VARCHAR(100)  NOT NULL,
    email       VARCHAR(150)  NOT NULL UNIQUE,
    password    VARCHAR(255)  NOT NULL,
    province    VARCHAR(50)   DEFAULT NULL,
    isVerified  TINYINT(1)    NOT NULL DEFAULT 0,
    status      ENUM('active','inactive','pending') NOT NULL DEFAULT 'pending',
    createdAt   DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB CHARACTER SET utf8mb4;
";

if ($conn->query($createSQL)) {
    $messages[] = "✅ tblUser created successfully.";
} else {
    $messages[] = "❌ Error creating tblUser: " . $conn->error;
    // Cannot continue without the table
    showPage($messages);
    exit;
}

// ── 3. Load data from userData.txt ────────────────────────────────────────────
$filePath = __DIR__ . '/userData.txt';

if (!file_exists($filePath)) {
    $messages[] = "❌ userData.txt not found at: $filePath";
} else {
    $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $inserted = 0;
    $errors   = 0;

    // Prepare a parameterised INSERT statement
    $stmt = $conn->prepare(
        "INSERT INTO tblUser (fullName, email, password, province, isVerified, status)
         VALUES (?, ?, ?, ?, ?, ?)"
    );

    if (!$stmt) {
        $messages[] = "❌ Prepare failed: " . $conn->error;
    } else {
        foreach ($lines as $line) {
            // Tab-delimited: fullName  email  password  province  isVerified  status
            $cols = explode("\t", $line);
            if (count($cols) < 6) {
                $messages[] = "⚠️  Skipped malformed line: $line";
                $errors++;
                continue;
            }

            [$fullName, $email, $password, $province, $isVerified, $status] = $cols;
            $isVerified = (int) trim($isVerified);
            $status     = trim($status);

            $stmt->bind_param("ssssis",
                $fullName, $email, $password, $province, $isVerified, $status
            );

            if ($stmt->execute()) {
                $inserted++;
            } else {
                $messages[] = "⚠️  Could not insert '$email': " . $stmt->error;
                $errors++;
            }
        }
        $stmt->close();
        $messages[] = "✅ Loaded $inserted rows from userData.txt ($errors errors).";
    }
}

$conn->close();

// ── 4. Show result page ───────────────────────────────────────────────────────
showPage($messages);

function showPage(array $msgs): void {
    echo "<!DOCTYPE html>
<html lang='en'>
<head>
<meta charset='UTF-8'>
<title>createTable.php — ClothingStore</title>
<style>
  body { font-family: 'Segoe UI', sans-serif; background:#0f0f0f; color:#e5e5e5; padding:2rem; }
  h1   { color:#c9a86c; }
  ul   { list-style:none; padding:0; }
  li   { background:#1a1a1a; border-left:4px solid #c9a86c; padding:.6rem 1rem; margin:.4rem 0; border-radius:4px; }
  a    { color:#c9a86c; }
</style>
</head>
<body>
<h1>createTable.php — Execution Log</h1>
<ul>";
    foreach ($msgs as $m) {
        echo "<li>" . htmlspecialchars($m) . "</li>";
    }
    echo "</ul><p><a href='index.php'>← Back to Home</a></p>
</body></html>";
}
?>
