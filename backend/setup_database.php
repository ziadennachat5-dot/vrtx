<?php
$databaseFile = __DIR__ . DIRECTORY_SEPARATOR . 'database.sqlite';
try {
	$pdo = new PDO('sqlite:' . $databaseFile);
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$pdo->exec('CREATE TABLE IF NOT EXISTS compte (
		id INTEGER PRIMARY KEY AUTOINCREMENT,
		email TEXT NOT NULL UNIQUE,
		password_hash TEXT NOT NULL,
		created_at TEXT NOT NULL
	)');
	echo "Database and table 'compte' are ready at: {$databaseFile}\n";
	echo "Fields: id, email (unique), password_hash, created_at\n";
} catch (Throwable $e) {
	http_response_code(500);
	echo 'Setup failed: ' . $e->getMessage();
	exit(1);
}
