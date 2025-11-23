# 🚀 Alternativas ao ngrok para Testar Mercado Pago Localmente

## ⚠️ Importante

O Mercado Pago **precisa** de uma URL pública acessível para fazer callbacks. Não é possível testar completamente sem um túnel público. Mas existem alternativas mais simples que o ngrok!

---

## ✅ Opção 1: localtunnel (Mais Simples - Recomendado)

### Vantagens:
- ✅ Não precisa criar conta
- ✅ Mais fácil de usar
- ✅ Gratuito
- ✅ Funciona imediatamente

### Como usar:

#### 1. Instalar:
```bash
npm install -g localtunnel
```

#### 2. Iniciar túnel:

**Se estiver usando Apache (porta 80):**
```bash
lt --port 80
```

**OU se estiver usando Laravel (porta 8000):**
```bash
lt --port 8000
```

#### 3. Você receberá uma URL como:
```
your url is: https://random-name.loca.lt
```

#### 4. Configurar no .env:
```env
APP_URL=https://random-name.loca.lt
```

#### 5. Limpar cache:
```bash
php artisan config:clear
```

#### 6. Pronto! Use essa URL para testar.

**Nota:** A URL muda a cada vez que você reinicia. Se quiser URL fixa, use: `lt --port 80 --subdomain meu-nome`

---

## ✅ Opção 2: Cloudflare Tunnel (cloudflared)

### Vantagens:
- ✅ Gratuito
- ✅ Sem limite de tempo
- ✅ Mais estável

### Como usar:

#### 1. Baixar:
Acesse: https://github.com/cloudflare/cloudflared/releases
Baixe para Windows e extraia

#### 2. Usar:
```bash
# Navegue até a pasta do cloudflared
cloudflared tunnel --url http://localhost:80

# OU
cloudflared tunnel --url http://localhost:8000
```

#### 3. Você receberá uma URL como:
```
https://random-name.trycloudflare.com
```

#### 4. Configurar no .env:
```env
APP_URL=https://random-name.trycloudflare.com
```

---

## ✅ Opção 3: serveo.net (Sem Instalação)

### Vantagens:
- ✅ Não precisa instalar nada
- ✅ Usa SSH (já vem no Windows 10+)

### Como usar:

#### Windows (PowerShell):
```powershell
ssh -R 80:localhost:80 serveo.net
```

#### Você receberá uma URL como:
```
Forwarding HTTP traffic from https://random-name.serveo.net
```

#### Configurar no .env:
```env
APP_URL=https://random-name.serveo.net
```

**Nota:** Pode ser instável às vezes.

---

## ✅ Opção 4: localhost.run (SSH também)

### Como usar:
```bash
ssh -R 80:localhost:80 ssh.localhost.run
```

Você receberá uma URL pública.

---

## 🎯 Recomendação: Use localtunnel

É a opção mais simples e confiável. Siga estes passos:

### Passo a Passo Completo:

#### 1. Verificar se tem Node.js:
```bash
node --version
```

Se não tiver, instale: https://nodejs.org/

#### 2. Instalar localtunnel:
```bash
npm install -g localtunnel
```

#### 3. Verificar qual servidor está rodando:

**Opção A: Apache do XAMPP (porta 80)**
- Abra XAMPP Control Panel
- Verifique se Apache está "Running"
- Use: `lt --port 80`

**Opção B: Laravel (porta 8000)**
- Pare o `php artisan serve` se estiver rodando
- Inicie: `php artisan serve --host=0.0.0.0 --port=8000`
- Use: `lt --port 8000`

#### 4. Iniciar localtunnel:
```bash
lt --port 80
```

Você verá:
```
your url is: https://random-name.loca.lt
```

#### 5. Configurar .env:
```env
APP_URL=https://random-name.loca.lt
```

#### 6. Limpar cache:
```bash
php artisan config:clear
```

#### 7. Testar:
- Acesse: `https://random-name.loca.lt`
- Se funcionar, teste o checkout com Mercado Pago!

---

## 🔧 Configuração Completa do Apache (Se usar porta 80)

Se escolher usar Apache, configure o Virtual Host:

### 1. Editar: `C:\xampp\apache\conf\extra\httpd-vhosts.conf`

Adicione:
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

### 2. Editar: `C:\xampp\apache\conf\httpd.conf`

Procure e descomente (remova o #):
```apache
Include conf/extra/httpd-vhosts.conf
```

### 3. Reiniciar Apache no XAMPP Control Panel

### 4. Testar: `http://localhost`

---

## 📝 Resumo Rápido (localtunnel)

```bash
# 1. Instalar
npm install -g localtunnel

# 2. Iniciar (escolha uma porta)
lt --port 80        # Apache
# OU
lt --port 8000     # Laravel

# 3. Copiar URL (ex: https://abc123.loca.lt)

# 4. Configurar .env
APP_URL=https://abc123.loca.lt

# 5. Limpar cache
php artisan config:clear

# 6. Testar!
```

---

## ⚠️ Importante

- **Sempre use HTTPS** nas URLs (não HTTP)
- **Atualize APP_URL** sempre que reiniciar o túnel
- **Limpe o cache** após mudar APP_URL
- **Teste localmente primeiro** antes de usar o túnel

---

## 🐛 Troubleshooting

### "lt: command not found"
- Instale Node.js: https://nodejs.org/
- Reinstale: `npm install -g localtunnel`

### "Port already in use"
- Use outra porta ou pare o servidor que está usando a porta

### "Connection refused"
- Verifique se o servidor está rodando
- Teste acessar `http://localhost` primeiro

---

## 🎉 Pronto!

Agora você tem uma alternativa simples ao ngrok. Use **localtunnel** - é a mais fácil!


