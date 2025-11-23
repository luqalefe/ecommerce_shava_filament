-- ============================================
-- SQL para criar usuário Admin
-- Email: luqalefe@gmail.com
-- Senha: 12345678 (temporária - altere após login)
-- ============================================

INSERT INTO `users` (
    `name`,
    `email`,
    `password`,
    `is_admin`,
    `role`,
    `email_verified_at`,
    `created_at`,
    `updated_at`
) VALUES (
    'Lucas Admin',
    'luqalefe@gmail.com',
    '$2y$10$NgzSJ3pSKB2oElHdfSpof.tDjweiaD82P3JZxPnUQEpEQUAzMnQCq',
    1,
    'admin',
    NOW(),
    NOW(),
    NOW()
);

-- ============================================
-- Verificar se foi criado corretamente:
-- SELECT id, name, email, is_admin, role FROM users WHERE email = 'luqalefe@gmail.com';
-- ============================================

