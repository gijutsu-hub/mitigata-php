<?php
// Shared SQLite bootstrap. Seeds a tiny DB on first run.
function db() {
    $path = '/tmp/vuln.sqlite';
    $fresh = !file_exists($path);
    $pdo = new PDO('sqlite:' . $path);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    if ($fresh) {
        $pdo->exec("CREATE TABLE users(id INTEGER PRIMARY KEY, username TEXT, password TEXT, role TEXT, secret TEXT)");
        $pdo->exec("INSERT INTO users(username,password,role,secret) VALUES
            ('alice','password123','user','alice-api-key-001'),
            ('bob','hunter2','user','bob-api-key-002'),
            ('admin','S3cr3t_Adm1n_!','admin','FLAG{php_admin_pwned}')");
        $pdo->exec("CREATE TABLE notes(id INTEGER PRIMARY KEY, owner_id INTEGER, body TEXT)");
        $pdo->exec("INSERT INTO notes(owner_id,body) VALUES
            (1,'alice private note'),(2,'bob private note'),(3,'admin: do not leak FLAG{php_idor}')");
    }
    return $pdo;
}
