# ✅ Checklist de Testes - Importação de Usuários

## 📋 Antes de Testar

- [ ] Migrations executadas (`php artisan migrate`)
- [ ] Dependências instaladas (composer packages)
- [ ] Servidor web rodando
- [ ] Banco de dados configurado
- [ ] Acesso ao sistema como administrador

---

## 🧪 Testes Funcionais

### 1. Template Excel

#### 1.1 Download do Template
- [ ] Acessar `/users/import`
- [ ] Clicar em "Baixar Template Excel"
- [ ] Arquivo baixado com nome `template_importacao_usuarios_*.xlsx`
- [ ] Arquivo abre corretamente no Excel/LibreOffice
- [ ] Colunas presentes: RA e E-mail
- [ ] Linhas de exemplo presentes

#### 1.2 Validação do Template
- [ ] Cabeçalhos em negrito e coloridos
- [ ] Largura das colunas adequada
- [ ] Exemplos de dados nas linhas 2 e 3

---

### 2. Importação Básica

#### 2.1 Importação Bem-Sucedida
**Dados de teste:**
```
RA      | E-mail
111111  | aluno1@teste.com
222222  | aluno2@teste.com
333333  | professor1@teste.com
```

- [ ] Upload do arquivo
- [ ] Selecionar tipo "Aluno" para os 2 primeiros
- [ ] Mensagem de sucesso exibida
- [ ] Contador mostra "2 usuários importados"
- [ ] Botão "Baixar PDF" aparece
- [ ] Repetir para tipo "Professor"

#### 2.2 Verificação no Banco
```sql
SELECT ra, email, user_type, status FROM users WHERE ra IN ('111111', '222222', '333333');
```
- [ ] 3 registros encontrados
- [ ] Tipos corretos (aluno/professor)
- [ ] Status = 'ativo'
- [ ] Senhas criptografadas (começam com $2y$)

---

### 3. Validações de Erro

#### 3.1 RA Duplicado
**Teste:** Importar novamente o RA 111111
- [ ] Erro exibido: "RA 111111 já existe no sistema"
- [ ] Usuário NÃO é criado
- [ ] Outros usuários do arquivo são processados normalmente

#### 3.2 E-mail Duplicado
**Teste:** Importar e-mail já existente
- [ ] Erro exibido: "E-mail já existe no sistema"
- [ ] Usuário NÃO é criado

#### 3.3 E-mail Inválido
**Dados de teste:**
```
RA      | E-mail
444444  | email-invalido
555555  | teste@
666666  | @dominio.com
```
- [ ] Erro: "E-mail inválido ou vazio"
- [ ] Linha específica do erro mostrada

#### 3.4 RA Vazio
**Dados de teste:**
```
RA      | E-mail
        | teste@teste.com
```
- [ ] Erro: "RA não pode estar vazio"
- [ ] Linha específica do erro mostrada

#### 3.5 Arquivo Muito Grande
**Teste:** Upload de arquivo > 5MB
- [ ] Erro de validação
- [ ] Mensagem clara sobre limite de tamanho

#### 3.6 Formato Incorreto
**Teste:** Upload de arquivo .txt ou .pdf
- [ ] Erro: formato não aceito
- [ ] Apenas .xlsx e .xls permitidos

---

### 4. Geração de PDF

#### 4.1 Download do PDF
- [ ] Após importação bem-sucedida, clicar em "Baixar PDF"
- [ ] Arquivo baixado: `credenciais_usuarios_*.pdf`
- [ ] PDF abre corretamente

#### 4.2 Conteúdo do PDF
- [ ] Cabeçalho com título e data
- [ ] Aviso de confidencialidade presente
- [ ] Total de usuários correto
- [ ] Para cada usuário:
  - [ ] RA exibido
  - [ ] E-mail exibido
  - [ ] Senha em texto simples exibida
  - [ ] Badge de tipo (Aluno/Professor) colorido
  - [ ] Aviso para trocar senha
- [ ] Rodapé com informações de suporte

#### 4.3 Segurança LGPD
- [ ] Tentar baixar PDF novamente (deve dar erro)
- [ ] Mensagem: "Nenhuma credencial disponível"
- [ ] Verificar que credenciais não ficaram na sessão
- [ ] PDF não fica armazenado em `/storage/app`

---

### 5. Cadastro Manual

#### 5.1 Criar Aluno Manualmente
**Dados:**
- Nome: João da Silva
- Login: joao.silva
- RA: 777777
- E-mail: joao@teste.com
- Tipo: Aluno
- Senha: Teste@123

- [ ] Formulário preenchido
- [ ] Campo RA visível
- [ ] Campo Tipo de Usuário visível
- [ ] Mensagem de sucesso
- [ ] Usuário aparece na listagem

#### 5.2 Validações do Formulário
- [ ] Nome obrigatório
- [ ] E-mail obrigatório e válido
- [ ] RA único (tentar usar 777777 novamente)
- [ ] Senha mínima de 8 caracteres
- [ ] Confirmação de senha deve coincidir

---

### 6. Listagem e Filtros

#### 6.1 Filtro por Tipo
- [ ] Acessar `/users`
- [ ] Selecionar filtro "Alunos"
- [ ] Apenas alunos são exibidos
- [ ] Selecionar "Professores"
- [ ] Apenas professores são exibidos
- [ ] Selecionar "Todos os Tipos"
- [ ] Todos usuários são exibidos

#### 6.2 Filtro por Status
- [ ] Filtrar "Ativos"
- [ ] Filtrar "Inativos"
- [ ] Filtrar "Bloqueados"
- [ ] Filtrar "Todos os Status"

#### 6.3 Busca
- [ ] Buscar por nome
- [ ] Buscar por e-mail
- [ ] Buscar por RA
- [ ] Resultados filtrados corretamente

---

### 7. Senhas Geradas

#### 7.1 Qualidade das Senhas
**Verificar no PDF que as senhas contêm:**
- [ ] Pelo menos 10 caracteres
- [ ] Letra maiúscula
- [ ] Letra minúscula
- [ ] Número
- [ ] Caractere especial (!@#$%&*)

#### 7.2 Unicidade
**Importar 10 usuários e verificar PDF:**
- [ ] Todas as 10 senhas são diferentes
- [ ] Nenhuma senha se repete

---

### 8. Testes de Performance

#### 8.1 Importação de Volume Médio (50 usuários)
- [ ] Preparar Excel com 50 usuários
- [ ] Importar
- [ ] Tempo < 10 segundos
- [ ] Todos usuários criados
- [ ] PDF gerado com sucesso

#### 8.2 Importação de Volume Alto (200 usuários)
- [ ] Preparar Excel com 200 usuários
- [ ] Importar
- [ ] Tempo < 30 segundos
- [ ] Todos usuários criados
- [ ] PDF gerado (pode ser grande)

---

### 9. Testes de Interface

#### 9.1 Responsividade
- [ ] Testar em desktop (1920x1080)
- [ ] Testar em tablet (768x1024)
- [ ] Testar em mobile (375x667)
- [ ] Todos elementos visíveis e funcionais

#### 9.2 Navegação
- [ ] Botão "Voltar" funciona
- [ ] Breadcrumbs (se houver) funcionam
- [ ] Links entre páginas funcionam
- [ ] Mensagens de sucesso/erro aparecem

---

### 10. Testes de Integração

#### 10.1 Fluxo Completo
**Cenário: Início de semestre**
1. [ ] Baixar template
2. [ ] Preencher com 10 alunos
3. [ ] Importar
4. [ ] Baixar PDF
5. [ ] Verificar na listagem
6. [ ] Filtrar por "Alunos"
7. [ ] Buscar por um RA específico
8. [ ] Editar um usuário (se função existir)
9. [ ] Desativar um usuário (se função existir)

---

## 🐛 Testes de Casos Extremos

### 11. Edge Cases

#### 11.1 Excel com Linhas Vazias
```
RA      | E-mail
111111  | teste1@teste.com
        |                    <- linha vazia
222222  | teste2@teste.com
```
- [ ] Linha vazia é ignorada
- [ ] Outros usuários são importados

#### 11.2 Caracteres Especiais no RA
```
RA        | E-mail
ABC-123   | teste@teste.com
RA/456    | teste2@teste.com
```
- [ ] RAs com caracteres especiais são aceitos (ou rejeitados conforme regra)

#### 11.3 E-mail com Domínio Diferente
```
RA      | E-mail
111111  | teste@gmail.com
222222  | teste@hotmail.com
333333  | teste@institucional.edu
```
- [ ] Todos domínios são aceitos (ou verificar se há validação)

---

## 🔒 Testes de Segurança

### 12. Segurança

#### 12.1 SQL Injection
**Teste:** Tentar importar RA com SQL
```
RA                           | E-mail
'; DROP TABLE users; --      | teste@teste.com
```
- [ ] RA é tratado como string
- [ ] Nenhum comando SQL é executado
- [ ] Erro de validação ou importação segura

#### 12.2 XSS
**Teste:** Tentar importar script no e-mail
```
RA      | E-mail
111111  | <script>alert('XSS')</script>@teste.com
```
- [ ] Script não é executado
- [ ] E-mail é escapado ou validado

#### 12.3 CSRF
- [ ] Verificar presença de token CSRF em formulários
- [ ] Tentar submeter sem token (deve falhar)

#### 12.4 Upload Malicioso
**Teste:** Renomear arquivo .exe para .xlsx
- [ ] Upload rejeitado
- [ ] Sistema valida conteúdo, não apenas extensão

---

## 📊 Logs e Monitoramento

### 13. Verificação de Logs

#### 13.1 Importação Bem-Sucedida
```bash
tail -f storage/logs/laravel.log
```
- [ ] Log: "Usuário importado com sucesso"
- [ ] RA e e-mail registrados
- [ ] Sem erros

#### 13.2 Importação com Erros
- [ ] Log: "Erro ao importar usuário"
- [ ] Detalhes do erro registrados
- [ ] Linha e dados problemáticos identificados

---

## 🎯 Checklist de Aceitação

### Critérios de Aceitação Final
- [ ] Todas as funcionalidades descritas foram implementadas
- [ ] Nenhum erro crítico encontrado
- [ ] Performance aceitável (< 10s para 50 usuários)
- [ ] Interface intuitiva e responsiva
- [ ] Documentação completa
- [ ] Validações de segurança funcionando
- [ ] LGPD compliance verificado
- [ ] Testes em diferentes navegadores:
  - [ ] Chrome
  - [ ] Firefox
  - [ ] Edge
  - [ ] Safari (se disponível)

---

## 📝 Registro de Testes

### Template de Registro
```
Data: __/__/____
Testador: _____________
Ambiente: [ ] Dev [ ] Staging [ ] Produção

Resumo:
- Testes executados: ___/85
- Testes com sucesso: ___
- Testes com falha: ___
- Bugs encontrados: ___

Bugs Críticos:
1. _______________________________
2. _______________________________

Observações:
_________________________________
_________________________________
```

---

## 🚀 Próximos Passos Após Testes

### Se todos os testes passarem:
1. ✅ Marcar como "Pronto para Produção"
2. 📋 Criar release notes
3. 👥 Treinar usuários finais
4. 📊 Configurar monitoramento
5. 🎉 Deploy em produção

### Se houver falhas:
1. 🐛 Documentar bugs encontrados
2. 🔧 Priorizar correções
3. 🔄 Corrigir e re-testar
4. ✅ Validar novamente

---

**Boa sorte com os testes! 🚀**
