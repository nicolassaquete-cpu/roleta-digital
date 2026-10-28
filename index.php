<?php
/**
 * Página Principal: Roleta Digital Interativa
 * MobileFirst estilizado com Tailwind CSS
 */
require_once __DIR__ . '/helpers/auth_helper.php';
require_once __DIR__ . '/helpers/logger.php';
require_once __DIR__ . '/bd.php';

$usuario = obterUsuarioLogado();
$limiteItens = limiteItensRoleta();
$tituloPagina = 'Roleta Interativa';

// Busca lista de roletas salvas do usuário para o dropdown de troca rápida se estiver logado
$roletasDoUsuario = [];
if (estaAutenticado()) {
    try {
        $stmt = $pdo->prepare("SELECT id, titulo FROM roletas WHERE usuario_id = :uid ORDER BY id DESC");
        $stmt->execute([':uid' => $usuario['id']]);
        $roletasDoUsuario = $stmt->fetchAll();
    } catch (Exception $e) {}
}

require_once __DIR__ . '/views/header.php';
?>

<main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6">
    
    <!-- Topo da Roleta com Título e Ações Rápidas -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <div class="flex items-center gap-2">
                <h1 id="roleta-titulo" class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">O que comer hoje?</h1>
                <span id="item-count-badge" class="text-xs font-bold px-2 py-0.5 rounded bg-slate-800 text-slate-300 border border-slate-700">
                    8 / <?= $limiteItens >= 999999 ? '∞' : $limiteItens ?>
                </span>
            </div>
            <p class="text-xs sm:text-sm text-slate-400 mt-1">Gire a roleta e deixe o destino decidir por você!</p>
        </div>

        <!-- Barra de Ações: Presets e Salvar -->
        <div class="flex flex-wrap items-center gap-2">
            <!-- Dropdown ou Botões de Modelos Rápidos -->
            <button type="button" data-preset="comida" class="px-3 py-1.5 rounded-xl bg-slate-800/80 hover:bg-slate-700 border border-slate-700/80 text-xs font-medium text-slate-300 transition">
                🍕 Comida
            </button>
            <button type="button" data-preset="decisao" class="px-3 py-1.5 rounded-xl bg-slate-800/80 hover:bg-slate-700 border border-slate-700/80 text-xs font-medium text-slate-300 transition">
                ⚡ Sim ou Não
            </button>
            <button type="button" data-preset="numeros" class="px-3 py-1.5 rounded-xl bg-slate-800/80 hover:bg-slate-700 border border-slate-700/80 text-xs font-medium text-slate-300 transition">
                🔢 1 a 10
            </button>

            <?php if (estaAutenticado()): ?>
                <button type="button" id="btn-salvar-roleta" class="px-3.5 py-1.5 rounded-xl bg-indigo-600/20 hover:bg-indigo-600/30 border border-indigo-500/40 text-indigo-300 text-xs font-bold transition flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                    Salvar Roleta
                </button>
            <?php else: ?>
                <a href="login.php" class="px-3.5 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 border border-slate-700 text-slate-300 text-xs font-semibold transition" title="Faça login para salvar">
                    Salvar na Conta
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Área Principal em Grid: Roleta + Barra Lateral de Itens + Banner Lateral -->
    <div class="flex flex-col xl:flex-row gap-6 items-start">
        
        <!-- Coluna Central: Roleta Canvas e Botão Girar -->
        <section class="flex-1 w-full flex flex-col items-center justify-center bg-slate-900/60 border border-slate-800/80 rounded-3xl p-4 sm:p-8 backdrop-blur-md shadow-2xl relative">
            
            <!-- Container do Canvas com Ponteiro Fixo -->
            <div class="relative w-full max-w-[420px] aspect-square flex items-center justify-center my-4">
                <!-- Ponteiro Indicador Superior -->
                <div class="wheel-pointer"></div>

                <!-- Canvas da Roleta com Glow -->
                <canvas id="wheel-canvas" class="wheel-glow w-full h-full cursor-pointer max-w-[420px] max-h-[420px]"></canvas>
            </div>

            <!-- Botão Gigante de Girar Roleta -->
            <div class="w-full max-w-sm mt-4 text-center">
                <button id="btn-spin" type="button" 
                    class="w-full py-4 px-8 rounded-2xl font-black text-lg sm:text-xl uppercase tracking-wider text-white bg-gradient-to-r from-indigo-600 via-indigo-500 to-purple-600 hover:from-indigo-500 hover:to-purple-500 active:scale-95 transition-all shadow-xl shadow-indigo-600/40 border border-indigo-400/30 flex items-center justify-center gap-3 disabled:opacity-50 disabled:cursor-not-allowed">
                    <svg class="w-7 h-7 animate-spin" style="animation-duration: 3s;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    GIRAR ROLETA!
                </button>
            </div>
        </section>

        <!-- Coluna de Gerenciamento de Itens -->
        <section class="w-full xl:w-80 shrink-0 bg-slate-900/80 border border-slate-800 rounded-3xl p-5 shadow-xl flex flex-col">
            <div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-800">
                <h2 class="text-sm font-bold uppercase tracking-wider text-slate-200 flex items-center gap-2">
                    <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                    Opções da Roleta
                </h2>
                <div class="flex items-center gap-1">
                    <button id="btn-shuffle" type="button" class="p-1.5 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 transition" title="Embaralhar opções">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                    </button>
                    <button id="btn-limpar-itens" type="button" class="p-1.5 rounded-lg text-slate-400 hover:text-rose-400 hover:bg-rose-950/30 transition" title="Limpar tudo">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </div>
            </div>

            <!-- Formulário de Adicionar Novo Item -->
            <form id="form-add-item" class="flex gap-2 mb-4">
                <input type="text" id="input-item-text" placeholder="Nova opção..." maxlength="60"
                    class="flex-1 px-3.5 py-2 bg-slate-950/70 border border-slate-700 rounded-xl text-white text-xs sm:text-sm focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition">
                <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl font-bold text-xs transition shadow-md shadow-indigo-600/30">
                    +
                </button>
            </form>

            <button type="button" id="btn-import-massa" class="w-full py-1.5 mb-3 rounded-lg border border-slate-800 hover:border-slate-700 bg-slate-950/40 text-[11px] font-semibold text-slate-400 hover:text-slate-200 transition">
                + Importar Vários Itens em Massa
            </button>

            <!-- Lista de Itens com Scroll -->
            <div id="item-list" class="space-y-1.5 max-h-[360px] overflow-y-auto pr-1"></div>
            
            <?php if (!ehPremium()): ?>
                <div class="mt-4 pt-3 border-t border-slate-800 text-[11px] text-slate-400 text-center">
                    Plano Gratuito: até 50 itens.
                    <a href="planos.php" class="text-indigo-400 font-bold hover:underline">Seja Premium</a> para itens ilimitados!
                </div>
            <?php endif; ?>
        </section>

        <!-- Anúncio Lateral (Renderizado somente para Plano Gratuito) -->
        <?php require __DIR__ . '/views/anuncios_lateral.php'; ?>

    </div>

    <!-- Anúncio Inferior (Renderizado somente para Plano Gratuito) -->
    <?php require __DIR__ . '/views/anuncios_bottom.php'; ?>

</main>

<!-- Scripts da Roleta -->
<script src="assets/js/wheel.js"></script>
<script src="assets/js/app.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        initRoletaApp({
            items: PRESETS.comida,
            maxLimit: <?= (int)$limiteItens ?>,
            roletaId: null
        });
    });
</script>

<?php require_once __DIR__ . '/views/footer.php'; ?>
