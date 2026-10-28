<?php
/**
 * API: Exclui uma roleta do usuário com registro de auditoria
 */
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../helpers/auth_helper.php';
require_once __DIR__ . '/../helpers/logger.php';
require_once __DIR__ . '/../bd.php';

if (!estaAutenticado()) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Não autorizado.']);
    exit;
}

try {
    $dados = json_decode(file_get_contents('php://input'), true);
    $roletaId = !empty($dados['id']) ? (int)$dados['id'] : 0;
    $usuarioId = (int)$_SESSION['usuario_id'];

    if (!$roletaId) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'ID da roleta inválido.']);
        exit;
    }

    // Busca dados da roleta antes de excluir para o log
    $stmtBusca = $pdo->prepare("SELECT titulo FROM roletas WHERE id = :id AND usuario_id = :usuario_id");
    $stmtBusca->execute([':id' => $roletaId, ':usuario_id' => $usuarioId]);
    $roleta = $stmtBusca->fetch();

    if (!$roleta) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Roleta não encontrada ou você não tem permissão para excluí-la.']);
        exit;
    }

    // Exclusão via PDO
    $stmtDel = $pdo->prepare("DELETE FROM roletas WHERE id = :id AND usuario_id = :usuario_id");
    $stmtDel->execute([':id' => $roletaId, ':usuario_id' => $usuarioId]);

    // Registra auditoria
    registrarLog(
        $pdo,
        'EXCLUIR_ROLETA',
        ['roleta_id' => $roletaId, 'titulo' => $roleta['titulo']],
        $usuarioId
    );

    echo json_encode(['sucesso' => true, 'mensagem' => 'Roleta excluída com sucesso!']);
} catch (Exception $e) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao excluir roleta: ' . $e->getMessage()]);
}
