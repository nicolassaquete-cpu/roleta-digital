<?php
/**
 * Cabeçalho Global do Sistema
 * Responsivo, Mobile-First com Tailwind CSS
 */
require_once __DIR__ . '/../helpers/auth_helper.php';

$usuarioAtual = obterUsuarioLogado();
$paginaAtual = basename($_SERVER['PHP_SELF'] ?? '');
?>
<!DOCTYPE html>
<html lang="pt-BR" class="h-full bg-slate-950 text-slate-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($tituloPagina) ? htmlspecialchars($tituloPagina) . ' - ' : '' ?>Roleta Digital</title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Canvas Confetti para celebrações -->
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.9.3/dist/confetti.browser.min.js"></script>

    <!-- Fonte Google Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Estilos complementares -->
    <link rel="stylesheet" href="assets/css/style.css">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#eef2ff',
                            500: '#6366f1',
                            600: '#4f46e5',
                            700: '#4338ca',
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="min-h-full flex flex-col bg-slate-950 text-slate-100 antialiased font-sans selection:bg-indigo-500 selection:text-white">

    <!-- Barra de Navegação -->
    <header class="sticky top-0 z-40 bg-slate-900/80 backdrop-blur-md border-b border-slate-800/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                
                <!-- Logo & Marca -->
                <div class="flex items-center gap-3">
                    <a href="index.php" class="flex items-center gap-2.5 group">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-600 to-purple-600 flex items-center justify-center text-white shadow-lg shadow-indigo-500/25 group-hover:scale-105 transition-transform">
                            <!-- Ícone de Roleta -->
                            <svg class="w-6 h-6 animate-[spin_10s_linear_infinite]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10" stroke-width="2"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 2v20M2 12h20M4.93 4.93l14.14 14.14M4.93 19.07l14.14-14.14"/>
                            </svg>
                        </div>
                        <div>
                            <span class="text-lg font-extrabold tracking-tight bg-gradient-to-r from-white via-indigo-100 to-indigo-400 bg-clip-text text-transparent">Roleta Digital</span>
                            <span class="hidden sm:inline-block text-[10px] uppercase font-bold tracking-widest text-indigo-400 ml-1.5 px-1.5 py-0.5 rounded bg-indigo-500/10 border border-indigo-500/20">Online</span>
                        </div>
                    </a>
                </div>

                <!-- Links Desktop -->
                <nav class="hidden md:flex items-center gap-1 text-sm font-medium">
                    <a href="index.php" class="px-3.5 py-2 rounded-xl transition <?= $paginaAtual === 'index.php' ? 'bg-indigo-600/20 text-indigo-300 font-semibold border border-indigo-500/30' : 'text-slate-300 hover:text-white hover:bg-slate-800/60' ?>">
                        Roleta
                    </a>
                    
                    <?php if (estaAutenticado()): ?>
                        <a href="roletas.php" class="px-3.5 py-2 rounded-xl transition <?= $paginaAtual === 'roletas.php' ? 'bg-indigo-600/20 text-indigo-300 font-semibold border border-indigo-500/30' : 'text-slate-300 hover:text-white hover:bg-slate-800/60' ?>">
                            Minhas Roletas
                        </a>
                    <?php endif; ?>

                    <a href="historico.php" class="px-3.5 py-2 rounded-xl transition <?= $paginaAtual === 'historico.php' ? 'bg-indigo-600/20 text-indigo-300 font-semibold border border-indigo-500/30' : 'text-slate-300 hover:text-white hover:bg-slate-800/60' ?>">
                        Histórico & Logs
                    </a>

                    <a href="planos.php" class="px-3.5 py-2 rounded-xl transition <?= $paginaAtual === 'planos.php' ? 'bg-indigo-600/20 text-indigo-300 font-semibold border border-indigo-500/30' : 'text-slate-300 hover:text-white hover:bg-slate-800/60' ?>">
                        Planos
                    </a>
                </nav>

                <!-- Área de Usuário / Ações -->
                <div class="hidden md:flex items-center gap-3">
                    <?php if ($usuarioAtual): ?>
                        <!-- Badge do Plano -->
                        <a href="planos.php" class="inline-flex items-center gap-1.5 text-xs font-bold px-2.5 py-1 rounded-lg border transition <?= ehPremium() ? 'bg-gradient-to-r from-amber-500/20 to-yellow-500/20 text-amber-300 border-amber-500/40 hover:border-amber-400' : 'bg-slate-800 text-slate-300 border-slate-700 hover:bg-slate-700' ?>">
                            <?php if (ehPremium()): ?>
                                <svg class="w-3.5 h-3.5 text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                <span>PREMIUM</span>
                            <?php else: ?>
                                <span class="w-2 h-2 rounded-full bg-slate-400"></span>
                                <span>GRATUITO</span>
                            <?php endif; ?>
                        </a>

                        <!-- Dropdown / Link de Perfil -->
                        <a href="perfil.php" class="text-sm font-medium text-slate-300 hover:text-white px-2 py-1">
                            <?= htmlspecialchars($usuarioAtual['nome']) ?>
                        </a>

                        <a href="logout.php" class="p-2 rounded-xl text-slate-400 hover:text-rose-400 hover:bg-rose-950/30 transition" title="Sair da conta">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        </a>
                    <?php else: ?>
                        <a href="login.php" class="text-sm font-semibold text-slate-300 hover:text-white px-3 py-2 transition">
                            Entrar
                        </a>
                        <a href="cadastro.php" class="px-4 py-2 rounded-xl text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-500 shadow-md shadow-indigo-600/30 transition">
                            Cadastrar
                        </a>
                    <?php endif; ?>
                </div>

                <!-- Botão Menu Mobile -->
                <div class="flex md:hidden items-center gap-2">
                    <?php if ($usuarioAtual): ?>
                        <span class="text-xs font-bold px-2 py-0.5 rounded <?= ehPremium() ? 'bg-amber-500/20 text-amber-300 border border-amber-500/30' : 'bg-slate-800 text-slate-400' ?>">
                            <?= ehPremium() ? 'VIP' : 'GRÁTIS' ?>
                        </span>
                    <?php endif; ?>
                    <button id="mobile-menu-btn" type="button" class="p-2 rounded-xl text-slate-400 hover:text-white hover:bg-slate-800 focus:outline-none">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Menu Mobile Drawer -->
        <div id="mobile-menu" class="hidden md:hidden border-t border-slate-800 bg-slate-900 px-4 pt-3 pb-5 space-y-2">
            <a href="index.php" class="block px-3 py-2 rounded-xl text-base font-medium <?= $paginaAtual === 'index.php' ? 'bg-indigo-600/20 text-indigo-300' : 'text-slate-300 hover:bg-slate-800' ?>">
                Roleta
            </a>
            <?php if (estaAutenticado()): ?>
                <a href="roletas.php" class="block px-3 py-2 rounded-xl text-base font-medium <?= $paginaAtual === 'roletas.php' ? 'bg-indigo-600/20 text-indigo-300' : 'text-slate-300 hover:bg-slate-800' ?>">
                    Minhas Roletas
                </a>
            <?php endif; ?>
            <a href="historico.php" class="block px-3 py-2 rounded-xl text-base font-medium <?= $paginaAtual === 'historico.php' ? 'bg-indigo-600/20 text-indigo-300' : 'text-slate-300 hover:bg-slate-800' ?>">
                Histórico & Logs de Auditoria
            </a>
            <a href="planos.php" class="block px-3 py-2 rounded-xl text-base font-medium <?= $paginaAtual === 'planos.php' ? 'bg-indigo-600/20 text-indigo-300' : 'text-slate-300 hover:bg-slate-800' ?>">
                Planos (Gratuito x Premium)
            </a>

            <div class="pt-3 border-t border-slate-800 flex flex-col gap-2">
                <?php if ($usuarioAtual): ?>
                    <div class="px-3 py-1 text-xs text-slate-400">Logado como: <strong class="text-white"><?= htmlspecialchars($usuarioAtual['nome']) ?></strong></div>
                    <a href="perfil.php" class="block px-3 py-2 rounded-xl text-base font-medium text-slate-300 hover:bg-slate-800">
                        Meu Perfil
                    </a>
                    <a href="logout.php" class="block px-3 py-2 rounded-xl text-base font-medium text-rose-400 hover:bg-rose-950/30">
                        Sair da Conta
                    </a>
                <?php else: ?>
                    <a href="login.php" class="block w-full text-center py-2.5 rounded-xl text-slate-300 hover:bg-slate-800 font-medium">Entrar</a>
                    <a href="cadastro.php" class="block w-full text-center py-2.5 rounded-xl bg-indigo-600 text-white font-semibold shadow-md">Cadastrar-se</a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <script>
        document.getElementById('mobile-menu-btn')?.addEventListener('click', () => {
            const menu = document.getElementById('mobile-menu');
            menu.classList.toggle('hidden');
        });
    </script>
