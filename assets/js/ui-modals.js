/**
 * Gerenciador de Modais Customizados
 * Requisito: "sempre trate as mensagens de caixa de mensagens nativas do navegador em um MODAL"
 * Substitui os diálogos bloqueantes nativos por modais modernos assíncronos.
 */

// Helper para abrir/fechar classes
function toggleModal(modalId, show = true) {
    const el = document.getElementById(modalId);
    if (!el) return;
    const dialog = el.querySelector('div');

    if (show) {
        el.classList.remove('hidden');
        requestAnimationFrame(() => {
            el.classList.remove('opacity-0');
            el.classList.add('opacity-100');
            if (dialog) {
                dialog.classList.remove('scale-95', 'scale-90');
                dialog.classList.add('scale-100');
            }
        });
    } else {
        el.classList.remove('opacity-100');
        el.classList.add('opacity-0');
        if (dialog) {
            dialog.classList.remove('scale-100');
            dialog.classList.add('scale-95');
        }
        setTimeout(() => el.classList.add('hidden'), 200);
    }
}

/**
 * Exibe um alerta modal estilizado
 * Substitui window.alert
 */
function showAlertModal(mensagem, titulo = 'Aviso') {
    return new Promise((resolve) => {
        const titleEl = document.getElementById('modal-alert-title');
        const msgEl = document.getElementById('modal-alert-message');
        const btnOk = document.getElementById('modal-alert-btn-ok');

        if (titleEl) titleEl.textContent = titulo;
        if (msgEl) msgEl.textContent = mensagem;

        const cleanup = () => {
            btnOk.removeEventListener('click', onOk);
            toggleModal('modal-alert', false);
            resolve();
        };

        const onOk = () => cleanup();
        btnOk.addEventListener('click', onOk, { once: true });

        toggleModal('modal-alert', true);
    });
}

/**
 * Exibe um diálogo modal de confirmação
 * Substitui window.confirm
 */
function showConfirmModal(mensagem, titulo = 'Confirmação') {
    return new Promise((resolve) => {
        const titleEl = document.getElementById('modal-confirm-title');
        const msgEl = document.getElementById('modal-confirm-message');
        const btnOk = document.getElementById('modal-confirm-btn-ok');
        const btnCancel = document.getElementById('modal-confirm-btn-cancel');

        if (titleEl) titleEl.textContent = titulo;
        if (msgEl) msgEl.textContent = mensagem;

        const onOk = () => {
            toggleModal('modal-confirm', false);
            resolve(true);
        };

        const onCancel = () => {
            toggleModal('modal-confirm', false);
            resolve(false);
        };

        btnOk.addEventListener('click', onOk, { once: true });
        btnCancel.addEventListener('click', onCancel, { once: true });

        toggleModal('modal-confirm', true);
    });
}

/**
 * Exibe um prompt modal solicitando texto ao usuário
 * Substitui window.prompt
 */
function showPromptModal(mensagem, valorPadrao = '', titulo = 'Digite uma informação') {
    return new Promise((resolve) => {
        const titleEl = document.getElementById('modal-prompt-title');
        const msgEl = document.getElementById('modal-prompt-message');
        const inputEl = document.getElementById('modal-prompt-input');
        const btnOk = document.getElementById('modal-prompt-btn-ok');
        const btnCancel = document.getElementById('modal-prompt-btn-cancel');

        if (titleEl) titleEl.textContent = titulo;
        if (msgEl) msgEl.textContent = mensagem;
        if (inputEl) {
            inputEl.value = valorPadrao;
            setTimeout(() => {
                inputEl.focus();
                inputEl.select();
            }, 100);
        }

        const onOk = () => {
            const val = inputEl.value;
            toggleModal('modal-prompt', false);
            resolve(val);
        };

        const onCancel = () => {
            toggleModal('modal-prompt', false);
            resolve(null);
        };

        const onKey = (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                inputEl.removeEventListener('keydown', onKey);
                onOk();
            }
        };

        inputEl.addEventListener('keydown', onKey);
        btnOk.addEventListener('click', onOk, { once: true });
        btnCancel.addEventListener('click', onCancel, { once: true });

        toggleModal('modal-prompt', true);
    });
}

/**
 * Exibe o Modal do Resultado Vencedor
 */
function showVencedorModal(textoVencedor, onGirarNovamente = null) {
    const textoEl = document.getElementById('modal-vencedor-texto');
    const btnFechar = document.getElementById('modal-vencedor-btn-fechar');
    const btnGirar = document.getElementById('modal-vencedor-btn-girar');

    if (textoEl) textoEl.textContent = textoVencedor;

    const fechar = () => toggleModal('modal-vencedor', false);

    btnFechar.onclick = fechar;
    btnGirar.onclick = () => {
        fechar();
        if (typeof onGirarNovamente === 'function') {
            setTimeout(onGirarNovamente, 250);
        }
    };

    toggleModal('modal-vencedor', true);
}
