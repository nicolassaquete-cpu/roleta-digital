<?php
/**
 * Página de Históricos e Logs de Auditoria
 * Requisito CORE: "O sistema deve ter uma página de históricos e manter sempre os logs de qualquer alteração feita por qualquer usuário, para auditorias futuras."
 * Regra Global: "Use sempre PDO para conexão e queries no MySQL para evitar SQL Injections"
 */
require_once __DIR__ . '/helpers/auth_helper.php';
require_once __DIR__ . '/helpers/logger.php';
require_once __DIR__ . '/bd.php';

$usuario = obterUsuarioLogado();
$abaAtiva = $_GET['aba'] ?? 'sorteios';
$busca = trim($_GET['q'] ?? '');

$tituloPagina = 'Histórico & Logs de Auditoria';

// Consulta Histórico de Sorteios via PDO
$sorteios = [];
try {
    $sqlSorteios = "SELECT s.*, r.titulo as roleta_titulo, u.nome as usuario_nome 
                    FROM historico_sorteios s 
                    LEFT JOIN roletas r ON s.roleta_id = r.id 
                    LEFT JOIN usuarios u ON s.usuario_id = u.id ";
    
    $paramsSorteios = [];
    if (!empty($busca) && $abaAtiva === 'sorteios') {
        $sqlSorteios .= " WHERE s.item_sorteado LIKE :busca OR r.titulo LIKE :busca OR u.nome LIKE :busca ";
        $paramsSorteios[':busca'] = "%{$busca}%";
    }

    $sqlSorteios .= " ORDER BY s.id DESC LIMIT 100";
    $stmtSorteios = $pdo->prepare($sqlSorteios);
    $stmtSorteios->execute($paramsSorteios);
    $sorteios = $stmtSorteios->fetchAll();
} catch (Exception $e) {}

// Consulta Logs de Auditoria via PDO (para auditorias futuras)
$logsAuditoria = [];
try {
    $sqlLogs = "SELECT l.*, u.nome as usuario_rel_nome, u.email as usuario_email 
                FROM logs_auditoria l 
                LEFT JOIN usuarios u ON l.usuario_id = u.id ";
    
    $paramsLogs = [];
    if (!empty($busca) && $abaAtiva === 'auditoria') {
        $sqlLogs .= " WHERE l.acao LIKE :busca OR l.detalhes LIKE :busca OR l.usuario_nome LIKE :busca OR l.ip LIKE :busca ";
        $paramsLogs[':busca'] = "%{$busca}%";
    }

    $sqlLogs .= " ORDER BY l.id DESC LIMIT 150";
    $stmtLogs = $pdo->prepare($sqlLogs);
    $stmtLogs->execute($paramsLogs);
    $logsAuditoria = $stmtLogs->fetchAll();
} catch (Exception $e) {}

require_once __DIR__ . '/views/header.php';
?>

<main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    <!-- Cabeçalho da Página -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <div class="flex items-center gap-2">
                <div class="p-2 rounded-xl bg-indigo-600/20 text-indigo-400 border border-indigo-500/30">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">Histórico & Auditoria</h1>
            </div>
            <p class="text-sm text-slate-400 mt-1">Registros permanentes de sorteios e logs de auditoria do sistema</p>
        </div>

        <!-- Abas de Navegação -->
        <div class="flex items-center gap-2 bg-slate-900/80 p-1.5 rounded-2xl border border-slate-800 shrink-0">
            <a href="?aba=sorteios<?= $busca ? '&q=' . urlencode($busca) : '' ?>" 
                class="px-4 py-2 rounded-xl text-xs sm:text-sm font-semibold transition <?= $abaAtiva === 'sorteios' ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30' : 'text-slate-400 hover:text-white' ?>">
                🎯 Sorteios Realizados
            </a>
            <a href="?aba=auditoria<?= $busca ? '&q=' . urlencode($busca) : '' ?>" 
                class="px-4 py-2 rounded-xl text-xs sm:text-sm font-semibold transition <?= $abaAtiva === 'auditoria' ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30' : 'text-slate-400 hover:text-white' ?>">
                🛡️ Logs de Auditoria
            </a>
        </div>
    </div>

    <!-- Barra de Filtro e Busca -->
    <div class="bg-slate-900/60 border border-slate-800 rounded-2xl p-4 mb-6 flex flex-col sm:flex-row items-center justify-between gap-3">
        <form method="GET" class="w-full sm:w-auto flex-1 flex items-center gap-2">
            <input type="hidden" name="aba" value="<?= htmlspecialchars($abaAtiva) ?>">
            <div class="relative flex-1 max-w-md">
                <input type="text" name="q" value="<?= htmlspecialchars($busca) ?>" placeholder="Filtrar registros..." 
                    class="w-full pl-9 pr-4 py-2 bg-slate-950/70 border border-slate-700/80 rounded-xl text-xs sm:text-sm text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500">
                <svg class="w-4 h-4 text-slate-500 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
            <button type="submit" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold border border-slate-700 transition">
                Buscar
            </button>
            <?php if ($busca): ?>
                <a href="?aba=<?= htmlspecialchars($abaAtiva) ?>" class="text-xs text-rose-400 hover:underline">Limpar</a>
            <?php endif; ?>
        </form>

        <div class="text-xs text-slate-400">
            Mostrando <?= $abaAtiva === 'sorteios' ? count($sorteios) : count($logsAuditoria) ?> registro(s)
        </div>
    </div>

    <!-- Conteúdo da Aba 1: Sorteios Realizados -->
    <?php if ($abaAtiva === 'sorteios'): ?>
        <div class="bg-slate-900/80 border border-slate-800 rounded-3xl overflow-hidden shadow-xl">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs sm:text-sm text-slate-300">
                    <thead class="bg-slate-950/60 text-slate-400 uppercase text-[11px] font-bold tracking-wider border-b border-slate-800">
                        <tr>
                            <th class="px-6 py-4">ID</th>
                            <th class="px-6 py-4">Opção Sorteada</th>
                            <th class="px-6 py-4">Roleta</th>
                            <th class="px-6 py-4">Usuário</th>
                            <th class="px-6 py-4">Data e Hora</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800">
                        <?php if (empty($sorteios)): ?>
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-slate-500">
                                    Nenhum sorteio registrado até o momento.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($sorteios as $s): ?>
                                <tr class="hover:bg-slate-800/40 transition">
                                    <td class="px-6 py-4 font-mono text-slate-500 text-xs">#<?= $s['id'] ?></td>
                                    <td class="px-6 py-4 font-bold text-white">
                                        <span class="inline-flex items-center gap-2">
                                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-400"></span>
                                            <?= htmlspecialchars($s['item_sorteado']) ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-indigo-300">
                                        <?= htmlspecialchars($s['roleta_titulo'] ?? 'Giro Avulso / Padrão') ?>
                                    </td>
                                    <td class="px-6 py-4 text-slate-300">
                                        <?= htmlspecialchars($s['usuario_nome'] ?? 'Visitante Anônimo') ?>
                                    </td>
                                    <td class="px-6 py-4 text-slate-400 text-xs">
                                        <?= date('d/m/Y H:i:s', strtotime($s['criado_em'])) ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    <!-- Conteúdo da Aba 2: Logs de Auditoria -->
    <?php else: ?>
        <div class="bg-slate-900/80 border border-slate-800 rounded-3xl overflow-hidden shadow-xl">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs sm:text-sm text-slate-300">
                    <thead class="bg-slate-950/60 text-slate-400 uppercase text-[11px] font-bold tracking-wider border-b border-slate-800">
                        <tr>
                            <th class="px-6 py-4">ID</th>
                            <th class="px-6 py-4">Ação</th>
                            <th class="px-6 py-4">Usuário</th>
                            <th class="px-6 py-4">IP</th>
                            <th class="px-6 py-4">Detalhes da Alteração</th>
                            <th class="px-6 py-4">Data e Hora</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800 font-mono text-xs">
                        <?php if (empty($logsAuditoria)): ?>
                            <tr>
                                <td colspan="6" class="px-6 py-10 text-center text-slate-500 font-sans">
                                    Nenhum log de auditoria encontrado.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($logsAuditoria as $log): ?>
                                <tr class="hover:bg-slate-800/40 transition">
                                    <td class="px-6 py-4 text-slate-500">#<?= $log['id'] ?></td>
                                    <td class="px-6 py-4">
                                        <?php
                                            $badgeCor = 'bg-slate-800 text-slate-300 border-slate-700';
                                            if (strpos($log['acao'], 'CRIAR') !== false || strpos($log['acao'], 'CADASTRO') !== false) {
                                                $badgeCor = 'bg-emerald-950 text-emerald-300 border-emerald-700/60';
                                            } elseif (strpos($log['acao'], 'EXCLUIR') !== false || strpos($log['acao'], 'FALHA') !== false) {
                                                $badgeCor = 'bg-rose-950 text-rose-300 border-rose-700/60';
                                            } elseif (strpos($log['acao'], 'ALTERACAO') !== false || strpos($log['acao'], 'EDITAR') !== false) {
                                                $badgeCor = 'bg-amber-950 text-amber-300 border-amber-700/60';
                                            } elseif (strpos($log['acao'], 'LOGIN') !== false) {
                                                $badgeCor = 'bg-indigo-950 text-indigo-300 border-indigo-700/60';
                                            }
                                        ?>
                                        <span class="inline-block px-2.5 py-1 rounded-md text-[11px] font-bold border <?= $badgeCor ?>">
                                            <?= htmlspecialchars($log['acao']) ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-slate-300 font-sans">
                                        <?= htmlspecialchars($log['usuario_nome'] ?? 'Sistema') ?>
                                    </td>
                                    <td class="px-6 py-4 text-slate-400">
                                        <?= htmlspecialchars($log['ip'] ?? '-') ?>
                                    </td>
                                    <td class="px-6 py-4 text-slate-300 font-sans max-w-xs break-words">
                                        <?= htmlspecialchars($log['detalhes'] ?? '-') ?>
                                    </td>
                                    <td class="px-6 py-4 text-slate-400 whitespace-nowrap">
                                        <?= date('d/m/Y H:i:s', strtotime($log['criado_em'])) ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>

    <!-- Anúncios inferiores se plano gratuito -->
    <?php require __DIR__ . '/views/anuncios_bottom.php'; ?>
</main>

<?php require_once __DIR__ . '/views/footer.php'; ?>
