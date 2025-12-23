# 🔐 Como Configurar Google OAuth

## 📋 Pré-requisitos

1. Conta Google (Gmail)
2. Acesso ao [Google Cloud Console](https://console.cloud.google.com/)

---

## 🚀 Passo a Passo

### 1. Criar Projeto no Google Cloud Console

1. Acesse: https://console.cloud.google.com/
2. Clique em **"Selecionar um projeto"** → **"Novo Projeto"**
3. Nome do projeto: `Shava E-commerce` (ou outro nome)
4. Clique em **"Criar"**

### 2. Habilitar Google+ API

1. No menu lateral, vá em **"APIs e Serviços"** → **"Biblioteca"**
2. Procure por **"Google+ API"** ou **"Google Identity"**
3. Clique em **"Ativar"**

### 3. Criar Credenciais OAuth 2.0

1. Vá em **"APIs e Serviços"** → **"Credenciais"**
2. Clique em **"Criar credenciais"** → **"ID do cliente OAuth"**
3. Se solicitado, configure a tela de consentimento:
   - Tipo de usuário: **"Externo"**
   - Nome do app: `Shava E-commerce`
   - Email de suporte: seu email
   - Domínios autorizados: seu domínio (ex: `seudominio.com.br`)
   - Clique em **"Salvar e continuar"**
   - Adicione escopos: `email`, `profile`, `openid`
   - Adicione usuários de teste (se necessário)
   - Clique em **"Salvar e continuar"**

4. Tipo de aplicativo: **"Aplicativo da Web"**
5. Nome: `Shava E-commerce Web Client`
6. **URIs de redirecionamento autorizados**:
   ```
   http://localhost:8000/auth/google/callback
   https://seudominio.com.br/auth/google/callback
   ```
   ⚠️ **IMPORTANTE**: Adicione TODAS as URLs onde o app será usado (local e produção)

7. Clique em **"Criar"**

### 4. Copiar Credenciais

Após criar, você verá:
- **ID do cliente** (Client ID): `123456789-abc...googleusercontent.com`
- **Chave secreta do cliente** (Client Secret): `GOCSPX-abc...`

**⚠️ IMPORTANTE**: Guarde essas credenciais com segurança!

---

## ⚙️ Configurar no Laravel

### 1. Adicionar ao `.env`

```env
# Google OAuth
GOOGLE_CLIENT_ID=123456789-abc...googleusercontent.com
GOOGLE_CLIENT_SECRET=GOCSPX-abc...
GOOGLE_REDIRECT_URI=${APP_URL}/auth/google/callback
```

**Para desenvolvimento local:**
```env
APP_URL=http://localhost:8000
GOOGLE_REDIRECT_URI=http://localhost:8000/auth/google/callback
```

**Para produção (Hostinger):**
```env
APP_URL=https://seudominio.com.br
GOOGLE_REDIRECT_URI=https://seudominio.com.br/auth/google/callback
```

### 2. Limpar Cache de Configuração

```bash
php artisan config:clear
php artisan cache:clear
```

### 3. Verificar Configuração

A configuração está em `config/services.php`:

```php
'google' => [
    'client_id' => env('GOOGLE_CLIENT_ID'),
    'client_secret' => env('GOOGLE_CLIENT_SECRET'),
    'redirect' => env('GOOGLE_REDIRECT_URI', env('APP_URL') . '/auth/google/callback'),
],
```

---

## ✅ Testar

1. Acesse: `http://localhost:8000/login` (ou seu domínio)
2. Clique em **"Entrar com Google"**
3. Você será redirecionado para o Google
4. Autorize o acesso
5. Você será redirecionado de volta e estará logado

---

## 🐛 Troubleshooting

### Erro: "Missing required parameter: client_id"

**Causa**: As variáveis de ambiente não estão configuradas ou o cache não foi limpo.

**Solução**:
1. Verifique se `GOOGLE_CLIENT_ID` e `GOOGLE_CLIENT_SECRET` estão no `.env`
2. Execute: `php artisan config:clear`
3. Verifique se não há espaços extras nas variáveis do `.env`

### Erro: "redirect_uri_mismatch"

**Causa**: A URL de callback não está registrada no Google Cloud Console.

**Solução**:
1. Acesse Google Cloud Console → Credenciais
2. Edite o OAuth 2.0 Client ID
3. Adicione a URL exata que está sendo usada:
   - `http://localhost:8000/auth/google/callback` (desenvolvimento)
   - `https://seudominio.com.br/auth/google/callback` (produção)

### Erro: "Access blocked: This app's request is invalid"

**Causa**: A tela de consentimento não está configurada ou o app está em modo de teste.

**Solução**:
1. Configure a tela de consentimento no Google Cloud Console
2. Adicione usuários de teste (se necessário)
3. Publique o app (se necessário para uso público)

### Erro: "Invalid client secret"

**Causa**: A chave secreta está incorreta ou foi regenerada.

**Solução**:
1. Verifique se `GOOGLE_CLIENT_SECRET` no `.env` está correto
2. Se necessário, gere uma nova chave no Google Cloud Console
3. Atualize o `.env` e limpe o cache

---

## 🔒 Segurança

1. **Nunca commite o `.env`** no Git
2. Use credenciais diferentes para desenvolvimento e produção
3. Regenere as chaves se suspeitar de vazamento
4. Mantenha a tela de consentimento atualizada

---

## 📝 Checklist

- [ ] Projeto criado no Google Cloud Console
- [ ] Google+ API habilitada
- [ ] OAuth 2.0 Client ID criado
- [ ] URLs de callback adicionadas (local e produção)
- [ ] Credenciais adicionadas ao `.env`
- [ ] Cache limpo (`php artisan config:clear`)
- [ ] Teste realizado com sucesso

---

**Última atualização**: Novembro 2024



