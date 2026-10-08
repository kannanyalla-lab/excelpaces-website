<?php
function db(): PDO {
    static $pdo = null;
    if ($pdo) return $pdo;
    global $CFG;
    if (($CFG['driver'] ?? 'mysql') === 'sqlite') {
        $pdo = new PDO('sqlite:' . ROOT . '/' . ($CFG['sqlite'] ?? 'data.sqlite'));
        $pdo->exec('PRAGMA foreign_keys=ON');
    } else {
        $dsn = 'mysql:host=' . $CFG['host'] . ';dbname=' . $CFG['name'] . ';charset=utf8mb4';
        $pdo = new PDO($dsn, $CFG['user'], $CFG['pass']);
        $pdo->exec("SET NAMES utf8mb4");
    }
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    return $pdo;
}
function q(string $sql, array $p = []): PDOStatement { $s = db()->prepare($sql); $s->execute($p); return $s; }
function all(string $sql, array $p = []): array { return q($sql, $p)->fetchAll(); }
function one(string $sql, array $p = []): ?array { $r = q($sql, $p)->fetch(); return $r ?: null; }
function val(string $sql, array $p = []) { $r = q($sql, $p)->fetch(PDO::FETCH_NUM); return $r ? $r[0] : null; }
function ins(string $table, array $data): int {
    $c = array_keys($data);
    q("INSERT INTO $table (" . implode(',', $c) . ") VALUES (" . implode(',', array_fill(0, count($c), '?')) . ")", array_values($data));
    return (int)db()->lastInsertId();
}
function upd(string $table, int $id, array $data): void {
    $set = implode(',', array_map(fn($c) => "$c=?", array_keys($data)));
    q("UPDATE $table SET $set WHERE id=?", [...array_values($data), $id]);
}
