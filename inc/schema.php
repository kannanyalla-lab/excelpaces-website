<?php
function create_schema(PDO $pdo, string $driver): void {
    $ai = $driver === 'sqlite' ? 'INTEGER PRIMARY KEY AUTOINCREMENT' : 'INT AUTO_INCREMENT PRIMARY KEY';
    $lt = $driver === 'sqlite' ? 'TEXT' : 'LONGTEXT';
    $tail = $driver === 'sqlite' ? '' : ' ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci';
    $t = [
    "CREATE TABLE IF NOT EXISTS settings (k VARCHAR(100) PRIMARY KEY, v $lt)$tail",
    "CREATE TABLE IF NOT EXISTS users (id $ai, name VARCHAR(120), email VARCHAR(190) UNIQUE, pass VARCHAR(255), role VARCHAR(20) DEFAULT 'admin', created VARCHAR(30))$tail",
    "CREATE TABLE IF NOT EXISTS menu (id $ai, label VARCHAR(120), url VARCHAR(255), sort INT DEFAULT 0, active TINYINT DEFAULT 1, cta TINYINT DEFAULT 0)$tail",
    "CREATE TABLE IF NOT EXISTS pages (id $ai, slug VARCHAR(120) UNIQUE, title VARCHAR(255), subtitle VARCHAR(255), body $lt, image VARCHAR(255), meta_desc VARCHAR(300), active TINYINT DEFAULT 1)$tail",
    "CREATE TABLE IF NOT EXISTS slides (id $ai, eyebrow VARCHAR(160), title VARCHAR(255), text $lt, btn_label VARCHAR(80), btn_url VARCHAR(255), sort INT DEFAULT 0, active TINYINT DEFAULT 1, image VARCHAR(255))$tail",
    "CREATE TABLE IF NOT EXISTS items (id $ai, kind VARCHAR(30), title VARCHAR(255), text $lt, icon VARCHAR(40), sort INT DEFAULT 0, active TINYINT DEFAULT 1)$tail",
    "CREATE TABLE IF NOT EXISTS faculty (id $ai, name VARCHAR(160), role VARCHAR(255), org VARCHAR(255), bio $lt, photo VARCHAR(255), featured TINYINT DEFAULT 0, sort INT DEFAULT 0, active TINYINT DEFAULT 1)$tail",
    "CREATE TABLE IF NOT EXISTS testimonials (id $ai, name VARCHAR(160), role VARCHAR(255), quote $lt, photo VARCHAR(255), sort INT DEFAULT 0, active TINYINT DEFAULT 1, email VARCHAR(190), status VARCHAR(12) DEFAULT 'approved', created VARCHAR(30))$tail",
    "CREATE TABLE IF NOT EXISTS courses (id $ai, title VARCHAR(255), start_date VARCHAR(12), end_date VARCHAR(12), venue VARCHAR(255), fee VARCHAR(120), seats VARCHAR(60), status VARCHAR(12) DEFAULT 'open', note $lt, active TINYINT DEFAULT 1)$tail",
    "CREATE TABLE IF NOT EXISTS gallery (id $ai, title VARCHAR(255), file VARCHAR(255), sort INT DEFAULT 0, active TINYINT DEFAULT 1)$tail",
    "CREATE TABLE IF NOT EXISTS partners (id $ai, name VARCHAR(160), logo VARCHAR(255), url VARCHAR(255), sort INT DEFAULT 0, active TINYINT DEFAULT 1)$tail",
    "CREATE TABLE IF NOT EXISTS messages (id $ai, name VARCHAR(160), email VARCHAR(190), phone VARCHAR(60), subject VARCHAR(255), body $lt, created VARCHAR(30), is_read TINYINT DEFAULT 0)$tail",
    "CREATE TABLE IF NOT EXISTS applications (id $ai, course_id INT, name VARCHAR(160), email VARCHAR(190), phone VARCHAR(60), country VARCHAR(80), qualification VARCHAR(255), mrcp VARCHAR(120), attempts VARCHAR(40), hospital VARCHAR(255), message $lt, created VARCHAR(30), status VARCHAR(20) DEFAULT 'new', address $lt, gender VARCHAR(20), dob VARCHAR(12), nationality VARCHAR(80), institution VARCHAR(255), percentage VARCHAR(40), year_grad VARCHAR(10), employment $lt)$tail",
    ];
    foreach ($t as $sql) $pdo->exec($sql);
}

/* Adds columns introduced after the first release. Safe to run repeatedly. */
function migrate_schema(PDO $pdo, string $driver): void {
    $lt = $driver === 'sqlite' ? 'TEXT' : 'LONGTEXT';
    $want = [
        'applications' => ['address'=>$lt,'gender'=>'VARCHAR(20)','dob'=>'VARCHAR(12)','nationality'=>'VARCHAR(80)','institution'=>'VARCHAR(255)','percentage'=>'VARCHAR(40)','year_grad'=>'VARCHAR(10)','employment'=>$lt],
        'testimonials' => ['email'=>'VARCHAR(190)','status'=>"VARCHAR(12) DEFAULT 'approved'",'created'=>'VARCHAR(30)'],
        'slides' => ['image'=>'VARCHAR(255)'],
    ];
    foreach ($want as $table => $cols) {
        $have = [];
        if ($driver === 'sqlite') { foreach ($pdo->query("PRAGMA table_info($table)")->fetchAll() as $r) $have[] = $r['name']; }
        else { foreach ($pdo->query("SHOW COLUMNS FROM $table")->fetchAll() as $r) $have[] = $r['Field']; }
        foreach ($cols as $c => $def) if (!in_array($c, $have, true)) $pdo->exec("ALTER TABLE $table ADD COLUMN $c $def");
    }
}
