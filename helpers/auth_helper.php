<?php
/**
 * Helper de Autenticação e Controle de Planos (Sessões Nativas PHP)
 * Requisitos:
 * - Tratar senhas de usuários com hash bcrypt
 * - Plano gratuito: max 50 itens na roleta + anúncios
 * - Plano premium: itens ilimitados + sem anúncios
 */

// Inicia sessão nativa com configurações seguras
function iniciarSessao(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        // Define opções recomendadas de sessão
        ini_set('session.cookie_httponly', 1);
        ini_set('session.use_only_cookies', 1);
        session_start();
    }
}

// Inicia automaticamente ao incluir o helper
iniciarSessao();

/**
 * Retorna true se o usuário estiver autenticado na sessão
 */
function estaAutenticado(): bool
{
    return !empty($_SESSION['usuario_id']);
}

/**
 * Retorna dados do usuário atualmente logado ou null
 */
function obterUsuarioLogado(): ?array
{
    if (!estaAutenticado()) {
        return null;
    }

    return [
        'id'     => (int)$_SESSION['usuario_id'],
        'nome'   => $_SESSION['usuario_nome'] ?? 'Usuário',
        'email'  => $_SESSION['usuario_email'] ?? '',
        'plano'  => $_SESSION['usuario_plano'] ?? 'gratuito',
    ];
}

/**
 * Verifica se o usuário atual possui plano Premium ativo
 */
function ehPremium(): bool
{
    $usuario = obterUsuarioLogado();
    if (!$usuario) {
        return false; // Visitantes usam plano gratuito
    }
    return strtolower($usuario['plano']) === 'premium';
}

/**
 * Retorna o limite de itens permitidos na roleta conforme o plano:
 * - Plano Gratuito: 50 itens
 * - Plano Premium: Ilimitado (999999)
 */
function limiteItensRoleta(): int
{
    return ehPremium() ? 999999 : 50;
}

/**
 * Força a autenticação para páginas protegidas
 */
function exigirAutenticacao(): void
{
    if (!estaAutenticado()) {
        definirMensagemFlash('warning', 'Você precisa entrar na sua conta para acessar esta página.');
        header('Location: login.php');
        exit;
    }
}

/**
 * Define mensagens flash para exibição em TOAST na interface
 * @param string $tipo 'success', 'error', 'warning', 'info'
 * @param string $mensagem Texto a exibir
 */
function definirMensagemFlash(string $tipo, string $mensagem): void
{
    iniciarSessao();
    $_SESSION['flash_messages'][] = [
        'tipo'     => $tipo,
        'mensagem' => $mensagem
    ];
}

/**
 * Recupera e consome as mensagens flash da sessão
 */
function obterMensagensFlash(): array
{
    iniciarSessao();
    $mensagens = $_SESSION['flash_messages'] ?? [];
    unset($_SESSION['flash_messages']);
    return $mensagens;
}
