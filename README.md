# OPNsense Web UI

Uma interface administrativa web moderna, intuitiva e simplificada desenvolvida para se integrar diretamente à API do **OPNsense**. Este painel facilita a gestão de usuários, grupos, permissões e aliases do firewall, sendo ideal para ambientes corporativos e educacionais que demandam gerenciamento em lote e relatórios rápidos.

---

## 🚀 Tecnologias Utilizadas

O projeto foi construído utilizando a seguinte stack de desenvolvimento:

*   **Backend:** [Laravel 10](https://laravel.com/) (executando em PHP 8.1+)
*   **Integração de API:** [Guzzle HTTP Client](https://docs.guzzlephp.org/) para comunicação segura e de baixa latência com o appliance OPNsense.
*   **Frontend:** HTML5, [Tailwind CSS](https://tailwindcss.com/) (para design responsivo e moderno), [Alpine.js](https://alpinejs.dev/) (para reatividade leve no cliente) e [TomSelect](https://tom-select.js.org/) (para campos de seleção múltipla).
*   **Manipulação de Planilhas:** [PhpSpreadsheet](https://phpspreadsheet.readthedocs.io/) para leitura, escrita e geração de templates Excel (.xlsx).
*   **Geração de PDFs:** [DomPDF](https://github.com/barryvdh/laravel-dompdf) para conversão dinâmica de relatórios em arquivos PDF prontos para impressão.

---

## ✨ Funcionalidades Principais

### 1. Assistente de Configuração Inicial (Setup Wizard)
*   **Primeira Execução Automatizada:** Se a aplicação detectar que é a primeira vez que é executada (`APP_FIRST_RUN=true`), ela redireciona o usuário para o assistente de setup.
*   **Validação em Tempo de Execução:** O setup testa as credenciais informadas do OPNsense antes de finalizar, verificando se há comunicação e permissão adequadas.
*   **Banco de Dados & Admin Local:** Cria as tabelas do banco de dados e registra a conta de usuário administrador local.
*   **Redefinição Total do Sistema:** Rota administrativa segura (`/system/reset`) que limpa o banco de dados local e redefine a aplicação para o estado de primeira execução.

### 2. Dashboard Informativo
*   Estatísticas agregadas de **Usuários**, **Grupos** e **Aliases** cadastrados diretamente no OPNsense.
*   Indicador de status da conexão da API (Online/Offline).

### 3. Gerenciamento de Usuários
*   **CRUD de Usuários:** Interface para visualizar, cadastrar, atualizar e remover usuários no appliance OPNsense.
*   **Configurações Detalhadas:** Controle de Username (RA/Login), Nome Completo, E-mail, Senhas, Associação a múltiplos Grupos, Shell de Login (`/sbin/nologin` por padrão para segurança), Data de Expiração da conta e Chaves SSH Autorizadas (`authorizedkeys`).
*   **Metadados Inteligentes:** Armazena informações estruturadas (ex: RA, Tipo de Usuário, Códigos de Recuperação) no campo de comentários (`comment`/`descr`) do OPNsense, enriquecendo a pesquisa local sem alterar o esquema nativo do firewall.

### 4. Importação de Usuários em Lote (Planilhas Excel)
*   **Template Excel Padrão:** Importação simples por planilha contendo `RA`, `Nome completo` e indicação se o usuário deve ser importado ou ignorado. O sistema autogera uma senha forte e um código de recuperação para o usuário.
*   **Template Excel Padrão Faculdade (FATEC-style):**
    *   Leitura de planilhas com colunas `RA_Matricula`, `Nome`, `Grupo`, `Login`, `Senha` e `Importar`.
    *   Geração automática de logins no formato padrão (ex: `ad + RA` -> `ad12345`).
    *   Detecção inteligente de grupos inexistentes: se a planilha indicar um grupo que não existe no OPNsense, o sistema pode criá-lo automaticamente ou solicitar mapeamento manual.
*   **Relatório de Credenciais em PDF (Conformidade LGPD):**
    *   Após o processamento do lote, o painel disponibiliza um PDF formatado para impressão contendo os dados de acesso e códigos de redefinição criados.
    *   *Nota de Privacidade:* Seguindo os princípios da **LGPD**, essas credenciais temporárias são enviadas via buffer diretamente para o download do navegador e **nunca** são armazenadas no disco local do servidor.

### 5. Gerenciamento de Grupos
*   Criação, edição e exclusão de grupos diretamente no OPNsense.
*   Exibição automática da contagem de membros em cada grupo.
*   Exportação de relatórios com a lista de usuários de um determinado grupo em formato Excel/CSV.

### 6. Gerenciamento de Permissões
*   Mapeamento de privilégios de acesso do OPNsense GUI.
*   Atribuição rápida de conjuntos de privilégios a grupos existentes, automatizando a concessão de acessos.

### 7. Controle de Aliases de Firewall
*   Visualização e CRUD completo de Aliases no OPNsense.
*   Manipulação de IPs: Adicione ou remova dinamicamente endereços IP de um determinado alias em tempo real.
*   **Aplicar Regras:** Ação de "Reconfigurar" que aplica imediatamente as alterações na tabela de aliases do firewall OPNsense de forma assíncrona.

---

## 🛠️ Pré-requisitos

Para rodar a aplicação localmente, certifique-se de possuir:

*   **PHP** >= 8.1
*   **Composer** (Gerenciador de pacotes PHP)
*   **Node.js** & **NPM**
*   **Banco de Dados** (MySQL, MariaDB, PostgreSQL ou SQLite)
*   **OPNsense** com o serviço de API ativado e credenciais de API geradas.

---

## ⚙️ Instalação e Execução

Siga o passo a passo abaixo para configurar a aplicação em seu ambiente:

### 1. Clonar o projeto e instalar as dependências
```bash
# Clone o repositório
git clone https://github.com/seu-usuario/OPNsenseWebUi.git
cd OPNsenseWebUi

# Instale as dependências do PHP
composer install

# Instale as dependências do Frontend
npm install
```

### 2. Configurar o arquivo de variáveis de ambiente
Crie um arquivo `.env` baseado no modelo:
```bash
# No Windows PowerShell:
copy .env.example .env

# No Linux / macOS / Git Bash:
cp .env.example .env
```
Abra o arquivo `.env` e configure as credenciais do seu banco de dados local nas chaves `DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME` e `DB_PASSWORD`.

### 3. Gerar a chave da aplicação Laravel
```bash
php artisan key:generate
```

### 4. Executar e compilar assets
Abra um terminal e rode o compilador de assets (Vite):
```bash
npm run dev
```

### 5. Iniciar o servidor embutido do PHP/Laravel
Em outro terminal, execute o servidor de desenvolvimento:
```bash
php artisan serve
```
Acesse a aplicação no seu navegador pelo endereço fornecido (geralmente [http://127.0.0.1:8000](http://127.0.0.1:8000)).

---

## 🛡️ Configurando a API no OPNsense

Para que este painel consiga gerenciar o appliance OPNsense, você precisará gerar um par de chaves de API:

1.  Acesse o painel do seu OPNsense (`https://ip-do-seu-firewall`).
2.  Navegue em **System → Access → Users**.
3.  Edite o usuário que fará a gerência (ou crie um novo usuário para a API).
4.  Na seção **API Keys**, clique no botão **+** (Add).
5.  O OPNsense fará o download automático de um arquivo contendo a `key` (API Key) e o `secret` (API Secret).
6.  Ao abrir a aplicação pela primeira vez no navegador, o **Setup Wizard** solicitará a URL do OPNsense (ex: `https://192.168.1.1`), a API Key e o API Secret que você acabou de obter.

---

## 🔒 Segurança e LGPD

*   **Privacidade dos Dados:** Senhas e logins gerados em lote para importações educacionais ou corporativas aparecem apenas uma única vez na tela de confirmação e no PDF gerado. Nenhuma senha gerada em lote é salva em texto limpo no banco de dados local ou nos logs da aplicação.
*   **Comunicação Criptografada:** Todas as chamadas de API feitas entre a aplicação e o OPNsense devem idealmente utilizar HTTPS (embora a verificação SSL rígida esteja desativada por padrão no Guzzle para facilitar conexões com certificados autoassinados em redes locais, recomenda-se configurar certificados válidos em ambiente de produção).
