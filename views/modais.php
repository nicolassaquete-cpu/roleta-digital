<?php
/**
 * Componentes Modais Dinâmicos
 * Requisito Global: "sempre trate as mensagens de caixa de mensagens nativas do navegador em um MODAL"
 * Substitui os nativos: window.alert, window.confirm, window.prompt
 */
?>
<!-- 1. Modal Geral de Alerta / Informação (Substitui window.alert) -->
<div id="modal-alert" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-slate-950/75 backdrop-blur-sm transition-opacity duration-200">
    <div class="bg-slate-900 border border-slate-700/80 rounded-2xl max-w-md w-full p-6 shadow-2xl transform scale-95 transition-transform duration-200">
        <div class="flex items-start gap-4">
            <div id="modal-alert-icon" class="p-3 bg-indigo-600/20 text-indigo-400 rounded-xl border border-indigo-500/30 shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div class="flex-1">
                <h3 id="modal-alert-title" class="text-lg font-bold text-white">Atenção</h3>
                <p id="modal-alert-message" class="text-slate-300 text-sm mt-1.5 leading-relaxed break-words"></p>
            </div>
        </div>
        <div class="mt-6 flex justify-end">
            <button id="modal-alert-btn-ok" type="button" class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-semibold text-sm transition shadow-lg shadow-indigo-600/30 active:scale-95">
                OK
            </button>
        </div>
    </div>
</div>

<!-- 2. Modal de Confirmação (Substitui window.confirm) -->
<div id="modal-confirm" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-slate-950/75 backdrop-blur-sm transition-opacity duration-200">
    <div class="bg-slate-900 border border-slate-700/80 rounded-2xl max-w-md w-full p-6 shadow-2xl transform scale-95 transition-transform duration-200">
        <div class="flex items-start gap-4">
            <div class="p-3 bg-amber-600/20 text-amber-400 rounded-xl border border-amber-500/30 shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <div class="flex-1">
                <h3 id="modal-confirm-title" class="text-lg font-bold text-white">Confirmação</h3>
                <p id="modal-confirm-message" class="text-slate-300 text-sm mt-1.5 leading-relaxed break-words"></p>
            </div>
        </div>
        <div class="mt-6 flex items-center justify-end gap-3">
            <button id="modal-confirm-btn-cancel" type="button" class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-medium text-sm transition border border-slate-700">
                Cancelar
            </button>
            <button id="modal-confirm-btn-ok" type="button" class="px-5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-500 text-white font-semibold text-sm transition shadow-lg shadow-rose-600/30 active:scale-95">
                Confirmar
            </button>
        </div>
    </div>
</div>

<!-- 3. Modal de Entrada de Texto (Substitui window.prompt) -->
<div id="modal-prompt" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-slate-950/75 backdrop-blur-sm transition-opacity duration-200">
    <div class="bg-slate-900 border border-slate-700/80 rounded-2xl max-w-md w-full p-6 shadow-2xl transform scale-95 transition-transform duration-200">
        <div class="flex items-start gap-4">
            <div class="p-3 bg-indigo-600/20 text-indigo-400 rounded-xl border border-indigo-500/30 shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            </div>
            <div class="flex-1">
                <h3 id="modal-prompt-title" class="text-lg font-bold text-white">Preencha o campo</h3>
                <p id="modal-prompt-message" class="text-slate-300 text-sm mt-1.5 leading-relaxed break-words"></p>
                <div class="mt-4">
                    <input id="modal-prompt-input" type="text" class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-white text-sm focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500" />
                </div>
            </div>
        </div>
        <div class="mt-6 flex items-center justify-end gap-3">
            <button id="modal-prompt-btn-cancel" type="button" class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-medium text-sm transition border border-slate-700">
                Cancelar
            </button>
            <button id="modal-prompt-btn-ok" type="button" class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-semibold text-sm transition shadow-lg shadow-indigo-600/30 active:scale-95">
                Salvar
            </button>
        </div>
    </div>
</div>

<!-- 4. Modal de Resultado do Sorteio (Vencedor da Roleta) -->
<div id="modal-vencedor" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-md transition-opacity duration-300">
    <div class="bg-gradient-to-b from-slate-900 to-indigo-950/80 border border-indigo-500/50 rounded-3xl max-w-md w-full p-8 shadow-2xl text-center transform scale-90 transition-transform duration-300 relative overflow-hidden">
        <!-- Brilho estético -->
        <div class="absolute -top-16 -left-16 w-32 h-32 bg-indigo-500/20 rounded-full blur-2xl pointer-events-none"></div>
        <div class="absolute -bottom-16 -right-16 w-32 h-32 bg-purple-500/20 rounded-full blur-2xl pointer-events-none"></div>

        <div class="inline-flex items-center justify-center w-20 h-20 rounded-2xl bg-gradient-to-tr from-amber-500 to-yellow-300 text-slate-950 mb-4 shadow-xl shadow-amber-500/30 ring-4 ring-amber-400/20 animate-bounce">
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
        </div>

        <h3 class="text-xs uppercase tracking-widest font-bold text-amber-400 mb-1">Opção Selecionada!</h3>
        <p class="text-slate-400 text-sm">A roleta parou no seguinte resultado:</p>

        <div class="my-6 p-5 rounded-2xl bg-slate-800/90 border border-slate-700/80 shadow-inner">
            <span id="modal-vencedor-texto" class="text-2xl sm:text-3xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-white via-indigo-200 to-indigo-400 break-words">
                ---
            </span>
        </div>

        <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
            <button id="modal-vencedor-btn-fechar" type="button" class="w-full sm:w-auto px-6 py-3 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 font-semibold text-sm transition border border-slate-700">
                Fechar
            </button>
            <button id="modal-vencedor-btn-girar" type="button" class="w-full sm:w-auto px-6 py-3 rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 text-white font-bold text-sm transition shadow-lg shadow-indigo-600/40">
                Girar Novamente
            </button>
        </div>
    </div>
</div>
