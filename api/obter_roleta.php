<?php
/**
 * API: Retorna dados de uma roleta em JSON
 * Regra Global: Use sempre PDO para conexão e queries no MySQL para evitar SQL Injections
 */
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../helpers/auth_helper.php';
require_once __DIR__ . '/../bd.php';

$roletaId = !empty($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$roletaId) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'ID não informado.']);
    exit;
}

try {
    $stmtRoleta = $pdo->prepare("SELECT id, titulo, descricao FROM roletas WHERE id = :id");
    $stmtRoleta->execute([':id' => $roletaId]);
    $roleta = $stmtRoleta->fetch();

    if (!$roleta) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Roleta não encontrada.']);
        exit;
    }

    $stmtItens = $pdo->prepare("SELECT texto, cor, peso FROM itens_roleta WHERE roleta_id = :id ORDER BY id ASC");
    $stmtItens->execute([':id' => $roletaId]);
    $itens = $stmtItens->fetchAll();

    echo json_encode([
        'sucesso' => true,
        'roleta'  => $roleta,
        'itens'   => $itens
    ]);
} catch (Exception $e) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao consultar roleta: ' . $e->getMessage()]);
}
