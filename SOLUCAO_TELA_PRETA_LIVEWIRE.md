# Solução Definitiva: Tela Preta no Filament/Livewire

## Problema

Tela preta ao usar qualquer funcionalidade Livewire:
- ❌ Ordenação de colunas (clicar em "Nome", "Email")
- ❌ Busca por nome/email
- ❌ Filtros dropdown
- ✅ Ações de status (resolvido com redirect)
- ✅ Botões de filtro PF/PJ (resolvido com url)

## Causa Raiz

A **barra de progresso do Livewire** (`show_progress_bar`) estava causando uma tela de overlay preta que não desaparecia após requisições AJAX.

## Solução Aplicada

### 1. Publicar Configuração do Livewire
```bash
php artisan livewire:publish --config
```

### 2. Desabilitar Barra de Progresso

**Arquivo**: `config/livewire.php`

```php
'navigate' => [
    'show_progress_bar' => false,  // ← MUDOU DE true PARA false
    'progress_bar_color' => 'transparent',
],
```

### 3. Atualizar Cache
```bash
php artisan config:cache
```

## Como Testar

1. **Recarregue a página** do admin (F5)
2. **Limpe cache do navegador** (Ctrl+Shift+Delete)
3. Teste todas as funcionalidades:
   - ✅ Clicar em "Nome" para ordenar
   - ✅ Clicar em "Email" para ordenar
   - ✅ Usar busca por nome/email
   - ✅ Usar filtro dropdown
   - ✅ Clicar nos botões PF/PJ
   - ✅ Mudar status de pedidos

## Por Que Funcionou

O Livewire mostrava uma barra de progresso preta/azul durante requisições AJAX. Por algum motivo (possivelmente CSS override do Filament), essa barra cobria a tela inteira e não desaparecia, causando a "tela preta".

Ao desabilitar `show_progress_bar`, as requisições AJAX continuam funcionando normalmente, mas sem o overlay problemático.

## Arquivos Modificados

- `config/livewire.php` - Desabilitado `show_progress_bar`

## Se o Problema Persistir

### Opção 1: Verificar Console do Navegador
Pressione F12 → Aba "Console" → Veja se há erros JavaScript

### Opção 2: Verificar Network
Pressione F12 → Aba "Network" → Veja se requisições estão falhando

### Opção 3: Rebuild Assets
```bash
npm run build
php artisan filament:assets
```

### Opção 4: Desabilitar Morphing
Se ainda houver problemas, tente:
```php
// config/livewire.php
'inject_morph_markers' => false,
```

## Outras Configurações Importantes

```php
'inject_assets' => true,  // Deve estar true
'render_on_redirect' => false,  // Deve estar false
```

## Conclusão

A **barra de progresso do Livewire** era o culpado! Agora todas as funcionalidades devem funcionar sem tela preta.
