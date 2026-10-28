<?php
/**
 * Banner de Anúncio Lateral
 * Requisito: "o plano gratuito oferece no máximo 50 itens na roleta e anúncios na lateral e inferior do site"
 * "o plano premium oferece itens ilimitados na roleta e remove os anúncios do site"
 */
if (ehPremium()) {
    return; // Usuário Premium não vê anúncios!
}
?>
<!-- Banner Lateral de Anúncio (Apenas Plano Gratuito) -->
<aside class="hidden xl:flex flex-col gap-4 w-72 shrink-0">
    <div class="bg-gradient-to-br from-slate-900 to-slate-800/90 border border-slate-700/70 rounded-2xl p-4 shadow-xl text-center sticky top-24">
        <div class="flex items-center justify-between pb-2 mb-3 border-b border-slate-800 text-[11px] font-semibold text-slate-400 uppercase tracking-wider">
            <span class="flex items-center gap-1.5">
                <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                Publicidade
            </span>
            <a href="planos.php" class="text-indigo-400 hover:text-indigo-300 font-bold transition">Remover</a>
        </div>

        <!-- Conteúdo do Anúncio Patrocinado 1 -->
        <div class="rounded-xl overflow-hidden bg-slate-950 border border-slate-800 p-4 mb-4 text-left group">
            <span class="text-[10px] uppercase font-bold text-indigo-400 tracking-wider">Patrocinado</span>
            <h4 class="text-sm font-bold text-white mt-1 group-hover:text-indigo-300 transition">Hospedagem Cloud Ultrarrápida</h4>
            <p class="text-xs text-slate-400 mt-1 leading-relaxed">Coloque seu projeto no ar com 99.9% de uptime e discos NVMe.</p>
            <div class="mt-3 inline-block text-xs font-semibold text-indigo-400 group-hover:translate-x-1 transition-transform">
                Saiba mais &rarr;
            </div>
        </div>

        <!-- Banner Chamada para o Premium -->
        <div class="rounded-xl bg-gradient-to-tr from-indigo-950/70 to-purple-950/70 border border-indigo-500/30 p-4 text-left">
            <div class="flex items-center gap-2 mb-2">
                <span class="p-1 rounded-md bg-amber-500/20 text-amber-400 text-xs font-bold">VIP</span>
                <span class="text-xs font-bold text-white">Cansado dos anúncios?</span>
            </div>
            <p class="text-xs text-slate-300 mb-3 leading-relaxed">
                Assine o <strong>Plano Premium</strong> para navegar 100% sem anúncios e cadastrar itens ilimitados na roleta!
            </p>
            <a href="planos.php" class="block text-center py-2 px-3 rounded-lg bg-indigo-600 hover:bg-indigo-500 text-white font-semibold text-xs transition shadow-md shadow-indigo-600/30">
                Fazer Upgrade Premium
            </a>
        </div>
    </div>
</aside>
