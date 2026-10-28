<?php
/**
 * Componente de Toasts (Notificações Flutuantes)
 * Requisito: "Retorne sempre as mensagens de erros de forma claras na interface para o usuário (TOAST)"
 */
$mensagensFlash = obterMensagensFlash();
?>
<!-- Toast Container Flutuante (Fixo, Responsivo MobileFirst) -->
<div id="toast-container" class="fixed top-4 right-4 z-50 flex flex-col gap-2.5 max-w-sm w-full px-4 pointer-events-none sm:px-0"></div>

<script>
/**
 * Dispara uma notificação TOAST estilizada com Tailwind CSS.
 * @param {string} mensagem Texto da notificação
 * @param {'success'|'error'|'warning'|'info'} tipo Tipo visual do toast
 * @param {number} duracao Tempo em milissegundos antes de desaparecer
 */
function showToast(mensagem, tipo = 'info', duracao = 4500) {
    const container = document.getElementById('toast-container');
    if (!container) return;

    const toast = document.createElement('div');
    toast.className = 'pointer-events-auto transform translate-y-2 opacity-0 transition-all duration-300 ease-out flex items-start gap-3 p-4 rounded-xl shadow-xl border backdrop-blur-md text-sm font-medium';

    // Cores e ícones conforme o tipo
    let estilos = '';
    let iconSvg = '';

    switch (tipo) {
        case 'success':
            estilos = 'bg-emerald-950/90 border-emerald-500/40 text-emerald-200 shadow-emerald-950/50';
            iconSvg = `<svg class="w-5 h-5 text-emerald-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>`;
            break;
        case 'error':
            estilos = 'bg-rose-950/90 border-rose-500/40 text-rose-200 shadow-rose-950/50';
            iconSvg = `<svg class="w-5 h-5 text-rose-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>`;
            break;
        case 'warning':
            estilos = 'bg-amber-950/90 border-amber-500/40 text-amber-200 shadow-amber-950/50';
            iconSvg = `<svg class="w-5 h-5 text-amber-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>`;
            break;
        case 'info':
        default:
            estilos = 'bg-indigo-950/90 border-indigo-500/40 text-indigo-200 shadow-indigo-950/50';
            iconSvg = `<svg class="w-5 h-5 text-indigo-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>`;
            break;
    }

    toast.className += ' ' + estilos;
    toast.innerHTML = `
        ${iconSvg}
        <div class="flex-1 break-words">${mensagem}</div>
        <button type="button" class="text-slate-400 hover:text-white transition p-1" onclick="this.parentElement.remove()">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    `;

    container.appendChild(toast);

    // Animação de entrada
    requestAnimationFrame(() => {
        toast.classList.remove('translate-y-2', 'opacity-0');
        toast.classList.add('translate-y-0', 'opacity-100');
    });

    // Desaparecimento automático
    setTimeout(() => {
        toast.classList.add('opacity-0', 'scale-95');
        setTimeout(() => toast.remove(), 300);
    }, duracao);
}

// Dispara mensagens que vieram do servidor PHP via Sessão
document.addEventListener('DOMContentLoaded', () => {
    <?php if (!empty($mensagensFlash)): ?>
        <?php foreach ($mensagensFlash as $flash): ?>
            showToast(<?= json_encode($flash['mensagem']) ?>, <?= json_encode($flash['tipo']) ?>);
        <?php endforeach; ?>
    <?php endif; ?>
});
</script>
