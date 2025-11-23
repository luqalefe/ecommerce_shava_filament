# 🔧 Solução: ngrok Offline Error

## Problema Identificado

Você tem dois servidores rodando:
- **Porta 80**: Apache do XAMPP ✅
- **Porta 8000**: Laravel (php artisan serve) ⚠️ (escutando apenas em localhost)

## ✅ Solução 1: Usar Apache do XAMPP (Recomendado)

### Passo 1: Verificar se o Apache está rodando
- Abra o XAMPP Control Panel
- Verifique se o Apache está com status "Running"

### Passo 2: Configurar Virtual Host (Opcional mas Recomendado)

1. Abra o arquivo `C:\xampp\apache\conf\extra\httpd-vhosts.conf`

2. Adicione esta configuração:

```apache
<VirtualHost *:80>
    DocumentRoot "C:/xampp/htdocs/ecommerce_shava/public"
    ServerName localhost
    <Directory "C:/xampp/htdocs/ecommerce_shava/public">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

3. Reinicie o Apache no XAMPP Control Panel

### Passo 3: Iniciar ngrok apontando para porta 80

```bash
ngrok http 80
```

### Passo 4: Configurar APP_URL

Copie a URL HTTPS do ngrok e configure no `.env`:

```env
APP_URL=https://sua-url-ngrok.ngrok-free.app
```

### Passo 5: Limpar cache

```bash
php artisan config:clear
```

### Passo 6: Acessar

Acesse: `https://sua-url-ngrok.ngrok-free.app`

---

## ✅ Solução 2: Usar Laravel na porta 8000 (com correção)

### Passo 1: Parar o servidor atual

Pressione `Ctrl+C` no terminal onde está rodando `php artisan serve`

### Passo 2: Iniciar servidor escutando em todas as interfaces

```bash
php artisan serve --host=0.0.0.0 --port=8000
```

Isso faz o servidor escutar em `0.0.0.0` (todas as interfaces) em vez de apenas `127.0.0.1`

### Passo 3: Iniciar ngrok

Em outro terminal:

```bash
ngrok http 8000
```

### Passo 4: Configurar APP_URL

```env
APP_URL=https://sua-url-ngrok.ngrok-free.app
```

### Passo 5: Limpar cache

```bash
php artisan config:clear
```

---

## 🔍 Verificar se está funcionando

### Teste 1: Acessar localmente
- Apache: `http://localhost`
- Laravel: `http://localhost:8000`

### Teste 2: Acessar via ngrok
- `https://sua-url-ngrok.ngrok-free.app`

Se ambos funcionarem, está tudo certo!

---

## ⚠️ Troubleshooting

### Erro: "ngrok endpoint offline"

**Causas possíveis:**
1. Servidor não está rodando
2. Porta incorreta no ngrok
3. Firewall bloqueando

**Soluções:**
1. Verifique se o servidor está rodando:
   ```bash
   netstat -ano | findstr :80
   netstat -ano | findstr :8000
   ```

2. Use a porta correta no ngrok:
   - Apache: `ngrok http 80`
   - Laravel: `ngrok http 8000`

3. Verifique o firewall do Windows:
   - Permita conexões na porta 80 ou 8000

### Erro: "502 Bad Gateway"

**Causa:** ngrok conectou mas o servidor não responde

**Solução:**
- Verifique se o servidor está realmente rodando
- Teste acessar `http://localhost` ou `http://localhost:8000` diretamente
- Reinicie o servidor

### Erro: "404 Not Found"

**Causa:** DocumentRoot incorreto no Apache

**Solução:**
- Configure o Virtual Host apontando para `public` do Laravel
- Ou acesse diretamente: `http://localhost/public`

---

## 📝 Checklist Rápido

- [ ] Servidor rodando (Apache ou Laravel)
- [ ] ngrok rodando na porta correta
- [ ] URL HTTPS copiada do ngrok
- [ ] APP_URL configurado no .env
- [ ] Cache limpo (`php artisan config:clear`)
- [ ] Testado acesso local
- [ ] Testado acesso via ngrok

---

## 🚀 Próximos Passos

Após resolver:
1. Teste o checkout completo
2. Use cartões de teste do Mercado Pago
3. Verifique os logs em `storage/logs/laravel.log`


