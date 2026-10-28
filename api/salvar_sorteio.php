<?php
/**
 * API: Registra sorteio realizado e grava log de auditoria
 * Regra Global: Use sempre PDO para conexão e queries no MySQL para evitar SQL Injections
 */
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../helpers/auth_helper.php';
require_once __DIR__ . '/../helpers/logger.php';
require_once __DIR__ . '/../bd.php';

try {
    $dados = json_decode(file_get_contents('php://input'), true);
    $itemSorteado = trim($dados['item_sorteado'] ?? '');
    $roletaId = !empty($dados['roleta_id']) ? (int)$dados['roleta_id'] : null;
    $usuarioId = estaAutenticado() ? (int)$_SESSION['usuario_id'] : null;

    if (empty($itemSorteado)) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Item sorteado não informado.']);
        exit;
    }

    // Insere no histórico de sorteios usando PDO Prepared Statement
    $sql = "INSERT INTO historico_sorteios (usuario_id, roleta_id, item_sorteado, criado_em)
            VALUES (:usuario_id, :roleta_id, :item_sorteado, NOW())";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':usuario_id'    => $usuarioId,
        ':roleta_id'     => $roletaId,
        ':item_sorteado' => $itemSorteado
    ]);

    $sorteioId = $pdo->lastInsertId();

    // Registra auditoria
    registrarLog(
        $pdo, 
        'SORTEIO_REALIZADO', 
        [
            'sorteio_id' => $sorteioId,
            'roleta_id' => $roletaId,
            'item_sorteado' => $itemSorteado
        ],
        $usuarioId
    );

    echo json_encode(['sucesso' => true, 'sorteio_id' => $sorteioId]);
} catch (Exception $e) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Erro interno ao salvar sorteio: ' . $e->getMessage()]);
}
