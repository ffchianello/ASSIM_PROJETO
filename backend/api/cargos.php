<?php
// backend/api/cargos.php
require_once __DIR__.'/../config.php';
require_once __DIR__.'/../core/Database.php';

$action = $_GET['action'] ?? 'list';
$pdo = Database::getInstance();

function json($d){ echo json_encode($d, JSON_UNESCAPED_UNICODE); exit; }

if($action === 'list'){
    $stmt = $pdo->query("SELECT * FROM cargos ORDER BY id DESC");
    json($stmt->fetchAll());
}

if($action === 'search'){
    $q = '%'.($_GET['q'] ?? '').'%';
    $stmt = $pdo->prepare("SELECT * FROM cargos WHERE nome LIKE ? ORDER BY id DESC");
    $stmt->execute([$q]);
    json($stmt->fetchAll());
}

if($action === 'create'){
    $data = json_decode(file_get_contents('php://input'), true);
    if(empty($data['nome']) || !isset($data['salario'])) json(['error'=>'Campos obrigatórios']);
    $stmt = $pdo->prepare("INSERT INTO cargos (nome, salario) VALUES (?, ?)");
    $stmt->execute([$data['nome'], $data['salario']]);
    json(['success'=>true, 'id'=>$pdo->lastInsertId()]);
}

if($action === 'update'){
    $data = json_decode(file_get_contents('php://input'), true);
    if(empty($data['id']) || empty($data['nome']) || !isset($data['salario'])) json(['error'=>'Campos obrigatórios']);
    $stmt = $pdo->prepare("UPDATE cargos SET nome=?, salario=? WHERE id=?");
    $stmt->execute([$data['nome'],$data['salario'],$data['id']]);
    json(['success'=>true]);
}

if($action === 'delete'){
    $id = $_GET['id'] ?? null;
    if(!$id) json(['error'=>'id ausente']);
    $stmt = $pdo->prepare("DELETE FROM cargos WHERE id=?");
    $stmt->execute([$id]);
    json(['success'=>true]);
}
