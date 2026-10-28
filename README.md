# 🎡 Roleta Digital

Um sistema web completo, responsivo e moderno de **Roleta Interativa**, desenvolvido de acordo com as especificações e regras de negócio do arquivo `requisitos.md`.

---

## 🚀 Tecnologias Utilizadas

- **Backend:** PHP 8+ estruturado com sessões nativas (`$_SESSION`)
- **Banco de Dados:** MySQL com **PDO** para segurança contra SQL Injections
- **Frontend:** HTML5, PHP, CSS3, **Tailwind CSS** (Mobile-First)
- **Efeitos e Sons:** HTML5 Canvas, Web Audio API nativa e Canvas Confetti

---

## 🎯 Atendimento aos Requisitos (CORE e Globais)

| Requisito | Como foi implementado |
| :--- | :--- |
| **Senhas com Hash Bcrypt** | Senhas criptografadas e verificadas exclusivamente com `password_hash($senha, PASSWORD_BCRYPT)` e `password_verify($senha, $hash)`. |
| **Página de Históricos & Auditoria** | Tela `historico.php` com visualização de todos os sorteios realizados e **tabela de auditoria** registrando ações (`CADASTRO_USUARIO`, `LOGIN_SUCESSO`, `LOGIN_FALHA`, `CRIAR_ROLETA`, `EDITAR_ROLETA`, `EXCLUIR_ROLETA`, `ALTERACAO_PLANO`, `SORTEIO_REALIZADO`), com data, IP, detalhes e usuário. |
| **Plano Gratuito** | Limite rígido de **no máximo 50 itens** na roleta e exibição de **anúncios patrocinados na lateral (`views/anuncios_lateral.php`) e no rodapé (`views/anuncios_bottom.php`)**. |
| **Plano Premium** | Itens **ilimitados** na roleta e **100% livre de anúncios**. Botão de upgrade/downgrade instantâneo em `planos.php` para testar ambos os planos. |
| **Segurança com PDO** | 100% das operações com o banco de dados utilizam conexões PDO e Prepared Statements parametrizados, prevenindo SQL Injections. |
| **Separação Lógica de Arquivos** | Sem monólitos. Conexão isolada em `bd.php`, lógica de auditoria em `helpers/logger.php`, controle de planos e sessões em `helpers/auth_helper.php`, endpoints assíncronos em `api/`, componentes visuais em `views/` e páginas estruturadas. |
| **Tailwind CSS & Mobile-First** | Interface responsiva projetada prioritariamente para smartphones e adaptável a tablets e desktops, com tema escuro elegante e toques modernos em degradê. |
| **Notificações em TOAST** | Sistema flutuante `showToast(msg, tipo)` tratando mensagens de sucesso, erro, alerta e informativos. Mensagens flash entre requisições são exibidas automaticamente em Toast. |
| **Modais para Caixas Nativas** | Substituição de diálogos nativos (`alert`, `confirm`, `prompt`) por modais elegantes em Tailwind CSS (`showAlertModal`, `showConfirmModal`, `showPromptModal`, e modal festivo de vencedor). |

---

## 📂 Estrutura de Diretórios

```
roleta-digital/
├── bd.php                    # Conexão central PDO MySQL
├── schema.sql                # Script de criação de tabelas e sementes iniciais
├── install.php               # Instalador web do banco de dados (1 clique)
├── index.php                 # Roleta interativa principal e presets
├── roleta.php                # Visualização/giro de roleta por ID
├── roletas.php               # Painel de gerenciamento das roletas do usuário
├── historico.php             # Histórico de sorteios e logs de auditoria
├── planos.php                # Comparativo Gratuito vs Premium com troca rápida
├── perfil.php                # Estatísticas e alteração de senha/dados
├── login.php                 # Autenticação com bcrypt
├── cadastro.php              # Registro de novos usuários com bcrypt
├── logout.php                # Logout seguro com log de auditoria
├── api/
│   ├── salvar_sorteio.php    # Registra giros e logs via PDO
│   ├── salvar_roleta.php     # Validação de limite de itens e persistência
│   ├── excluir_roleta.php    # Exclusão segura de roleta do usuário
│   ├── alterar_plano.php     # Troca de plano Gratuito <-> Premium
│   └── obter_roleta.php      # Dados da roleta em JSON
├── helpers/
│   ├── logger.php            # Registro permanente de logs de auditoria
│   └── auth_helper.php       # Sessões nativas e regras de plano
├── views/
│   ├── header.php            # Cabeçalho global responsivo
│   ├── footer.php            # Rodapé global
│   ├── anuncios_lateral.php  # Banner lateral (apenas plano gratuito)
│   ├── anuncios_bottom.php   # Banner inferior (apenas plano gratuito)
│   ├── modais.php            # Modais substitutos de alert/confirm/prompt
│   └── toasts.php            # Sistema visual de notificações Toast
├── assets/
│   ├── css/
│   │   └── style.css         # Estilizações complementares
│   └── js/
│       ├── wheel.js          # Motor da roleta Canvas com física e Web Audio
│       ├── ui-modals.js      # Gerenciador assíncrono de modais
│       └── app.js            # Interações, importações em massa e CRUD
├── requisitos.md             # Requisitos originais
├── README.md                 # Esta documentação
└── LICENSE                   # Licença MIT
```

---

## 🛠️ Como Executar o Projeto

### Requisitos:
- Servidor web com **PHP 7.4+** ou **PHP 8.x**
- Banco de dados **MySQL** ou **MariaDB**
- (Opcional) XAMPP, WampServer, Laragon ou o servidor embutido do PHP

---

### Passo 1: Configurar o Banco de Dados

1. Certifique-se de que o serviço MySQL está em execução.
2. Você pode instalar o banco de duas maneiras:
   - **Opção A (Via Navegador - Mais Fácil):** Acesse `http://localhost/roleta%20digital/install.php` e clique no botão **"Criar Banco e Tabelas Agora"**.
   - **Opção B (Via phpMyAdmin ou Terminal MySQL):** Importe o arquivo `schema.sql`.

As credenciais padrão em `bd.php` são:
- **Host:** `localhost`
- **Banco:** `roleta_digital`
- **Usuário:** `root`
- **Senha:** *(vazio)*

*(Você também pode configurar através das variáveis de ambiente `DB_HOST`, `DB_NAME`, `DB_USER` e `DB_PASS`).*

---

### Passo 2: Iniciar o Servidor Web

Se estiver utilizando o servidor embutido do PHP, abra o terminal na pasta do projeto e execute:

```bash
php -S localhost:8000
```

E abra no navegador: `http://localhost:8000`

---

## 👥 Contas de Demonstração Pré-Cadastradas

O sistema já vem com duas contas de teste prontas com senha protegida por bcrypt para você testar imediatamente as diferenças entre os planos:

1. **Conta Plano Gratuito:**
   - **E-mail:** `demo@gratuito.com`
   - **Senha:** `123456`
   - *Comportamento:* Exibe anúncios na lateral e no rodapé; limita a no máximo 50 itens por roleta.

2. **Conta Plano Premium VIP:**
   - **E-mail:** `premium@roleta.com`
   - **Senha:** `123456`
   - *Comportamento:* 100% zero anúncios; itens ilimitados na roleta.

*(Na própria tela de login há botões de 1 clique para preencher automaticamente essas contas).*

---

## 🔒 Segurança e Boas Práticas

- **SQL Injection Prevention:** Utilização rigorosa de `PDO::prepare` e `execute` com parâmetros nomeados.
- **XSS Prevention:** Sanitização com `htmlspecialchars` na renderização de dados de usuários.
- **Auditoria Total:** Cada ação no sistema gera um registro com carimbo de data/hora, IP e payload em JSON na tabela `logs_auditoria`.
