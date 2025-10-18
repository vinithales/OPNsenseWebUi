-- Script SQL para testar a funcionalidade de importação
-- Execute este script após rodar as migrations

-- Exemplo de inserção manual de usuários com os novos campos
-- ATENÇÃO: A senha 'teste123' está hasheada com bcrypt

-- Usuário Aluno de Teste
INSERT INTO users (name, email, password, ra, user_type, status, created_at, updated_at)
VALUES (
    'joao.silva',
    'joao.silva@escola.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- senha: password
    '123456',
    'aluno',
    'ativo',
    NOW(),
    NOW()
);

-- Usuário Professor de Teste
INSERT INTO users (name, email, password, ra, user_type, status, created_at, updated_at)
VALUES (
    'maria.santos',
    'maria.santos@escola.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- senha: password
    '789012',
    'professor',
    'ativo',
    NOW(),
    NOW()
);

-- Verificar os usuários criados
SELECT id, name, email, ra, user_type, status FROM users;

-- Consultas úteis para monitoramento

-- 1. Contar usuários por tipo
SELECT user_type, COUNT(*) as total
FROM users
GROUP BY user_type;

-- 2. Contar usuários por status
SELECT status, COUNT(*) as total
FROM users
GROUP BY status;

-- 3. Listar alunos ativos
SELECT name, email, ra, created_at
FROM users
WHERE user_type = 'aluno' AND status = 'ativo'
ORDER BY created_at DESC;

-- 4. Listar professores
SELECT name, email, ra, created_at
FROM users
WHERE user_type = 'professor'
ORDER BY name;

-- 5. Verificar RAs duplicados (não deveria haver)
SELECT ra, COUNT(*) as duplicates
FROM users
WHERE ra IS NOT NULL
GROUP BY ra
HAVING COUNT(*) > 1;
