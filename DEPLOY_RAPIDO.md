# ⚡ Deploy Rápido - Hostinger

## 🚀 Passos Rápidos (Resumo)

### 1. Preparação Local

**Windows:**
```cmd
deploy-hostinger.bat
```

**Linux/Mac:**
```bash
chmod +x deploy-hostinger.sh
./deploy-hostinger.sh
```

### 2. Upload para Hostinger

1. Acesse **hPanel** → **File Manager**
2. Vá em `public_html`
3. Delete arquivos padrão
4. Faça upload do ZIP gerado
5. Extraia o ZIP

### 3. Configurar Banco de Dados

1. **hPanel** → **MySQL Databases**
2. Crie banco e usuário
3. Anote credenciais

### 4. Configurar .env

1. No File Manager, vá para raiz (fora de `public_html`)
2. Crie/edite `.env` com:
   - Credenciais do banco
   - `APP_URL=https://seudominio.com.br`
   - `APP_DEBUG=false`
   - Chaves de API (Mercado Pago, Abacate Pay, etc.)

### 5. Configurar Permissões

Via File Manager:
- `storage` → Permissões: **755**
- `bootstrap/cache` → Permissões: **755**

### 6. Criar Link Simbólico

**Se tiver SSH:**
```bash
php artisan storage:link
```

**Se não tiver SSH:**
- No File Manager, crie link simbólico:
  - Origem: `../storage/app/public`
  - Destino: `public_html/storage`

### 7. Otimizar

1. Faça upload de `optimize-production.php` para a raiz
2. Acesse: `https://seudominio.com.br/optimize-production.php`
3. **Delete o arquivo** após executar!

### 8. Verificar

- [ ] Site carrega: `https://seudominio.com.br`
- [ ] Admin funciona: `https://seudominio.com.br/admin`
- [ ] Checkout funciona
- [ ] Upload de imagens funciona

---

## 📝 Checklist Rápido

- [ ] Assets compilados (`npm run build`)
- [ ] ZIP criado e enviado
- [ ] `.env` configurado
- [ ] Banco criado e migrations executadas
- [ ] Permissões configuradas (755)
- [ ] Link simbólico criado
- [ ] Cache otimizado
- [ ] SSL/HTTPS ativo
- [ ] `APP_DEBUG=false`

---

## 🆘 Problemas Comuns

### Erro 500
- Verifique permissões de `storage` e `bootstrap/cache`
- Verifique `.env` (todas variáveis corretas?)
- Verifique logs: `storage/logs/laravel.log`

### Assets não carregam
- Verifique se `npm run build` foi executado
- Verifique se `public/build/` foi enviado

### Banco não conecta
- Host deve ser `localhost` (não `127.0.0.1`)
- Verifique credenciais no `.env`

---

📖 **Guia completo**: Veja `GUIA_DEPLOY_HOSTINGER.md` para detalhes.

