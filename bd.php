<?php
/**
 * Conexão com o banco de dados MySQL via PDO.
 * Regra Global: Use sempre PDO para conexão e queries no MySQL para evitar SQL Injections.
 */

// Configurações do Banco de Dados (podem ser substituídas por variáveis de ambiente)
$dbHost = getenv('DB_HOST') ?: 'localhost';
$dbPort = getenv('DB_PORT') ?: '3306';
$dbName = getenv('DB_NAME') ?: 'roleta_digital';
$dbUser = getenv('DB_USER') ?: 'root';
$dbPass = getenv('DB_PASS') !== false ? getenv('DB_PASS') : '';

try {
    $dsn = "mysql:host={$dbHost};port={$dbPort};dbname={$dbName};charset=utf8mb4";
    $opcoes = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    $pdo = new PDO($dsn, $dbUser, $dbPass, $opcoes);
} catch (PDOException $e) {
    // Se o banco não existir ainda, tenta conexão básica para permitir execução do instalador
    try {
        $dsnSemBanco = "mysql:host={$dbHost};port={$dbPort};charset=utf8mb4";
        $pdoSemBanco = new PDO($dsnSemBanco, $dbUser, $dbPass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
        
        // Se a página atual não for install.php, sugere redirecionamento
        $paginaAtual = basename($_SERVER['PHP_SELF'] ?? '');
        if ($paginaAtual !== 'install.php') {
            header('Location: install.php');
            exit;
        }
    } catch (PDOException $ex) {
        // Exibe erro estruturado se o servidor MySQL estiver inacessível
        die("
            <div style='font-family: sans-serif; max-width: 600px; margin: 40px auto; padding: 20px; border: 1px solid #f87171; border-radius: 8px; background: #fef2f2; color: #991b1b;'>
                <h2 style='margin-top: 0;'>Erro ao conectar com o MySQL</h2>
                <p>Não foi possível estabelecer conexão via PDO: <strong>" . htmlspecialchars($e->getMessage()) . "</strong></p>
                <p>Verifique se o serviço MySQL está ativo e se as credenciais em <code>bd.php</code> estão corretas.</p>
                <a href='install.php' style='display: inline-block; background: #dc2626; color: #fff; padding: 10px 16px; border-radius: 6px; text-decoration: none;'>Abrir Instalador do Banco de Dados</a>
            </div>
        ");
    }
}
