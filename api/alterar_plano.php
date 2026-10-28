<?php
/**
 * Ação: Altera o plano do usuário logado (Gratuito <-> Premium)
 * Grava log de auditoria via PDO
 */
require_once __DIR__ . '/../helpers/auth_helper.php';
require_once __DIR__ . '/../helpers/logger.php';
require_once __DIR__ . '/../bd.php';

exigirAutenticacao();

$usuarioId = (int)$_SESSION['usuario_id'];
$novoPlano = strtolower(trim($_POST['plano'] ?? 'gratuito'));

if (!in_array($novoPlano, ['gratuito', 'premium'])) {
    definirMensagemFlash('error', 'Plano selecionado inválido.');
    header('Location: ../planos.php');
    exit;
}

try {
    // Busca plano atual para log
    $stmtUser = $pdo->prepare("SELECT plano FROM usuarios WHERE id = :id");
    $stmtUser->execute([':id' => $usuarioId]);
    $planoAnterior = $stmtUser->fetchColumn() ?: 'gratuito';

    // Atualiza plano via PDO
    $stmt = $pdo->prepare("UPDATE usuarios SET plano = :plano, atualizado_em = NOW() WHERE id = :id");
    $stmt->execute([':plano' => $novoPlano, ':id' => $usuarioId]);

    // Atualiza a sessão
    $_SESSION['usuario_plano'] = $novoPlano;

    // Registra log de auditoria
    registrarLog(
        $pdo,
        'ALTERACAO_PLANO',
        [
            'plano_anterior' => $planoAnterior,
            'novo_plano'     => $novoPlano
        ],
        $usuarioId
    );

    if ($novoPlano === 'premium') {
        definirMensagemFlash('success', 'Parabéns! Seu Plano Premium foi ativado com sucesso! Você agora tem itens ilimitados e zero anúncios.');
    } else {
        definirMensagemFlash('info', 'Seu plano foi alterado para Gratuito.');
    }
} catch (Exception $e) {
    definirMensagemFlash('error', 'Erro ao alterar plano: ' . $e->getMessage());
}

$retorno = $_SERVER['HTTP_REFERER'] ?? '../planos.php';
header("Location: $retorno");
exit;
