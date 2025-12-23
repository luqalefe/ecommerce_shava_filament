# Solução Rápida: Warnings de SSL no php artisan serve

## Problema

Você está vendo warnings como:
```
WARN  127.0.0.1:60xxx Invalid request (Unsupported SSL request).
```

## Causa

O `APP_URL` no arquivo `.env` está configurado com **HTTPS**, mas o `php artisan serve` roda apenas em **HTTP**.

## Solução

### 1. Ajustar o APP_URL no .env

Abra o arquivo `.env` e altere de:
```env
APP_URL=https://...
```

Para:
```env
APP_URL=http://127.0.0.1:8000
```

### 2. Limpar o cache

```bash
php artisan config:clear
php artisan cache:clear
```

### 3. Reiniciar o servidor

Pare o servidor atual (Ctrl+C) e inicie novamente:
```bash
php artisan serve
```

### 4. Limpar cache do navegador

- **Chrome/Edge**: Pressione `Ctrl+Shift+Delete`
- Marque "Imagens e arquivos em cache"
- Clique em "Limpar dados"

Ou acesse em modo anônimo: `Ctrl+Shift+N`

## URLs Corretas

Com `php artisan serve`:
- **Site**: `http://127.0.0.1:8000`
- **Admin**: `http://127.0.0.1:8000/admin`

⚠️ Use **http://** (não https://)

## Verificação

Após ajustar, você deve acessar:
```
http://127.0.0.1:8000
```

Os warnings de SSL devem desaparecer!
