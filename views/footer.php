<?php
/**
 * Rodapé Global do Sistema
 */
?>
    <!-- Rodapé -->
    <footer class="mt-auto border-t border-slate-800/80 bg-slate-900/60 py-8 text-slate-400 text-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>Roleta Digital &copy; <?= date('Y') ?> - Todos os direitos reservados.</span>
            </div>
            <div class="flex items-center gap-6 text-slate-400">
                <a href="index.php" class="hover:text-white transition">Roleta</a>
                <a href="historico.php" class="hover:text-white transition">Histórico</a>
                <a href="planos.php" class="hover:text-white transition">Planos</a>
                <a href="install.php" class="text-indigo-400 hover:text-indigo-300 transition">Instalador BD</a>
            </div>
        </div>
    </footer>

    <!-- Inclusão dos Modais Customizados (Substitutos de alert, confirm, prompt) -->
    <?php require_once __DIR__ . '/modais.php'; ?>

    <!-- Inclusão dos Toasts de Notificação -->
    <?php require_once __DIR__ . '/toasts.php'; ?>

    <!-- Script de Modais Dinâmicos -->
    <script src="assets/js/ui-modals.js"></script>
</body>
</html>
