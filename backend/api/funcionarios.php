<?php
// backend/api/funcionarios.php
require_once __DIR__.'/../config.php';
require_once __DIR__.'/../core/Database.php';

$action = $_GET['action'] ?? 'list';
$pdo = Database::getInstance();
function json($d){ echo json_encode($d, JSON_UNESCAPED_UNICODE); exit; }

// CPF utility
function cpf_only_digits($s){ return preg_replace('/\D/', '', $s); }
function cpf_valid($cpf){
    $c = cpf_only_digits($cpf);
    if(strlen($c) != 11) return false;
    if(preg_match('/^(\\d)\\1+$/', $c)) return false;
    for ($t = 9; $t < 11; $t++) {
        for ($d = 0, $cIndex = 0; $cIndex < $t; $cIndex++) {
            $d += $c[$cIndex] * (($t + 1) - $cIndex);
        }
        $d = ((10 * $d) % 11) % 10;
        if ($c[$cIndex] != $d) return false;
    }
    return true;
}

if($action === 'list'){
    $stmt = $pdo->query("SELECT f.*, c.nome as cargo_nome, c.salario FROM funcionarios f JOIN cargos c ON f.cargo_id = c.id ORDER BY f.id DESC");
    json($stmt->fetchAll());
}

if($action === 'search'){
    $q = '%'.($_GET['q'] ?? '').'%';
    $cpf = $_GET['cpf'] ?? null;
    if($cpf){
        $stmt = $pdo->prepare("SELECT f.*, c.nome as cargo_nome, c.salario FROM funcionarios f JOIN cargos c ON f.cargo_id=c.id WHERE f.cpf = ? LIMIT 1");
        $stmt->execute([ $cpf ]);
        json($stmt->fetchAll());
    } else {
        $stmt = $pdo->prepare("SELECT f.*, c.nome as cargo_nome, c.salario FROM funcionarios f JOIN cargos c ON f.cargo_id=c.id WHERE f.nome LIKE ? ORDER BY f.id DESC");
        $stmt->execute([$q]);
        json($stmt->fetchAll());
    }
}

if($action === 'create'){
    $data = json_decode(file_get_contents('php://input'), true);
    if(empty($data['nome']) || empty($data['cpf']) || empty($data['cargo_id'])) json(['error'=>'Campos obrigatórios']);
    if(!cpf_valid($data['cpf'])) json(['error'=>'CPF inválido']);
    // uniqueness
    $stmt = $pdo->prepare("SELECT id FROM funcionarios WHERE cpf = ?");
    $stmt->execute([$data['cpf']]);
    if($stmt->fetch()) json(['error'=>'CPF já cadastrado']);
    $stmt = $pdo->prepare("INSERT INTO funcionarios (nome, data_nascimento, endereco, cpf, email, telefone, cargo_id) VALUES (?,?,?,?,?,?,?)");
    $stmt->execute([
        $data['nome'],
        $data['data_nascimento'] ?: null,
        $data['endereco'] ?: null,
        $data['cpf'],
        $data['email'] ?: null,
        $data['telefone'] ?: null,
        $data['cargo_id']
    ]);
    json(['success'=>true, 'id'=>$pdo->lastInsertId()]);
}

if($action === 'update'){
    $data = json_decode(file_get_contents('php://input'), true);
    if(empty($data['id']) || empty($data['nome']) || empty($data['cpf']) || empty($data['cargo_id'])) json(['error'=>'Campos obrigatórios']);
    if(!cpf_valid($data['cpf'])) json(['error'=>'CPF inválido']);
    // check cpf unique excluding this id
    $stmt = $pdo->prepare("SELECT id FROM funcionarios WHERE cpf = ? AND id != ?");
    $stmt->execute([$data['cpf'], $data['id']]);
    if($stmt->fetch()) json(['error'=>'CPF já cadastrado por outro funcionário']);
    $stmt = $pdo->prepare("UPDATE funcionarios SET nome=?, data_nascimento=?, endereco=?, cpf=?, email=?, telefone=?, cargo_id=? WHERE id=?");
    $stmt->execute([
        $data['nome'],
        $data['data_nascimento'] ?: null,
        $data['endereco'] ?: null,
        $data['cpf'],
        $data['email'] ?: null,
        $data['telefone'] ?: null,
        $data['cargo_id'],
        $data['id']
    ]);
    json(['success'=>true]);
}

if($action === 'delete'){
    $id = $_GET['id'] ?? null;
    if(!$id) json(['error'=>'id ausente']);
    $stmt = $pdo->prepare("DELETE FROM funcionarios WHERE id=?");
    $stmt->execute([$id]);
    json(['success'=>true]);
}
