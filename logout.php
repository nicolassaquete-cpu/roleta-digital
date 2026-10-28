<?php
/**
 * Logout do Usuário e Auditoria
 */
require_once __DIR__ . '/helpers/auth_helper.php';
require_once __DIR__ . '/helpers/logger.php';
require_once __DIR__ . '/bd.php';

if (estaAutenticado()) {
    $usuarioId = (int)$_SESSION['usuario_id'];
    $usuarioNome = $_SESSION['usuario_nome'] ?? 'Usuário';

    // Registra log de auditoria do encerramento de sessão
    registrarLog($pdo, 'LOGOUT', 'Usuário encerrou a sessão', $usuarioId, $usuarioNome);

    // Limpa a sessão
    $_SESSION = [];
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy();
}

// Inicia nova sessão limpa para exibir a mensagem flash
iniciarSessao();
definirMensagemFlash('info', 'Você desconectou da sua conta.');

header('Location: login.php');
exit;
