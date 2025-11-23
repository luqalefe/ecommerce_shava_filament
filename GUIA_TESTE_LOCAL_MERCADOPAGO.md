# 🧪 Guia: Testando Mercado Pago em Desenvolvimento Local (XAMPP)

## 📋 Problema

O Mercado Pago precisa acessar URLs públicas para fazer callbacks e redirecionamentos. Em desenvolvimento local (`localhost` ou `127.0.0.1`), essas URLs não são acessíveis externamente.

## ✅ Solução: Usar Túnel Público

### Opção 1: ngrok (Recomendado)

#### 1. Instalar ngrok

**Windows:**
- Baixe em: https://ngrok.com/download
- Extraia o arquivo `ngrok.exe` em uma pasta (ex: `C:\ngrok\`)
- Ou use via Chocolatey: `choco install ngrok`

**Mac:**
```bash
brew install ngrok
```

**Linux:**
```bash
# Baixe e extraia
wget https://bin.equinox.io/c/bNyj1mQVY4c/ngrok-v3-stable-linux-amd64.tgz
tar -xzf ngrok-v3-stable-linux-amd64.tgz
sudo mv ngrok /usr/local/bin/
```

#### 2. Criar conta no ngrok (gratuita)

1. Acesse: https://dashboard.ngrok.com/signup
2. Faça login e copie seu authtoken

#### 3. Configurar ngrok

```bash
ngrok config add-authtoken SEU_AUTHTOKEN_AQUI
```

#### 4. Iniciar túnel

Com seu servidor XAMPP rodando na porta 80 ou 8000:

```bash
# Se estiver usando porta 80 (padrão XAMPP)
ngrok http 80

# OU se estiver usando porta 8000 (php artisan serve)
ngrok http 8000
```

Você verá algo assim:
```
Forwarding  https://abc123.ngrok-free.app -> http://localhost:8000
```

#### 5. Configurar APP_URL no .env

Copie a URL HTTPS do ngrok e configure no `.env`:

```env
APP_URL=https://abc123.ngrok-free.app
```

**⚠️ IMPORTANTE:** 
- Use a URL **HTTPS** (não HTTP)
- Sempre que reiniciar o ngrok, a URL muda (a menos que tenha plano pago)
- Atualize o `APP_URL` sempre que a URL mudar

#### 6. Limpar cache do Laravel

```bash
php artisan config:clear
php artisan cache:clear
```

#### 7. Testar

Agora você pode testar o checkout normalmente. O Mercado Pago conseguirá acessar suas URLs de retorno.

---

### Opção 2: localtunnel (Alternativa Gratuita)

#### 1. Instalar localtunnel

```bash
npm install -g localtunnel
```

#### 2. Iniciar túnel

```bash
# Porta 80 (XAMPP padrão)
lt --port 80

# OU porta 8000
lt --port 8000
```

Você receberá uma URL como: `https://random-subdomain.loca.lt`

#### 3. Configurar no .env

```env
APP_URL=https://random-subdomain.loca.lt
```

#### 4. Limpar cache

```bash
php artisan config:clear
```

---

### Opção 3: Cloudflare Tunnel (cloudflared)

#### 1. Baixar cloudflared

Windows: https://github.com/cloudflare/cloudflared/releases

#### 2. Iniciar túnel

```bash
cloudflared tunnel --url http://localhost:8000
```

---

## 🔧 Configuração Completa do .env

```env
# Configuração Básica
APP_NAME="Shava Haux"
APP_ENV=local
APP_DEBUG=true
APP_URL=https://sua-url-ngrok.ngrok-free.app

# Mercado Pago (Sandbox)
MERCADOPAGO_ACCESS_TOKEN=APP_USR-7164337348782001-112011-14611bd1655627cbeb4ee00b47b6c124-3004659556
MERCADOPAGO_PUBLIC_KEY=APP_USR-06238026-36ac-46d6-bd08-d1eb92f61994

# Outras configurações...
```

---

## 🧪 Como Testar

### 1. Preparar Ambiente

```bash
# 1. Iniciar ngrok em um terminal
ngrok http 8000

# 2. Copiar a URL HTTPS (ex: https://abc123.ngrok-free.app)

# 3. Atualizar .env
APP_URL=https://abc123.ngrok-free.app

# 4. Limpar cache
php artisan config:clear

# 5. Iniciar servidor Laravel (se não estiver rodando)
php artisan serve
# OU usar o Apache do XAMPP na porta 80
```

### 2. Testar Checkout

1. Acesse sua loja através da URL do ngrok: `https://abc123.ngrok-free.app`
2. Adicione produtos ao carrinho
3. Vá para o checkout
4. Preencha o endereço
5. Selecione "Mercado Pago"
6. Clique em "Finalizar Pedido"
7. Você será redirecionado para o checkout do Mercado Pago

### 3. Cartões de Teste (Sandbox)

Use estes cartões para testar:

**Cartão Aprovado:**
- Número: `5031 7557 3453 0604`
- CVV: `123`
- Validade: Qualquer data futura (ex: `12/25`)
- Nome: Qualquer nome

**Cartão Recusado:**
- Número: `5031 4332 1540 6351`
- CVV: `123`
- Validade: Qualquer data futura

**Pix:**
- Use qualquer CPF válido
- O QR Code será gerado automaticamente

---

## 🐛 Troubleshooting

### Problema: "URL não acessível"

**Solução:**
- Verifique se o ngrok está rodando
- Confirme que o `APP_URL` está correto no `.env`
- Execute `php artisan config:clear`

### Problema: "URL mudou após reiniciar ngrok"

**Solução:**
- Atualize o `APP_URL` no `.env` com a nova URL
- Execute `php artisan config:clear`
- Ou use ngrok com domínio fixo (requer plano pago)

### Problema: "Erro de CORS ou SSL"

**Solução:**
- Use sempre a URL HTTPS do ngrok (não HTTP)
- O ngrok fornece SSL automaticamente

### Problema: "Mercado Pago não redireciona de volta"

**Solução:**
- Verifique os logs: `storage/logs/laravel.log`
- Confirme que as URLs de retorno estão corretas nos logs
- Teste acessar manualmente: `https://sua-url.ngrok-free.app/checkout/pedido-realizado`

---

## 📝 Dicas Importantes

1. **Sempre use HTTPS** nas URLs do ngrok
2. **Atualize APP_URL** sempre que reiniciar o ngrok
3. **Limpe o cache** após mudar APP_URL: `php artisan config:clear`
4. **Monitore os logs** para debug: `tail -f storage/logs/laravel.log`
5. **Use tokens de Sandbox** - nunca use tokens de produção em desenvolvimento

---

## 🚀 Próximos Passos

Após testar localmente com sucesso:

1. Configure webhooks (opcional) para receber notificações
2. Teste todos os fluxos: sucesso, falha, pendente
3. Quando for para produção, use tokens de produção e URLs reais

---

## 📚 Recursos

- [Documentação ngrok](https://ngrok.com/docs)
- [Documentação Mercado Pago](https://www.mercadopago.com.br/developers/pt/docs)
- [Cartões de Teste MP](https://www.mercadopago.com.br/developers/pt/docs/checkout-pro/test-cards)


