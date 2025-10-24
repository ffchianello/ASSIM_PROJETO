<?php
// backend/api/relatorio.php
require_once __DIR__.'/../config.php';
require_once __DIR__.'/../core/Database.php';
$pdo = Database::getInstance();
$q = '%'.($_GET['q'] ?? '').'%';
$cargo = $_GET['cargo'] ?? null;

$sql = "SELECT f.nome, f.telefone, c.nome as cargo, c.salario FROM funcionarios f JOIN cargos c ON f.cargo_id=c.id WHERE 1=1 ";
$params = [];
if(!empty($_GET['q'])){
    $sql .= " AND f.nome LIKE ?";
    $params[] = $q;
}
if(!empty($cargo)){
    $sql .= " AND c.id = ?";
    $params[] = $cargo;
}
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
echo json_encode($stmt->fetchAll(), JSON_UNESCAPED_UNICODE);
