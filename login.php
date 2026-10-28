<?php
/**
 * Página de Login de Usuários
 * Requisito CORE: "Tratar senhas de usuários com hash bcrypt"
 * Requisito Global: "Use sempre PDO para conexão e queries no MySQL para evitar SQL Injections"
 */
require_once __DIR__ . '/helpers/auth_helper.php';
require_once __DIR__ . '/helpers/logger.php';
require_once __DIR__ . '/bd.php';

if (estaAutenticado()) {
    header('Location: index.php');
    exit;
}

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = strtolower(trim($_POST['email'] ?? ''));
    $senha = $_POST['senha'] ?? '';

    if (empty($email) || empty($senha)) {
        $erro = 'Informe seu e-mail e sua senha.';
    } else {
        try {
            // Busca o usuário via PDO Prepared Statement
            $stmt = $pdo->prepare("SELECT id, nome, email, senha, plano FROM usuarios WHERE email = :email LIMIT 1");
            $stmt->execute([':email' => $email]);
            $usuario = $stmt->fetch();

            // Requisito CORE: Verificação da senha com hash bcrypt
            if ($usuario && password_verify($senha, $usuario['senha'])) {
                // Registra dados na sessão nativa do PHP
                $_SESSION['usuario_id'] = (int)$usuario['id'];
                $_SESSION['usuario_nome'] = $usuario['nome'];
                $_SESSION['usuario_email'] = $usuario['email'];
                $_SESSION['usuario_plano'] = $usuario['plano'];

                // Requisito CORE: Registra log de auditoria
                registrarLog(
                    $pdo,
                    'LOGIN_SUCESSO',
                    ['email' => $email, 'plano' => $usuario['plano']],
                    $usuario['id'],
                    $usuario['nome']
                );

                definirMensagemFlash('success', "Olá, {$usuario['nome']}! Você entrou com sucesso.");
                header('Location: index.php');
                exit;
            } else {
                $erro = 'E-mail ou senha incorretos.';

                // Log de falha de login para auditoria de segurança
                registrarLog(
                    $pdo,
                    'LOGIN_FALHA',
                    ['email_tentativa' => $email]
                );
            }
        } catch (Exception $e) {
            $erro = 'Erro de comunicação com o banco: ' . $e->getMessage();
        }
    }

    if ($erro) {
        definirMensagemFlash('error', $erro);
    }
}

$tituloPagina = 'Entrar na Conta';
require_once __DIR__ . '/views/header.php';
?>

<main class="flex-1 flex items-center justify-center p-4 sm:p-6 my-8">
    <div class="max-w-md w-full bg-slate-900/90 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-2xl backdrop-blur-md">
        
        <div class="text-center mb-6">
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-indigo-600/20 text-indigo-400 mb-3 border border-indigo-500/30">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                </svg>
            </div>
            <h1 class="text-2xl font-extrabold text-white tracking-tight">Acessar Conta</h1>
            <p class="text-slate-400 text-sm mt-1">Gerencie suas roletas e acesse seu histórico</p>
        </div>

        <?php if (!empty($erro)): ?>
            <div class="mb-5 p-3.5 rounded-xl bg-rose-950/50 border border-rose-500/40 text-rose-200 text-xs font-semibold flex items-center gap-2">
                <svg class="w-4 h-4 text-rose-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span><?= htmlspecialchars($erro) ?></span>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-4">
            <div>
                <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">E-mail</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required
                    class="w-full px-4 py-2.5 bg-slate-950/60 border border-slate-700 rounded-xl text-white text-sm focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition"
                    placeholder="seuemail@exemplo.com">
            </div>

            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label for="senha" class="block text-xs font-semibold uppercase tracking-wider text-slate-300">Senha</label>
                </div>
                <input type="password" id="senha" name="senha" required
                    class="w-full px-4 py-2.5 bg-slate-950/60 border border-slate-700 rounded-xl text-white text-sm focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition"
                    placeholder="••••••••">
            </div>

            <button type="submit" class="w-full mt-2 py-3 px-4 rounded-xl font-bold text-white bg-indigo-600 hover:bg-indigo-500 active:scale-[0.98] transition shadow-lg shadow-indigo-600/30">
                Entrar no Sistema
            </button>
        </form>

        <!-- Acesso Rápido para Testes dos Planos Gratuito x Premium -->
        <div class="mt-6 pt-5 border-t border-slate-800">
            <span class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 text-center mb-3">Contas de Teste Rápido (Demonstração)</span>
            <div class="grid grid-cols-2 gap-2">
                <button type="button" onclick="preencherCredenciais('demo@gratuito.com', '123456')" 
                    class="p-2 rounded-xl bg-slate-800 hover:bg-slate-700 border border-slate-700 text-left transition text-xs">
                    <div class="font-bold text-slate-200">Plano Gratuito</div>
                    <div class="text-[10px] text-slate-400">demo@gratuito.com</div>
                </button>
                <button type="button" onclick="preencherCredenciais('premium@roleta.com', '123456')" 
                    class="p-2 rounded-xl bg-amber-950/40 hover:bg-amber-900/40 border border-amber-600/40 text-left transition text-xs">
                    <div class="font-bold text-amber-300">Plano Premium</div>
                    <div class="text-[10px] text-amber-400/80">premium@roleta.com</div>
                </button>
            </div>
        </div>

        <div class="mt-6 text-center text-xs text-slate-400">
            Ainda não tem conta?
            <a href="cadastro.php" class="text-indigo-400 hover:text-indigo-300 font-semibold ml-1">Criar conta grátis</a>
        </div>
    </div>
</main>

<script>
function preencherCredenciais(email, senha) {
    document.getElementById('email').value = email;
    document.getElementById('senha').value = senha;
    showToast(`Credenciais de teste para [${email}] preenchidas!`, 'info');
}
</script>

<?php require_once __DIR__ . '/views/footer.php'; ?>
