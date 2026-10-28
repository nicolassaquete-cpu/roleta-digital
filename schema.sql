-- Script DDL para criação da base de dados e tabelas da Roleta Digital
-- Banco de dados: MySQL com suporte a UTF-8 (utf8mb4)

CREATE DATABASE IF NOT EXISTS `roleta_digital` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `roleta_digital`;

-- 1. Tabela de Usuários
-- Senhas tratadas com hash bcrypt conforme Requisito CORE
CREATE TABLE IF NOT EXISTS `usuarios` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `nome` VARCHAR(100) NOT NULL,
    `email` VARCHAR(150) NOT NULL UNIQUE,
    `senha` VARCHAR(255) NOT NULL,
    `plano` ENUM('gratuito', 'premium') NOT NULL DEFAULT 'gratuito',
    `criado_em` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `atualizado_em` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_email` (`email`),
    INDEX `idx_plano` (`plano`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Tabela de Roletas
CREATE TABLE IF NOT EXISTS `roletas` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `usuario_id` INT NULL,
    `titulo` VARCHAR(150) NOT NULL,
    `descricao` TEXT NULL,
    `criado_em` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `atualizado_em` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`usuario_id`) REFERENCES `usuarios`(`id`) ON DELETE CASCADE,
    INDEX `idx_usuario` (`usuario_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Tabela de Itens da Roleta
CREATE TABLE IF NOT EXISTS `itens_roleta` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `roleta_id` INT NOT NULL,
    `texto` VARCHAR(255) NOT NULL,
    `cor` VARCHAR(30) NOT NULL DEFAULT '#6366f1',
    `peso` INT NOT NULL DEFAULT 1,
    `criado_em` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`roleta_id`) REFERENCES `roletas`(`id`) ON DELETE CASCADE,
    INDEX `idx_roleta` (`roleta_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Tabela de Histórico de Sorteios
-- Mantém o histórico dos giros realizados
CREATE TABLE IF NOT EXISTS `historico_sorteios` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `usuario_id` INT NULL,
    `roleta_id` INT NULL,
    `item_sorteado` VARCHAR(255) NOT NULL,
    `criado_em` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`usuario_id`) REFERENCES `usuarios`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`roleta_id`) REFERENCES `roletas`(`id`) ON DELETE SET NULL,
    INDEX `idx_sorteio_usuario` (`usuario_id`),
    INDEX `idx_sorteio_data` (`criado_em`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Tabela de Logs de Auditoria
-- Requisito CORE: "manter sempre os logs de qualquer alteração feita por qualquer usuário, para auditorias futuras"
CREATE TABLE IF NOT EXISTS `logs_auditoria` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `usuario_id` INT NULL,
    `usuario_nome` VARCHAR(100) NULL,
    `acao` VARCHAR(100) NOT NULL,
    `detalhes` TEXT NULL,
    `ip` VARCHAR(45) NULL,
    `criado_em` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_auditoria_usuario` (`usuario_id`),
    INDEX `idx_auditoria_acao` (`acao`),
    INDEX `idx_auditoria_data` (`criado_em`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------
-- DADOS INICIAIS DE DEMONSTRAÇÃO
-- ----------------------------------------------------

-- Usuário Padrão Gratuito (Senha: '123456' com bcrypt)
INSERT IGNORE INTO `usuarios` (`id`, `nome`, `email`, `senha`, `plano`)
VALUES (1, 'Usuário Gratuito', 'demo@gratuito.com', '$2y$10$wT0lQo.uT80J4.GzGZtwIeF4jLzR2M9.N3z0bV4yZ9gVlZz9G9kG2', 'gratuito');

-- Usuário Premium (Senha: '123456' com bcrypt)
INSERT IGNORE INTO `usuarios` (`id`, `nome`, `email`, `senha`, `plano`)
VALUES (2, 'Usuário VIP Premium', 'premium@roleta.com', '$2y$10$wT0lQo.uT80J4.GzGZtwIeF4jLzR2M9.N3z0bV4yZ9gVlZz9G9kG2', 'premium');

-- Roleta de Demonstração 1: O que comer hoje?
INSERT IGNORE INTO `roletas` (`id`, `usuario_id`, `titulo`, `descricao`)
VALUES (1, 1, 'O que comer hoje?', 'Roleta para decidir o almoço ou jantar de forma rápida');

-- Itens da Roleta 1
INSERT IGNORE INTO `itens_roleta` (`roleta_id`, `texto`, `cor`, `peso`) VALUES
(1, 'Pizza Margherita', '#ef4444', 1),
(1, 'Hambúrguer Artesanal', '#f97316', 1),
(1, 'Sushi / Comida Japonesa', '#10b981', 1),
(1, 'Churrasco', '#8b5cf6', 1),
(1, 'Comida Mexicana (Tacos)', '#ec4899', 1),
(1, 'Salada Caesar Fitness', '#06b6d4', 1),
(1, 'Massa Italiana', '#eab308', 1),
(1, 'Pastel & Caldo de Cana', '#6366f1', 1);

-- Roleta de Demonstração 2: Decisão Rápida
INSERT IGNORE INTO `roletas` (`id`, `usuario_id`, `titulo`, `descricao`)
VALUES (2, 1, 'Decisão Rápida: Sim ou Não', 'Ideal para tirar dúvidas simples e objetivas');

-- Itens da Roleta 2
INSERT IGNORE INTO `itens_roleta` (`roleta_id`, `texto`, `cor`, `peso`) VALUES
(2, 'SIM, com certeza!', '#22c55e', 1),
(2, 'NÃO, de jeito nenhum!', '#ef4444', 1),
(2, 'Tente de novo mais tarde', '#eab308', 1),
(2, 'Talvez, avalie melhor', '#3b82f6', 1);

-- Registro inicial de log de auditoria
INSERT IGNORE INTO `logs_auditoria` (`usuario_id`, `usuario_nome`, `acao`, `detalhes`, `ip`)
VALUES (1, 'Sistema', 'SISTEMA_INSTALADO', 'Base de dados inicializada com sucesso e registros padrão carregados', '127.0.0.1');
