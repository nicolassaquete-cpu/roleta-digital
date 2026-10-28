/**
 * Lógica da Interface da Roleta Digital
 * Integração entre Canvas, Gerenciador de Itens, Modais e APIs
 */

// Paleta de cores para os novos itens adicionados
const PALETA_CORES = [
    '#ef4444', '#f97316', '#f59e0b', '#10b981', 
    '#06b6d4', '#3b82f6', '#6366f1', '#8b5cf6', 
    '#ec4899', '#14b8a6', '#84cc16', '#e11d48'
];

let wheelInstance = null;
let currentItems = [];
let maxLimit = 50; // Padrão gratuito
let roletaId = null;

// Presets divertidos para teste rápido
const PRESETS = {
    comida: [
        { texto: 'Pizza Margherita', cor: '#ef4444' },
        { texto: 'Hambúrguer Artesanal', cor: '#f97316' },
        { texto: 'Sushi / Japonês', cor: '#10b981' },
        { texto: 'Churrasco', cor: '#8b5cf6' },
        { texto: 'Comida Mexicana', cor: '#ec4899' },
        { texto: 'Salada Saudável', cor: '#06b6d4' },
        { texto: 'Massa / Macarrão', cor: '#eab308' },
        { texto: 'Pastel de Feira', cor: '#6366f1' }
    ],
    decisao: [
        { texto: 'SIM, com certeza!', cor: '#22c55e' },
        { texto: 'NÃO, de jeito nenhum!', cor: '#ef4444' },
        { texto: 'Talvez depois...', cor: '#eab308' },
        { texto: 'Pergunte novamente', cor: '#3b82f6' }
    ],
    numeros: [
        { texto: 'Número 1', cor: '#ef4444' },
        { texto: 'Número 2', cor: '#f97316' },
        { texto: 'Número 3', cor: '#f59e0b' },
        { texto: 'Número 4', cor: '#10b981' },
        { texto: 'Número 5', cor: '#06b6d4' },
        { texto: 'Número 6', cor: '#3b82f6' },
        { texto: 'Número 7', cor: '#6366f1' },
        { texto: 'Número 8', cor: '#8b5cf6' },
        { texto: 'Número 9', cor: '#ec4899' },
        { texto: 'Número 10', cor: '#14b8a6' }
    ]
};

/**
 * Inicializa a aplicação da Roleta
 */
function initRoletaApp(config = {}) {
    currentItems = config.items && config.items.length ? config.items : [...PRESETS.comida];
    maxLimit = config.maxLimit !== undefined ? config.maxLimit : 50;
    roletaId = config.roletaId || null;

    // Inicializa motor da roleta
    wheelInstance = new RouletteWheel('wheel-canvas', {
        items: currentItems,
        onFinish: onSpinFinished
    });

    renderItemList();
    setupEventListeners();
    updateCounterBadge();
}

/**
 * Renderiza a lista de opções na barra lateral/painel inferior
 */
function renderItemList() {
    const listEl = document.getElementById('item-list');
    if (!listEl) return;

    listEl.innerHTML = '';

    if (currentItems.length === 0) {
        listEl.innerHTML = `
            <div class="text-center py-8 text-slate-500 text-sm">
                Nenhuma opção na roleta. Adicione uma acima!
            </div>
        `;
        return;
    }

    currentItems.forEach((item, index) => {
        const row = document.createElement('div');
        row.className = 'flex items-center justify-between gap-2 p-2.5 rounded-xl bg-slate-900/60 border border-slate-800 text-sm hover:border-slate-700 transition group';
        row.innerHTML = `
            <div class="flex items-center gap-2.5 min-w-0">
                <span class="w-3.5 h-3.5 rounded-full shrink-0 shadow-sm" style="background-color: ${item.cor}"></span>
                <span class="text-slate-200 truncate font-medium text-xs sm:text-sm">${escapeHtml(item.texto)}</span>
            </div>
            <button type="button" class="text-slate-500 hover:text-rose-400 p-1 rounded-lg opacity-80 group-hover:opacity-100 transition" title="Remover item" onclick="removerItem(${index})">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </button>
        `;
        listEl.appendChild(row);
    });

    updateCounterBadge();
}

/**
 * Atualiza o contador de itens e aviso de limite
 */
function updateCounterBadge() {
    const countEl = document.getElementById('item-count-badge');
    if (countEl) {
        const isLimitReached = currentItems.length >= maxLimit && maxLimit < 999999;
        countEl.textContent = `${currentItems.length} / ${maxLimit >= 999999 ? '∞ (Premium)' : maxLimit}`;
        if (isLimitReached) {
            countEl.className = 'text-xs font-bold px-2 py-0.5 rounded bg-rose-500/20 text-rose-300 border border-rose-500/30';
        } else {
            countEl.className = 'text-xs font-bold px-2 py-0.5 rounded bg-slate-800 text-slate-300 border border-slate-700';
        }
    }
}

/**
 * Adiciona um novo item individual à roleta
 */
function adicionarItem(texto, cor = null) {
    texto = (texto || '').trim();
    if (!texto) {
        showToast('Digite um texto para o item.', 'warning');
        return;
    }

    // Regra de Negócio: Plano gratuito oferece no máximo 50 itens
    if (currentItems.length >= maxLimit) {
        showToast(`Limite atingido! O plano gratuito permite no máximo ${maxLimit} itens. Faça upgrade para o Premium para itens ilimitados!`, 'error');
        return;
    }

    const nextColor = cor || PALETA_CORES[currentItems.length % PALETA_CORES.length];
    currentItems.push({ texto, cor: nextColor });

    wheelInstance.setItems(currentItems);
    renderItemList();
    showToast(`"${texto}" adicionado à roleta!`, 'success');
}

/**
 * Remove um item da lista
 */
function removerItem(index) {
    if (index >= 0 && index < currentItems.length) {
        const removido = currentItems.splice(index, 1)[0];
        wheelInstance.setItems(currentItems);
        renderItemList();
        showToast(`"${removido.texto}" removido.`, 'info');
    }
}

/**
 * Limpa todos os itens com confirmação por MODAL
 */
async function limparTodosItens() {
    if (currentItems.length === 0) return;

    // Regra Global: Tratar mensagens do navegador em MODAL (Substitui confirm)
    const confirmou = await showConfirmModal('Tem certeza que deseja remover todos os itens da roleta?', 'Limpar Roleta');
    if (confirmou) {
        currentItems = [];
        wheelInstance.setItems(currentItems);
        renderItemList();
        showToast('Todos os itens foram removidos.', 'info');
    }
}

/**
 * Embaralha as opções
 */
function embaralharItens() {
    if (currentItems.length < 2) return;
    for (let i = currentItems.length - 1; i > 0; i--) {
        const j = Math.floor(Math.random() * (i + 1));
        [currentItems[i], currentItems[j]] = [currentItems[j], currentItems[i]];
    }
    wheelInstance.setItems(currentItems);
    renderItemList();
    showToast('Itens embaralhados!', 'info');
}

/**
 * Carrega um conjunto predefinido
 */
function carregarPreset(tipo) {
    if (PRESETS[tipo]) {
        currentItems = JSON.parse(JSON.stringify(PRESETS[tipo]));
        wheelInstance.setItems(currentItems);
        renderItemList();
        showToast(`Modelo carregado!`, 'success');
    }
}

/**
 * Callback acionado quando o giro da roleta termina
 */
async function onSpinFinished(winner) {
    const btnGirar = document.getElementById('btn-spin');
    if (btnGirar) btnGirar.disabled = false;

    if (!winner) return;

    // Registra o sorteio no backend via AJAX PDO e nos logs de auditoria
    try {
        await fetch('api/salvar_sorteio.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                item_sorteado: winner.texto,
                roleta_id: roletaId
            })
        });
    } catch (e) {
        console.error('Falha ao registrar sorteio:', e);
    }

    // Exibe o Modal customizado do vencedor
    showVencedorModal(winner.texto, () => {
        iniciarGiro();
    });
}

/**
 * Inicia o giro da roleta
 */
function iniciarGiro() {
    if (!wheelInstance) return;
    if (currentItems.length < 2) {
        showToast('Adicione ao menos 2 itens na roleta para girar!', 'warning');
        return;
    }

    const btnGirar = document.getElementById('btn-spin');
    if (btnGirar) btnGirar.disabled = true;

    wheelInstance.spin();
}

/**
 * Configura os event listeners da interface
 */
function setupEventListeners() {
    // Botão Girar
    const btnSpin = document.getElementById('btn-spin');
    if (btnSpin) {
        btnSpin.addEventListener('click', iniciarGiro);
    }

    // Formulário de adicionar item
    const formItem = document.getElementById('form-add-item');
    const inputItem = document.getElementById('input-item-text');
    if (formItem && inputItem) {
        formItem.addEventListener('submit', (e) => {
            e.preventDefault();
            adicionarItem(inputItem.value);
            inputItem.value = '';
            inputItem.focus();
        });
    }

    // Botão de Limpar
    const btnLimpar = document.getElementById('btn-limpar-itens');
    if (btnLimpar) {
        btnLimpar.addEventListener('click', limparTodosItens);
    }

    // Botão de Embaralhar
    const btnShuffle = document.getElementById('btn-shuffle');
    if (btnShuffle) {
        btnShuffle.addEventListener('click', embaralharItens);
    }

    // Presets
    document.querySelectorAll('[data-preset]').forEach(btn => {
        btn.addEventListener('click', () => carregarPreset(btn.dataset.preset));
    });

    // Importação em massa via Modal
    const btnMassa = document.getElementById('btn-import-massa');
    if (btnMassa) {
        btnMassa.addEventListener('click', async () => {
            const texto = await showPromptModal('Cole os itens desejados (um por linha):', '', 'Importar Itens em Massa');
            if (texto) {
                const linhas = texto.split('\n').map(l => l.trim()).filter(l => l.length > 0);
                if (linhas.length === 0) return;

                let inseridos = 0;
                for (const linha of linhas) {
                    if (currentItems.length >= maxLimit) {
                        showToast(`Limite do plano gratuito (${maxLimit} itens) atingido durante a importação.`, 'warning');
                        break;
                    }
                    currentItems.push({
                        texto: linha,
                        cor: PALETA_CORES[currentItems.length % PALETA_CORES.length]
                    });
                    inseridos++;
                }

                wheelInstance.setItems(currentItems);
                renderItemList();
                showToast(`${inseridos} itens importados com sucesso!`, 'success');
            }
        });
    }

    // Botão Salvar Roleta
    const btnSalvar = document.getElementById('btn-salvar-roleta');
    if (btnSalvar) {
        btnSalvar.addEventListener('click', salvarRoletaAtual);
    }
}

/**
 * Salva a roleta atual na conta do usuário
 */
async function salvarRoletaAtual() {
    if (currentItems.length === 0) {
        showToast('Adicione opções antes de salvar a roleta.', 'warning');
        return;
    }

    const tituloAtual = document.getElementById('roleta-titulo')?.textContent || 'Minha Roleta';
    const titulo = await showPromptModal('Nome da Roleta:', tituloAtual, 'Salvar Roleta');
    if (!titulo) return;

    try {
        const resp = await fetch('api/salvar_roleta.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                id: roletaId,
                titulo: titulo,
                itens: currentItems
            })
        });

        const data = await resp.json();

        if (data.sucesso) {
            roletaId = data.roleta_id;
            showToast('Roleta salva com sucesso no seu perfil!', 'success');
            const tituloEl = document.getElementById('roleta-titulo');
            if (tituloEl) tituloEl.textContent = titulo;
        } else {
            showToast(data.mensagem || 'Erro ao salvar a roleta.', 'error');
        }
    } catch (e) {
        showToast('Erro de comunicação com o servidor.', 'error');
    }
}

function escapeHtml(str) {
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
}
