<?php
/**
 * API: Salva ou atualiza uma roleta com seus itens
 * Valida o limite de itens conforme o plano (Gratuito: máx 50 itens; Premium: ilimitado)
 * Grava log de auditoria via PDO
 */
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../helpers/auth_helper.php';
require_once __DIR__ . '/../helpers/logger.php';
require_once __DIR__ . '/../bd.php';

if (!estaAutenticado()) {
    echo json_encode([
        'sucesso' => false, 
        'mensagem' => 'Você precisa estar conectado para salvar roletas na sua conta.'
    ]);
    exit;
}

try {
    $dados = json_decode(file_get_contents('php://input'), true);
    $roletaId = !empty($dados['id']) ? (int)$dados['id'] : null;
    $titulo = trim($dados['titulo'] ?? 'Minha Roleta');
    $itens = $dados['itens'] ?? [];
    $usuarioId = (int)$_SESSION['usuario_id'];

    if (empty($titulo)) {
        $titulo = 'Minha Roleta';
    }

    if (!is_array($itens) || count($itens) === 0) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'A roleta precisa de ao menos 1 item.']);
        exit;
    }

    // Validação do Limite de Itens por Plano (CORE)
    $limitePermitido = limiteItensRoleta();
    $totalItens = count($itens);

    if ($totalItens > $limitePermitido) {
        echo json_encode([
            'sucesso' => false,
            'mensagem' => "O plano gratuito permite no máximo {$limitePermitido} itens. Você tentou salvar {$totalItens} itens. Faça upgrade para o Premium!"
        ]);
        exit;
    }

    // Inicia transação PDO para garantir consistência
    $pdo->beginTransaction();

    if ($roletaId) {
        // Atualiza roleta existente se pertencer ao usuário
        $sqlCheck = "SELECT id FROM roletas WHERE id = :id AND usuario_id = :usuario_id";
        $stmtCheck = $pdo->prepare($sqlCheck);
        $stmtCheck->execute([':id' => $roletaId, ':usuario_id' => $usuarioId]);
        if (!$stmtCheck->fetch()) {
            $pdo->rollBack();
            echo json_encode(['sucesso' => false, 'mensagem' => 'Roleta não encontrada ou acesso negado.']);
            exit;
        }

        $sqlUpdate = "UPDATE roletas SET titulo = :titulo, atualizado_em = NOW() WHERE id = :id";
        $stmtUpdate = $pdo->prepare($sqlUpdate);
        $stmtUpdate->execute([':titulo' => $titulo, ':id' => $roletaId]);

        // Remove itens antigos para reinserção limpa
        $sqlDelItens = "DELETE FROM itens_roleta WHERE roleta_id = :roleta_id";
        $stmtDelItens = $pdo->prepare($sqlDelItens);
        $stmtDelItens->execute([':roleta_id' => $roletaId]);

        $acaoLog = 'EDITAR_ROLETA';
    } else {
        // Cria nova roleta
        $sqlInsert = "INSERT INTO roletas (usuario_id, titulo, criado_em, atualizado_em) 
                      VALUES (:usuario_id, :titulo, NOW(), NOW())";
        $stmtInsert = $pdo->prepare($sqlInsert);
        $stmtInsert->execute([':usuario_id' => $usuarioId, ':titulo' => $titulo]);
        $roletaId = (int)$pdo->lastInsertId();

        $acaoLog = 'CRIAR_ROLETA';
    }

    // Insere todos os itens da roleta via PDO Prepared Statement
    $sqlItem = "INSERT INTO itens_roleta (roleta_id, texto, cor, peso, criado_em) 
                VALUES (:roleta_id, :texto, :cor, :peso, NOW())";
    $stmtItem = $pdo->prepare($sqlItem);

    foreach ($itens as $item) {
        $textoItem = trim($item['texto'] ?? '');
        $corItem = trim($item['cor'] ?? '#6366f1');
        $pesoItem = !empty($item['peso']) ? (int)$item['peso'] : 1;

        if ($textoItem !== '') {
            $stmtItem->execute([
                ':roleta_id' => $roletaId,
                ':texto'     => $textoItem,
                ':cor'       => $corItem,
                ':peso'      => $pesoItem
            ]);
        }
    }

    // Registra log de auditoria
    registrarLog(
        $pdo,
        $acaoLog,
        [
            'roleta_id'   => $roletaId,
            'titulo'      => $titulo,
            'total_itens' => $totalItens
        ],
        $usuarioId
    );

    $pdo->commit();

    echo json_encode([
        'sucesso'   => true, 
        'roleta_id' => $roletaId, 
        'mensagem'  => 'Roleta salva com sucesso!'
    ]);
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo json_encode([
        'sucesso'  => false, 
        'mensagem' => 'Erro ao salvar roleta: ' . $e->getMessage()
    ]);
}
