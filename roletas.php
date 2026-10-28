<?php
/**
 * Gestão de Roletas do Usuário (Minhas Roletas)
 */
require_once __DIR__ . '/helpers/auth_helper.php';
require_once __DIR__ . '/helpers/logger.php';
require_once __DIR__ . '/bd.php';

exigirAutenticacao();

$usuario = obterUsuarioLogado();
$tituloPagina = 'Minhas Roletas';

try {
    // Busca roletas com contagem de itens usando PDO
    $sql = "SELECT r.*, COUNT(i.id) as total_itens 
            FROM roletas r 
            LEFT JOIN itens_roleta i ON r.id = i.roleta_id 
            WHERE r.usuario_id = :uid 
            GROUP BY r.id 
            ORDER BY r.id DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':uid' => $usuario['id']]);
    $roletas = $stmt->fetchAll();
} catch (Exception $e) {
    $roletas = [];
    definirMensagemFlash('error', 'Erro ao carregar roletas: ' . $e->getMessage());
}

require_once __DIR__ . '/views/header.php';
?>

<main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">Minhas Roletas</h1>
            <p class="text-sm text-slate-400 mt-1">Gerencie suas listas e roletas personalizadas</p>
        </div>

        <a href="index.php" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-semibold text-sm transition shadow-lg shadow-indigo-600/30">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Criar Nova Roleta
        </a>
    </div>

    <?php if (empty($roletas)): ?>
        <div class="bg-slate-900/60 border border-slate-800 rounded-3xl p-12 text-center max-w-lg mx-auto">
            <div class="w-16 h-16 rounded-2xl bg-indigo-600/10 text-indigo-400 mx-auto flex items-center justify-center mb-4 border border-indigo-500/20">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
            </div>
            <h3 class="text-lg font-bold text-white mb-1">Nenhuma roleta cadastrada ainda</h3>
            <p class="text-slate-400 text-sm mb-6">Monte sua primeira roleta interativa agora mesmo.</p>
            <a href="index.php" class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-semibold text-sm transition shadow-md">
                Criar Roleta Agora
            </a>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($roletas as $r): ?>
                <div class="bg-slate-900/80 border border-slate-800 rounded-3xl p-6 shadow-xl flex flex-col justify-between hover:border-slate-700 transition group">
                    <div>
                        <div class="flex items-start justify-between gap-3 mb-3">
                            <h3 class="text-lg font-bold text-white group-hover:text-indigo-300 transition line-clamp-1">
                                <?= htmlspecialchars($r['titulo']) ?>
                            </h3>
                            <span class="text-xs font-bold px-2 py-0.5 rounded bg-slate-800 text-indigo-300 border border-slate-700 shrink-0">
                                <?= (int)$r['total_itens'] ?> opções
                            </span>
                        </div>
                        <p class="text-xs text-slate-400 mb-6">
                            Atualizada em <?= date('d/m/Y \à\s H:i', strtotime($r['atualizado_em'])) ?>
                        </p>
                    </div>

                    <div class="flex items-center gap-2 pt-4 border-t border-slate-800/80">
                        <a href="roleta.php?id=<?= $r['id'] ?>" class="flex-1 text-center py-2 px-3 rounded-xl bg-indigo-600/20 hover:bg-indigo-600/30 border border-indigo-500/40 text-indigo-300 font-semibold text-xs transition">
                            Abrir & Girar
                        </a>
                        <button type="button" onclick="confirmarExclusaoRoleta(<?= $r['id'] ?>, <?= json_encode($r['titulo']) ?>)" class="p-2 rounded-xl text-slate-400 hover:text-rose-400 hover:bg-rose-950/30 transition" title="Excluir roleta">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Anúncios se plano gratuito -->
    <?php require __DIR__ . '/views/anuncios_bottom.php'; ?>
</main>

<script>
async function confirmarExclusaoRoleta(id, titulo) {
    // Regra Global: Tratar mensagens de confirmação em MODAL
    const confirmou = await showConfirmModal(`Tem certeza que deseja excluir a roleta "${titulo}"? Todos os itens associados serão removidos.`, 'Excluir Roleta');
    if (!confirmou) return;

    try {
        const resp = await fetch('api/excluir_roleta.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: id })
        });
        const res = await resp.json();
        if (res.sucesso) {
            showToast('Roleta excluída com sucesso!', 'success');
            setTimeout(() => window.location.reload(), 1000);
        } else {
            showToast(res.mensagem || 'Erro ao excluir roleta.', 'error');
        }
    } catch (e) {
        showToast('Erro de comunicação com o servidor.', 'error');
    }
}
</script>

<?php require_once __DIR__ . '/views/footer.php'; ?>
