<?php
/**
 * Helper de Auditoria e Logs
 * Requisito CORE: "manter sempre os logs de qualquer alteração feita por qualquer usuário, para auditorias futuras"
 * Regra Global: "Use sempre PDO para conexão e queries no MySQL para evitar SQL Injections"
 */

/**
 * Registra um log de auditoria no banco de dados via PDO.
 *
 * @param PDO $pdo Instância da conexão PDO
 * @param string $acao Nome da ação (ex: 'CADASTRO', 'LOGIN', 'CRIAR_ROLETA', 'EDITAR_ROLETA', 'EXCLUIR_ROLETA', 'SORTEIO')
 * @param string|array|null $detalhes Informações descritivas sobre a alteração/evento
 * @param int|null $usuarioId ID do usuário que executou a ação (opcional)
 * @param string|null $usuarioNome Nome do usuário que executou a ação (opcional)
 * @return bool Retorna true em caso de sucesso
 */
function registrarLog(PDO $pdo, string $acao, $detalhes = null, ?int $usuarioId = null, ?string $usuarioNome = null): bool
{
    try {
        // Se detalhes for array ou objeto, serializa em JSON limpo
        if (is_array($detalhes) || is_object($detalhes)) {
            $detalhes = json_encode($detalhes, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        }

        // Tenta obter usuário da sessão se não tiver sido passado explicitamente
        if ($usuarioId === null && isset($_SESSION['usuario_id'])) {
            $usuarioId = (int)$_SESSION['usuario_id'];
        }
        if ($usuarioNome === null && isset($_SESSION['usuario_nome'])) {
            $usuarioNome = $_SESSION['usuario_nome'];
        }

        // Obtém o IP do cliente de forma segura
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        if (strpos($ip, ',') !== false) {
            $ip = trim(explode(',', $ip)[0]);
        }

        $sql = "INSERT INTO logs_auditoria (usuario_id, usuario_nome, acao, detalhes, ip, criado_em)
                VALUES (:usuario_id, :usuario_nome, :acao, :detalhes, :ip, NOW())";

        $stmt = $pdo->prepare($sql);
        return $stmt->execute([
            ':usuario_id'   => $usuarioId,
            ':usuario_nome' => $usuarioNome ?: ($usuarioId ? "Usuário #$usuarioId" : 'Anônimo / Visitante'),
            ':acao'         => strtoupper(trim($acao)),
            ':detalhes'     => $detalhes,
            ':ip'           => substr($ip, 0, 45)
        ]);
    } catch (Exception $e) {
        // Em caso de falha no log, não quebra a aplicação principal, mas grava em error_log
        error_log("Falha ao registrar log de auditoria: " . $e->getMessage());
        return false;
    }
}
