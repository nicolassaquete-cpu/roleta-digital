<?php
/**
 * Visualização e Execução de Roleta Salva por ID
 * Carrega dados via PDO Prepared Statement
 */
require_once __DIR__ . '/helpers/auth_helper.php';
require_once __DIR__ . '/helpers/logger.php';
require_once __DIR__ . '/bd.php';

$roletaId = !empty($_GET['id']) ? (int)$_GET['id'] : 0;
$usuario = obterUsuarioLogado();
$limiteItens = limiteItensRoleta();

if (!$roletaId) {
    definirMensagemFlash('warning', 'Roleta não especificada.');
    header('Location: index.php');
    exit;
}

try {
    // Busca informações da roleta
    $stmtRoleta = $pdo->prepare("SELECT r.*, u.nome as autor_nome FROM roletas r LEFT JOIN usuarios u ON r.usuario_id = u.id WHERE r.id = :id");
    $stmtRoleta->execute([':id' => $roletaId]);
    $roleta = $stmtRoleta->fetch();

    if (!$roleta) {
        definirMensagemFlash('error', 'Roleta não encontrada.');
        header('Location: index.php');
        exit;
    }

    // Busca itens da roleta via PDO
    $stmtItens = $pdo->prepare("SELECT texto, cor, peso FROM itens_roleta WHERE roleta_id = :id ORDER BY id ASC");
    $stmtItens->execute([':id' => $roletaId]);
    $itensCarregados = $stmtItens->fetchAll();

    // Se a roleta não tiver itens cadastrados, define padrão
    if (empty($itensCarregados)) {
        $itensCarregados = [
            ['texto' => 'Opção 1', 'cor' => '#ef4444'],
            ['texto' => 'Opção 2', 'cor' => '#3b82f6']
        ];
    }
} catch (Exception $e) {
    definirMensagemFlash('error', 'Erro ao carregar roleta: ' . $e->getMessage());
    header('Location: index.php');
    exit;
}

$ehDono = $usuario && ((int)$roleta['usuario_id'] === (int)$usuario['id']);
$tituloPagina = $roleta['titulo'];
require_once __DIR__ . '/views/header.php';
?>

<main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6">
    
    <!-- Topo da Roleta com Título e Ações -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <div class="flex items-center gap-2">
                <a href="roletas.php" class="p-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 transition" title="Voltar para Minhas Roletas">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                </a>
                <h1 id="roleta-titulo" class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight"><?= htmlspecialchars($roleta['titulo']) ?></h1>
                <span id="item-count-badge" class="text-xs font-bold px-2 py-0.5 rounded bg-slate-800 text-slate-300 border border-slate-700">
                    <?= count($itensCarregados) ?> / <?= $limiteItens >= 999999 ? '∞' : $limiteItens ?>
                </span>
            </div>
            <p class="text-xs sm:text-sm text-slate-400 mt-1">
                Criado por <?= htmlspecialchars($roleta['autor_nome'] ?? 'Anônimo') ?> em <?= date('d/m/Y', strtotime($roleta['criado_em'])) ?>
            </p>
        </div>

        <div class="flex items-center gap-2">
            <?php if ($ehDono): ?>
                <button type="button" id="btn-salvar-roleta" class="px-3.5 py-1.5 rounded-xl bg-indigo-600/20 hover:bg-indigo-600/30 border border-indigo-500/40 text-indigo-300 text-xs font-bold transition flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                    Salvar Alterações
                </button>
            <?php endif; ?>
        </div>
    </div>

    <!-- Layout da Roleta -->
    <div class="flex flex-col xl:flex-row gap-6 items-start">
        
        <!-- Coluna da Roleta -->
        <section class="flex-1 w-full flex flex-col items-center justify-center bg-slate-900/60 border border-slate-800/80 rounded-3xl p-4 sm:p-8 backdrop-blur-md shadow-2xl relative">
            <div class="relative w-full max-w-[420px] aspect-square flex items-center justify-center my-4">
                <div class="wheel-pointer"></div>
                <canvas id="wheel-canvas" class="wheel-glow w-full h-full cursor-pointer max-w-[420px] max-h-[420px]"></canvas>
            </div>

            <div class="w-full max-w-sm mt-4 text-center">
                <button id="btn-spin" type="button" 
                    class="w-full py-4 px-8 rounded-2xl font-black text-lg sm:text-xl uppercase tracking-wider text-white bg-gradient-to-r from-indigo-600 via-indigo-500 to-purple-600 hover:from-indigo-500 hover:to-purple-500 active:scale-95 transition-all shadow-xl shadow-indigo-600/40 border border-indigo-400/30 flex items-center justify-center gap-3 disabled:opacity-50">
                    <svg class="w-7 h-7 animate-spin" style="animation-duration: 3s;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    GIRAR ROLETA!
                </button>
            </div>
        </section>

        <!-- Coluna de Gerenciamento de Itens -->
        <section class="w-full xl:w-80 shrink-0 bg-slate-900/80 border border-slate-800 rounded-3xl p-5 shadow-xl flex flex-col">
            <div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-800">
                <h2 class="text-sm font-bold uppercase tracking-wider text-slate-200">Opções</h2>
                <div class="flex items-center gap-1">
                    <button id="btn-shuffle" type="button" class="p-1.5 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 transition" title="Embaralhar">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                    </button>
                    <?php if ($ehDono): ?>
                        <button id="btn-limpar-itens" type="button" class="p-1.5 rounded-lg text-slate-400 hover:text-rose-400 hover:bg-rose-950/30 transition" title="Limpar tudo">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    <?php endif; ?>
                </div>
            </div>

            <?php if ($ehDono): ?>
                <form id="form-add-item" class="flex gap-2 mb-4">
                    <input type="text" id="input-item-text" placeholder="Nova opção..." maxlength="60"
                        class="flex-1 px-3.5 py-2 bg-slate-950/70 border border-slate-700 rounded-xl text-white text-xs sm:text-sm focus:outline-none focus:border-indigo-500">
                    <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl font-bold text-xs">
                        +
                    </button>
                </form>
            <?php endif; ?>

            <div id="item-list" class="space-y-1.5 max-h-[380px] overflow-y-auto pr-1"></div>
        </section>

        <!-- Anúncio Lateral (Apenas se Plano Gratuito) -->
        <?php require __DIR__ . '/views/anuncios_lateral.php'; ?>

    </div>

    <!-- Anúncio Inferior (Apenas se Plano Gratuito) -->
    <?php require __DIR__ . '/views/anuncios_bottom.php'; ?>

</main>

<script src="assets/js/wheel.js"></script>
<script src="assets/js/app.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        initRoletaApp({
            items: <?= json_encode($itensCarregados) ?>,
            maxLimit: <?= (int)$limiteItens ?>,
            roletaId: <?= (int)$roletaId ?>
        });
    });
</script>

<?php require_once __DIR__ . '/views/footer.php'; ?>
