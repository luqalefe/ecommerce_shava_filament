# 🔧 Solução para Erro CORS do Vite

## 🐛 Problema

Ao acessar o site via túnel (Serveo, ngrok, etc.), o Vite tenta carregar recursos de `localhost:5173`, causando erro de CORS:

```
Access to script at 'http://[::1]:5173/@vite/client' from origin 'http://tunel.serveo.net' 
has been blocked by CORS policy
```

## ✅ Soluções

### Solução 1: Usar Assets Compilados (Recomendado para Produção/Túnel)

**Para usar o site via túnel sem o servidor Vite:**

1. **Compile os assets:**
   ```bash
   npm run build
   ```

2. **Pare o servidor Vite** (se estiver rodando):
   - Pressione `Ctrl+C` no terminal onde o Vite está rodando
   - Ou feche o terminal

3. **Acesse o site via túnel:**
   - Os assets compilados em `public/build/` serão usados automaticamente
   - Não haverá mais erro de CORS

### Solução 2: Configurar Vite para Aceitar Túneis (Desenvolvimento)

**Se você precisa do hot-reload do Vite via túnel:**

1. **Configure o Vite** (já foi feito em `vite.config.js`):
   ```js
   server: {
       host: '0.0.0.0', // Aceita conexões de qualquer IP
       cors: {
           origin: '*', // Permite CORS
       },
   }
   ```

2. **Inicie o Vite com host público:**
   ```bash
   npm run dev -- --host
   ```

3. **Configure a variável de ambiente:**
   ```env
   VITE_HMR_HOST=seu-tunel.serveo.net
   ```

### Solução 3: Usar Variável de Ambiente (Automático)

O Laravel Vite Plugin detecta automaticamente se deve usar:
- **Modo Dev**: Se `APP_ENV=local` E o servidor Vite estiver rodando
- **Modo Produção**: Se os assets compilados existirem em `public/build/`

**Para forçar uso de assets compilados:**
```env
APP_ENV=production
```

Ou simplesmente compile os assets e pare o servidor Vite.

---

## 🚀 Para Deploy em Produção

**SEMPRE compile os assets antes de fazer deploy:**

```bash
npm run build
```

Isso garante que:
- ✅ Assets estão otimizados e minificados
- ✅ Não há dependência do servidor Vite
- ✅ Sem erros de CORS
- ✅ Melhor performance

---

## 📝 Checklist

- [ ] Assets compilados (`npm run build`)
- [ ] Servidor Vite parado (se estiver rodando)
- [ ] Assets em `public/build/` existem
- [ ] Site funciona sem erros de CORS

---

## 🐛 Troubleshooting

### Erro persiste mesmo após compilar

1. **Limpe o cache do navegador:**
   - `Ctrl+Shift+R` (hard refresh)
   - Ou limpe o cache completamente

2. **Verifique se os assets foram compilados:**
   ```bash
   ls public/build/
   ```
   Deve mostrar: `manifest.json`, `assets/app-*.css`, `assets/app-*.js`

3. **Verifique permissões:**
   ```bash
   chmod -R 755 public/build
   ```

### Ainda quer usar Vite Dev Server via túnel

1. Configure o Vite para aceitar conexões externas (já feito)
2. Inicie com: `npm run dev -- --host`
3. Configure `VITE_HMR_HOST` no `.env`

---

**Última atualização**: Novembro 2024



