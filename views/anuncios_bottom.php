<?php
/**
 * Banner de Anúncio Inferior
 * Requisito: "o plano gratuito oferece no máximo 50 itens na roleta e anúncios na lateral e inferior do site"
 * "o plano premium oferece itens ilimitados na roleta e remove os anúncios do site"
 */
if (ehPremium()) {
    return; // Usuário Premium não vê anúncios!
}
?>
<!-- Banner Inferior de Anúncio (Apenas Plano Gratuito) -->
<section class="w-full max-w-5xl mx-auto px-4 mt-8 mb-4">
    <div class="relative overflow-hidden bg-gradient-to-r from-slate-900 via-indigo-950/60 to-slate-900 border border-slate-800 rounded-2xl p-4 sm:p-5 shadow-lg flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-4 text-center sm:text-left">
            <div class="hidden sm:flex items-center justify-center w-12 h-12 rounded-xl bg-amber-500/20 text-amber-400 border border-amber-500/30 shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
            </div>
            <div>
                <div class="flex items-center justify-center sm:justify-start gap-2">
                    <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded bg-slate-800 text-slate-400 border border-slate-700">Anúncio</span>
                    <h4 class="text-sm font-bold text-white">Domine o Desenvolvimento Web com PHP & Tailwind</h4>
                </div>
                <p class="text-xs text-slate-400 mt-1">Cursos práticos e projetos reais do zero ao avançado. Aproveite 40% OFF hoje.</p>
            </div>
        </div>
        <div class="flex items-center gap-2 shrink-0">
            <a href="planos.php" class="text-xs text-slate-400 hover:text-slate-200 underline pr-2 hidden md:inline">
                Remover Anúncios
            </a>
            <a href="planos.php" class="px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-semibold text-xs transition shadow-md shadow-indigo-600/30 flex items-center gap-1.5 whitespace-nowrap">
                <span>Virar Premium</span>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>
    </div>
</section>
