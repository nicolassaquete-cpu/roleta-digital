<?php
/**
 * Página de Cadastro de Usuário
 * Requisito CORE: "Tratar senhas de usuários com hash bcrypt"
 * Requisito Global: "Use sempre PDO para conexão e queries no MySQL para evitar SQL Injections"
 */
require_once __DIR__ . '/helpers/auth_helper.php';
require_once __DIR__ . '/helpers/logger.php';
require_once __DIR__ . '/bd.php';

// Redireciona se já estiver conectado
if (estaAutenticado()) {
    header('Location: index.php');
    exit;
}

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $email = strtolower(trim($_POST['email'] ?? ''));
    $senha = $_POST['senha'] ?? '';
    $confirmarSenha = $_POST['confirmar_senha'] ?? '';

    if (empty($nome) || empty($email) || empty($senha)) {
        $erro = 'Preencha todos os campos obrigatórios.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = 'Informe um endereço de e-mail válido.';
    } elseif (strlen($senha) < 6) {
        $erro = 'A senha deve conter no mínimo 6 caracteres.';
    } elseif ($senha !== $confirmarSenha) {
        $erro = 'As senhas informadas não coincidem.';
    } else {
        try {
            // Verifica se o e-mail já existe via PDO Prepared Statement
            $stmtCheck = $pdo->prepare("SELECT id FROM usuarios WHERE email = :email");
            $stmtCheck->execute([':email' => $email]);

            if ($stmtCheck->fetch()) {
                $erro = 'Este e-mail já está cadastrado no sistema.';
            } else {
                // Requisito CORE: Criptografia da senha com bcrypt nativo do PHP
                $senhaHash = password_hash($senha, PASSWORD_BCRYPT);

                // Insere usuário com plano gratuito padrão
                $stmtInsert = $pdo->prepare("INSERT INTO usuarios (nome, email, senha, plano, criado_em) VALUES (:nome, :email, :senha, 'gratuito', NOW())");
                $stmtInsert->execute([
                    ':nome'  => $nome,
                    ':email' => $email,
                    ':senha' => $senhaHash
                ]);

                $novoId = (int)$pdo->lastInsertId();

                // Registra log de auditoria
                registrarLog(
                    $pdo,
                    'CADASTRO_USUARIO',
                    ['nome' => $nome, 'email' => $email, 'plano' => 'gratuito'],
                    $novoId,
                    $nome
                );

                // Login automático na sessão nativa
                $_SESSION['usuario_id'] = $novoId;
                $_SESSION['usuario_nome'] = $nome;
                $_SESSION['usuario_email'] = $email;
                $_SESSION['usuario_plano'] = 'gratuito';

                definirMensagemFlash('success', "Bem-vindo(a), {$nome}! Sua conta foi criada com sucesso.");
                header('Location: index.php');
                exit;
            }
        } catch (Exception $e) {
            $erro = 'Erro interno ao processar o cadastro: ' . $e->getMessage();
        }
    }

    if ($erro) {
        definirMensagemFlash('error', $erro);
    }
}

$tituloPagina = 'Cadastrar Conta';
require_once __DIR__ . '/views/header.php';
?>

<main class="flex-1 flex items-center justify-center p-4 sm:p-6 my-8">
    <div class="max-w-md w-full bg-slate-900/90 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-2xl backdrop-blur-md">
        
        <div class="text-center mb-6">
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-indigo-600/20 text-indigo-400 mb-3 border border-indigo-500/30">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                </svg>
            </div>
            <h1 class="text-2xl font-extrabold text-white tracking-tight">Criar Nova Conta</h1>
            <p class="text-slate-400 text-sm mt-1">Cadastre-se para salvar suas roletas e manter seu histórico</p>
        </div>

        <?php if (!empty($erro)): ?>
            <div class="mb-5 p-3.5 rounded-xl bg-rose-950/50 border border-rose-500/40 text-rose-200 text-xs font-semibold flex items-center gap-2">
                <svg class="w-4 h-4 text-rose-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span><?= htmlspecialchars($erro) ?></span>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-4">
            <div>
                <label for="nome" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">Nome Completo</label>
                <input type="text" id="nome" name="nome" value="<?= htmlspecialchars($_POST['nome'] ?? '') ?>" required
                    class="w-full px-4 py-2.5 bg-slate-950/60 border border-slate-700 rounded-xl text-white text-sm focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition"
                    placeholder="Ex: João da Silva">
            </div>

            <div>
                <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">Endereço de E-mail</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required
                    class="w-full px-4 py-2.5 bg-slate-950/60 border border-slate-700 rounded-xl text-white text-sm focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition"
                    placeholder="seuemail@exemplo.com">
            </div>

            <div>
                <label for="senha" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">Senha</label>
                <input type="password" id="senha" name="senha" required minlength="6"
                    class="w-full px-4 py-2.5 bg-slate-950/60 border border-slate-700 rounded-xl text-white text-sm focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition"
                    placeholder="Mínimo de 6 caracteres">
                <span class="text-[11px] text-slate-400 mt-1 block">Protegida com criptografia segura bcrypt.</span>
            </div>

            <div>
                <label for="confirmar_senha" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">Confirmar Senha</label>
                <input type="password" id="confirmar_senha" name="confirmar_senha" required minlength="6"
                    class="w-full px-4 py-2.5 bg-slate-950/60 border border-slate-700 rounded-xl text-white text-sm focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition"
                    placeholder="Repita a senha">
            </div>

            <button type="submit" class="w-full mt-2 py-3 px-4 rounded-xl font-bold text-white bg-indigo-600 hover:bg-indigo-500 active:scale-[0.98] transition shadow-lg shadow-indigo-600/30">
                Criar Minha Conta
            </button>
        </form>

        <div class="mt-6 pt-5 border-t border-slate-800 text-center text-xs text-slate-400">
            Já possui uma conta?
            <a href="login.php" class="text-indigo-400 hover:text-indigo-300 font-semibold ml-1">Entrar aqui</a>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/views/footer.php'; ?>
