<?php
/**
 * Perfil do Usuário e Estatísticas
 * Requisito: Tratar senhas com bcrypt e registrar auditoria via PDO
 */
require_once __DIR__ . '/helpers/auth_helper.php';
require_once __DIR__ . '/helpers/logger.php';
require_once __DIR__ . '/bd.php';

exigirAutenticacao();

$usuario = obterUsuarioLogado();
$usuarioId = $usuario['id'];
$tituloPagina = 'Meu Perfil';

// Processa atualização de perfil
$sucesso = '';
$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';

    if ($acao === 'atualizar_dados') {
        $novoNome = trim($_POST['nome'] ?? '');
        if (empty($novoNome)) {
            $erro = 'O nome não pode ficar em branco.';
        } else {
            try {
                $stmt = $pdo->prepare("UPDATE usuarios SET nome = :nome, atualizado_em = NOW() WHERE id = :id");
                $stmt->execute([':nome' => $novoNome, ':id' => $usuarioId]);

                $_SESSION['usuario_nome'] = $novoNome;
                $usuario['nome'] = $novoNome;

                registrarLog($pdo, 'ATUALIZAR_PERFIL', ['novo_nome' => $novoNome], $usuarioId, $novoNome);
                definirMensagemFlash('success', 'Nome atualizado com sucesso!');
            } catch (Exception $e) {
                $erro = 'Erro ao atualizar dados: ' . $e->getMessage();
            }
        }
    } elseif ($acao === 'alterar_senha') {
        $senhaAtual = $_POST['senha_atual'] ?? '';
        $novaSenha = $_POST['nova_senha'] ?? '';
        $confirmar = $_POST['confirmar_nova_senha'] ?? '';

        if (empty($senhaAtual) || empty($novaSenha)) {
            $erro = 'Preencha todos os campos de senha.';
        } elseif (strlen($novaSenha) < 6) {
            $erro = 'A nova senha deve ter no mínimo 6 caracteres.';
        } elseif ($novaSenha !== $confirmar) {
            $erro = 'A nova senha e a confirmação não coincidem.';
        } else {
            try {
                // Busca senha atual do banco
                $stmt = $pdo->prepare("SELECT senha FROM usuarios WHERE id = :id");
                $stmt->execute([':id' => $usuarioId]);
                $hashBanco = $stmt->fetchColumn();

                if (password_verify($senhaAtual, $hashBanco)) {
                    // Criptografa nova senha com BCRYPT
                    $novoHash = password_hash($novaSenha, PASSWORD_BCRYPT);
                    $stmtUpdate = $pdo->prepare("UPDATE usuarios SET senha = :senha, atualizado_em = NOW() WHERE id = :id");
                    $stmtUpdate->execute([':senha' => $novoHash, ':id' => $usuarioId]);

                    registrarLog($pdo, 'ALTERAR_SENHA', 'Usuário alterou sua senha', $usuarioId, $usuario['nome']);
                    definirMensagemFlash('success', 'Senha alterada com sucesso!');
                } else {
                    $erro = 'A senha atual informada está incorreta.';
                }
            } catch (Exception $e) {
                $erro = 'Erro ao alterar senha: ' . $e->getMessage();
            }
        }
    }

    if ($erro) {
        definirMensagemFlash('error', $erro);
    }
}

// Estatísticas do usuário
$totalRoletas = 0;
$totalSorteios = 0;
$dadosUsuarioBanco = [];

try {
    $stmt1 = $pdo->prepare("SELECT COUNT(*) FROM roletas WHERE usuario_id = :id");
    $stmt1->execute([':id' => $usuarioId]);
    $totalRoletas = (int)$stmt1->fetchColumn();

    $stmt2 = $pdo->prepare("SELECT COUNT(*) FROM historico_sorteios WHERE usuario_id = :id");
    $stmt2->execute([':id' => $usuarioId]);
    $totalSorteios = (int)$stmt2->fetchColumn();

    $stmt3 = $pdo->prepare("SELECT email, plano, criado_em FROM usuarios WHERE id = :id");
    $stmt3->execute([':id' => $usuarioId]);
    $dadosUsuarioBanco = $stmt3->fetch() ?: [];
} catch (Exception $e) {}

require_once __DIR__ . '/views/header.php';
?>

<main class="flex-1 max-w-4xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    <div class="mb-8">
        <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">Meu Perfil</h1>
        <p class="text-sm text-slate-400 mt-1">Gerencie sua conta e configurações</p>
    </div>

    <!-- Cards de Estatísticas Rápidas -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
        <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-5">
            <span class="text-xs uppercase font-bold text-slate-400 tracking-wider">Plano Atual</span>
            <div class="flex items-center gap-2 mt-2">
                <span class="text-xl font-bold <?= ehPremium() ? 'text-amber-400' : 'text-slate-200' ?>">
                    <?= ehPremium() ? 'Premium VIP' : 'Gratuito' ?>
                </span>
            </div>
            <a href="planos.php" class="text-xs text-indigo-400 hover:underline mt-2 inline-block">Gerenciar plano &rarr;</a>
        </div>

        <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-5">
            <span class="text-xs uppercase font-bold text-slate-400 tracking-wider">Roletas Criadas</span>
            <div class="text-2xl font-black text-white mt-1"><?= $totalRoletas ?></div>
            <a href="roletas.php" class="text-xs text-indigo-400 hover:underline mt-2 inline-block">Ver minhas roletas &rarr;</a>
        </div>

        <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-5">
            <span class="text-xs uppercase font-bold text-slate-400 tracking-wider">Sorteios Realizados</span>
            <div class="text-2xl font-black text-white mt-1"><?= $totalSorteios ?></div>
            <a href="historico.php" class="text-xs text-indigo-400 hover:underline mt-2 inline-block">Ver histórico &rarr;</a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Atualizar Nome -->
        <div class="bg-slate-900/80 border border-slate-800 rounded-3xl p-6 shadow-xl">
            <h3 class="text-base font-bold text-white mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                Dados da Conta
            </h3>

            <form method="POST" class="space-y-4">
                <input type="hidden" name="acao" value="atualizar_dados">
                
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">E-mail</label>
                    <input type="email" disabled value="<?= htmlspecialchars($dadosUsuarioBanco['email'] ?? $usuario['email']) ?>" 
                        class="w-full px-4 py-2.5 bg-slate-950/40 border border-slate-800 rounded-xl text-slate-400 text-sm cursor-not-allowed">
                </div>

                <div>
                    <label for="nome" class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Nome Completo</label>
                    <input type="text" id="nome" name="nome" value="<?= htmlspecialchars($usuario['nome']) ?>" required
                        class="w-full px-4 py-2.5 bg-slate-950/70 border border-slate-700 rounded-xl text-white text-sm focus:outline-none focus:border-indigo-500">
                </div>

                <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-semibold text-xs sm:text-sm transition shadow-md shadow-indigo-600/30">
                    Salvar Dados
                </button>
            </form>
        </div>

        <!-- Alterar Senha com BCRYPT -->
        <div class="bg-slate-900/80 border border-slate-800 rounded-3xl p-6 shadow-xl">
            <h3 class="text-base font-bold text-white mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                Alterar Senha
            </h3>

            <form method="POST" class="space-y-4">
                <input type="hidden" name="acao" value="alterar_senha">

                <div>
                    <label for="senha_atual" class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Senha Atual</label>
                    <input type="password" id="senha_atual" name="senha_atual" required
                        class="w-full px-4 py-2 bg-slate-950/70 border border-slate-700 rounded-xl text-white text-sm focus:outline-none focus:border-indigo-500">
                </div>

                <div>
                    <label for="nova_senha" class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Nova Senha</label>
                    <input type="password" id="nova_senha" name="nova_senha" required minlength="6"
                        class="w-full px-4 py-2 bg-slate-950/70 border border-slate-700 rounded-xl text-white text-sm focus:outline-none focus:border-indigo-500">
                </div>

                <div>
                    <label for="confirmar_nova_senha" class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Confirmar Nova Senha</label>
                    <input type="password" id="confirmar_nova_senha" name="confirmar_nova_senha" required minlength="6"
                        class="w-full px-4 py-2 bg-slate-950/70 border border-slate-700 rounded-xl text-white text-sm focus:outline-none focus:border-indigo-500">
                </div>

                <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 font-semibold text-xs sm:text-sm transition">
                    Atualizar Senha
                </button>
            </form>
        </div>
    </div>

    <!-- Anúncios se gratuito -->
    <?php require __DIR__ . '/views/anuncios_bottom.php'; ?>
</main>

<?php require_once __DIR__ . '/views/footer.php'; ?>
