<?php
/**
 * Instalador Web do Banco de Dados
 * Permite criar a base e as tabelas com 1 clique se o MySQL estiver ativo.
 */

$mensagem = '';
$sucesso = false;

$dbHost = getenv('DB_HOST') ?: 'localhost';
$dbPort = getenv('DB_PORT') ?: '3306';
$dbName = getenv('DB_NAME') ?: 'roleta_digital';
$dbUser = getenv('DB_USER') ?: 'root';
$dbPass = getenv('DB_PASS') !== false ? getenv('DB_PASS') : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Conexão inicial com o MySQL sem selecionar o banco
        $pdoServer = new PDO("mysql:host={$dbHost};port={$dbPort};charset=utf8mb4", $dbUser, $dbPass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);

        // Lê o arquivo schema.sql
        $sqlPath = __DIR__ . '/schema.sql';
        if (!file_exists($sqlPath)) {
            throw new Exception("Arquivo schema.sql não foi encontrado.");
        }

        $sql = file_get_contents($sqlPath);

        // Executa as instruções
        $pdoServer->exec($sql);

        $sucesso = true;
        $mensagem = "Banco de dados e tabelas criados com sucesso! Redirecionando...";
    } catch (Exception $e) {
        $mensagem = "Erro ao instalar o banco: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR" class="h-full bg-slate-900">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instalador do Banco de Dados - Roleta Digital</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="h-full flex items-center justify-center p-4 text-slate-100">
    <div class="max-w-md w-full bg-slate-800/80 backdrop-blur-md p-8 rounded-2xl border border-slate-700 shadow-2xl">
        <div class="text-center mb-6">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-indigo-600/20 text-indigo-400 mb-4 border border-indigo-500/30">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"/>
                </svg>
            </div>
            <h1 class="text-2xl font-bold tracking-tight">Instalação do Banco</h1>
            <p class="text-slate-400 text-sm mt-1">Configure o banco MySQL da Roleta Digital</p>
        </div>

        <?php if (!empty($mensagem)): ?>
            <div class="mb-6 p-4 rounded-xl text-sm font-medium <?= $sucesso ? 'bg-emerald-950/50 border border-emerald-600 text-emerald-300' : 'bg-red-950/50 border border-red-600 text-red-300' ?>">
                <?= htmlspecialchars($mensagem) ?>
            </div>
            <?php if ($sucesso): ?>
                <script>
                    setTimeout(() => { window.location.href = 'index.php'; }, 2000);
                </script>
            <?php endif; ?>
        <?php endif; ?>

        <div class="space-y-4 mb-6 bg-slate-900/60 p-4 rounded-xl border border-slate-700/60 text-xs text-slate-300">
            <div class="flex justify-between py-1 border-b border-slate-800">
                <span class="text-slate-400">Host:</span>
                <span class="font-mono text-indigo-300"><?= htmlspecialchars($dbHost) ?></span>
            </div>
            <div class="flex justify-between py-1 border-b border-slate-800">
                <span class="text-slate-400">Porta:</span>
                <span class="font-mono text-indigo-300"><?= htmlspecialchars($dbPort) ?></span>
            </div>
            <div class="flex justify-between py-1 border-b border-slate-800">
                <span class="text-slate-400">Banco:</span>
                <span class="font-mono text-indigo-300"><?= htmlspecialchars($dbName) ?></span>
            </div>
            <div class="flex justify-between py-1">
                <span class="text-slate-400">Usuário:</span>
                <span class="font-mono text-indigo-300"><?= htmlspecialchars($dbUser) ?></span>
            </div>
        </div>

        <form method="POST">
            <button type="submit" class="w-full py-3 px-4 rounded-xl font-semibold text-white bg-indigo-600 hover:bg-indigo-500 active:scale-[0.98] transition shadow-lg shadow-indigo-600/30 flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
                Criar Banco e Tabelas Agora
            </button>
        </form>

        <div class="mt-6 text-center">
            <a href="index.php" class="text-xs text-slate-400 hover:text-slate-200 transition">
                &larr; Voltar para a página inicial
            </a>
        </div>
    </div>
</body>
</html>
