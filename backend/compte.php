<?php
header('Content-Type: application/json; charset=utf-8');
$databaseFile = __DIR__ . DIRECTORY_SEPARATOR . 'database.sqlite';
function respond($success, $message, $extra = []) {
	echo json_encode(array_merge(['success' => $success, 'message' => $message], $extra));
	exit;
}
try {
	$pdo = new PDO('sqlite:' . $databaseFile);
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$pdo->exec('CREATE TABLE IF NOT EXISTS compte (
		id INTEGER PRIMARY KEY AUTOINCREMENT,
		email TEXT NOT NULL UNIQUE,
		password_hash TEXT NOT NULL,
		created_at TEXT NOT NULL
	)');
} catch (Throwable $e) {
	http_response_code(500);
	respond(false, 'Erreur base de donnÃ©es: ' . $e->getMessage());
}
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
if ($method !== 'POST') {
	respond(false, 'MÃ©thode non supportÃ©e. Utilisez POST.');
}
$action = $_POST['action'] ?? '';
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
	respond(false, 'Email invalide.');
}
if (strlen($password) < 6) {
	respond(false, 'Mot de passe trop court (min 6).');
}
try {
	if ($action === 'register') {
		$hash = password_hash($password, PASSWORD_DEFAULT);
		$stmt = $pdo->prepare('INSERT INTO compte (email, password_hash, created_at) VALUES (:email, :hash, :created_at)');
		$stmt->execute([
			':email' => $email,
			':hash' => $hash,
			':created_at' => (new DateTimeImmutable('now', new DateTimeZone('UTC')))->format('c'),
		]);
		respond(true, 'Compte crÃ©Ã© avec succÃ¨s.');
	} elseif ($action === 'login') {
		$stmt = $pdo->prepare('SELECT id, email, password_hash, created_at FROM compte WHERE email = :email');
		$stmt->execute([':email' => $email]);
		$user = $stmt->fetch(PDO::FETCH_ASSOC);
		if (!$user || !password_verify($password, $user['password_hash'])) {
			respond(false, 'Identifiants invalides.');
		}
		respond(true, 'Connexion rÃ©ussie.', ['user' => ['id' => (int)$user['id'], 'email' => $user['email']]]);
	} else {
		respond(false, 'Action non reconnue.');
	}
} catch (PDOException $e) {
	if ((int)$e->getCode() === 23000) {
		respond(false, 'Un compte avec cet email existe dÃ©jÃ .');
	}
	respond(false, 'Erreur SQL: ' . $e->getMessage());
}
