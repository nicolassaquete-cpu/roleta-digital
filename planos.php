<?php
/**
 * Página de Comparação de Planos (Gratuito x Premium)
 * Requisitos:
 * - Plano Gratuito: max 50 itens + anúncios na lateral e inferior
 * - Plano Premium: itens ilimitados + sem anúncios
 */
require_once __DIR__ . '/helpers/auth_helper.php';
require_once __DIR__ . '/helpers/logger.php';
require_once __DIR__ . '/bd.php';

$usuario = obterUsuarioLogado();
$planoAtual = $usuario ? strtolower($usuario['plano']) : 'gratuito';

$tituloPagina = 'Planos e Preços';
require_once __DIR__ . '/views/header.php';
?>

<main class="flex-1 max-w-6xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-10">
    
    <div class="text-center max-w-2xl mx-auto mb-12">
        <span class="text-xs font-bold uppercase tracking-widest text-indigo-400 px-3 py-1 rounded-full bg-indigo-500/10 border border-indigo-500/20">
            Escolha seu Plano
        </span>
        <h1 class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight mt-3">
            Simples, Transparente e Sem Surpresas
        </h1>
        <p class="text-slate-400 text-sm sm:text-base mt-2">
            Aproveite todas as vantagens do Plano Premium ou use a versão gratuita com até 50 itens.
        </p>
    </div>

    <!-- Cards de Comparação dos Planos -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-4xl mx-auto items-stretch">
        
        <!-- 1. Plano Gratuito -->
        <div class="bg-slate-900/80 border <?= $planoAtual === 'gratuito' ? 'border-indigo-500/60 ring-2 ring-indigo-500/20' : 'border-slate-800' ?> rounded-3xl p-8 flex flex-col justify-between shadow-xl relative backdrop-blur-md">
            <?php if ($planoAtual === 'gratuito'): ?>
                <span class="absolute -top-3.5 left-1/2 -translate-x-1/2 px-3 py-0.5 rounded-full bg-indigo-600 text-white text-[11px] font-bold uppercase tracking-wider shadow">
                    Seu Plano Atual
                </span>
            <?php endif; ?>

            <div>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-bold text-white">Plano Gratuito</h3>
                    <span class="text-xs font-semibold px-2.5 py-1 rounded-lg bg-slate-800 text-slate-300 border border-slate-700">Básico</span>
                </div>
                <div class="mb-6">
                    <span class="text-4xl font-extrabold text-white">R$ 0</span>
                    <span class="text-xs text-slate-400">/ sempre grátis</span>
                </div>

                <ul class="space-y-3.5 text-sm text-slate-300 mb-8">
                    <li class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-indigo-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>Até <strong>50 itens</strong> por roleta</span>
                    </li>
                    <li class="flex items-center gap-3 text-slate-400">
                        <svg class="w-5 h-5 text-amber-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <span>Contém <strong>anúncios</strong> na lateral e rodapé</span>
                    </li>
                    <li class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-indigo-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>Efeitos sonoros e celebração</span>
                    </li>
                    <li class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-indigo-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>Histórico de giros e sorteios</span>
                    </li>
                    <li class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-indigo-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>Salvar roletas personalizadas</span>
                    </li>
                </ul>
            </div>

            <?php if (estaAutenticado()): ?>
                <?php if ($planoAtual === 'gratuito'): ?>
                    <button type="button" disabled class="w-full py-3 px-4 rounded-xl font-bold text-slate-400 bg-slate-800/80 cursor-default text-sm">
                        Plano Atual Ativo
                    </button>
                <?php else: ?>
                    <form action="api/alterar_plano.php" method="POST">
                        <input type="hidden" name="plano" value="gratuito">
                        <button type="submit" class="w-full py-3 px-4 rounded-xl font-bold text-slate-300 bg-slate-800 hover:bg-slate-700 border border-slate-700 text-sm transition">
                            Voltar para Plano Gratuito
                        </button>
                    </form>
                <?php endif; ?>
            <?php else: ?>
                <a href="cadastro.php" class="block text-center py-3 px-4 rounded-xl font-bold text-white bg-slate-800 hover:bg-slate-700 border border-slate-700 text-sm transition">
                    Criar Conta Gratuita
                </a>
            <?php endif; ?>
        </div>

        <!-- 2. Plano Premium VIP -->
        <div class="bg-gradient-to-b from-slate-900 to-indigo-950/70 border <?= $planoAtual === 'premium' ? 'border-amber-500/80 ring-2 ring-amber-500/30' : 'border-indigo-500/50' ?> rounded-3xl p-8 flex flex-col justify-between shadow-2xl relative backdrop-blur-md">
            
            <span class="absolute -top-3.5 right-6 px-3 py-0.5 rounded-full bg-gradient-to-r from-amber-500 to-yellow-400 text-slate-950 text-[11px] font-black uppercase tracking-wider shadow-lg">
                Recomendado
            </span>

            <?php if ($planoAtual === 'premium'): ?>
                <span class="absolute -top-3.5 left-6 px-3 py-0.5 rounded-full bg-amber-500 text-slate-950 text-[11px] font-bold uppercase tracking-wider shadow">
                    Seu Plano Ativo
                </span>
            <?php endif; ?>

            <div>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-bold text-white flex items-center gap-2">
                        <span>Plano Premium VIP</span>
                        <svg class="w-5 h-5 text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    </h3>
                </div>
                <div class="mb-6">
                    <span class="text-4xl font-black text-transparent bg-clip-text bg-gradient-to-r from-white via-amber-200 to-yellow-400">R$ 14,90</span>
                    <span class="text-xs text-slate-300">/ mês ou teste grátis</span>
                </div>

                <ul class="space-y-3.5 text-sm text-slate-200 mb-8">
                    <li class="flex items-center gap-3 font-semibold text-amber-300">
                        <svg class="w-5 h-5 text-amber-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        <span>Itens ILIMITADOS na roleta</span>
                    </li>
                    <li class="flex items-center gap-3 font-semibold text-emerald-300">
                        <svg class="w-5 h-5 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        <span>100% ZERO Anúncios (Sem publicidade)</span>
                    </li>
                    <li class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-indigo-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>Painel exclusivo sem distrações</span>
                    </li>
                    <li class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-indigo-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>Suporte prioritário e novidades antecipadas</span>
                    </li>
                    <li class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-indigo-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>Histórico e auditoria sem expiração</span>
                    </li>
                </ul>
            </div>

            <?php if (estaAutenticado()): ?>
                <?php if ($planoAtual === 'premium'): ?>
                    <button type="button" disabled class="w-full py-3.5 px-4 rounded-xl font-bold text-amber-300 bg-amber-500/20 border border-amber-500/30 cursor-default text-sm flex items-center justify-center gap-2">
                        <svg class="w-5 h-5 text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        Você Já é Premium!
                    </button>
                <?php else: ?>
                    <form action="api/alterar_plano.php" method="POST">
                        <input type="hidden" name="plano" value="premium">
                        <button type="submit" class="w-full py-3.5 px-4 rounded-xl font-bold text-slate-950 bg-gradient-to-r from-amber-400 via-amber-300 to-yellow-400 hover:from-amber-300 hover:to-yellow-300 active:scale-[0.98] transition shadow-lg shadow-amber-500/30 text-sm flex items-center justify-center gap-2">
                            <span>Ativar Plano Premium Agora</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                        </button>
                    </form>
                <?php endif; ?>
            <?php else: ?>
                <a href="login.php" class="block text-center py-3.5 px-4 rounded-xl font-bold text-slate-950 bg-gradient-to-r from-amber-400 to-yellow-400 hover:from-amber-300 hover:to-yellow-300 text-sm transition shadow-lg shadow-amber-500/30">
                    Entrar para Virar Premium
                </a>
            <?php endif; ?>
        </div>

    </div>

    <!-- Anúncio se gratuito -->
    <?php require __DIR__ . '/views/anuncios_bottom.php'; ?>
</main>

<?php require_once __DIR__ . '/views/footer.php'; ?>
