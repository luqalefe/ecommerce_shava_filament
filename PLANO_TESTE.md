# 🧪 Plano de Teste - Página da Loja (/loja)

## 📋 Objetivo
Testar a página `/loja` que foi migrada para Livewire 3 + TailwindCSS, verificando se todos os componentes funcionam corretamente.

---

## ✅ Pré-requisitos

1. **Servidor Laravel rodando**
   ```bash
   php artisan serve
   ```

2. **Assets compilados (Vite)**
   ```bash
   npm run dev
   # ou
   npm run build
   ```

3. **Banco de dados configurado**
   - Tabela `products` com pelo menos alguns produtos ativos (`is_active = true`)
   - Tabela `categories` com categorias (para o navbar)

4. **Configuração do Cart**
   - Verificar se o pacote `darryldecode/cart` está instalado e configurado

---

## 🧪 Checklist de Testes

### 1. Teste Básico - Carregamento da Página

**URL**: `http://localhost:8000/loja`

**O que verificar:**
- [ ] A página carrega sem erros 500/404
- [ ] O título "Nossa Loja" aparece
- [ ] O layout está correto (navbar + footer)
- [ ] Os produtos são exibidos em grid (se houver produtos no banco)
- [ ] A mensagem "Nenhum produto encontrado" aparece se não houver produtos

**Possíveis problemas:**
- ❌ Erro 500: Verificar logs em `storage/logs/laravel.log`
- ❌ Erro 404: Verificar se a rota está registrada (`php artisan route:list | findstr loja`)
- ❌ Layout quebrado: Verificar se `npm run dev` está rodando

---

### 2. Teste do Campo de Busca

**O que fazer:**
1. Digitar algo no campo "Buscar produtos..."
2. Observar se a lista de produtos filtra automaticamente (debounce de 300ms)

**O que verificar:**
- [ ] O campo de busca aparece
- [ ] Ao digitar, a lista de produtos filtra em tempo real
- [ ] A paginação reseta quando a busca muda
- [ ] A URL atualiza com o parâmetro `?busca=termo` (graças ao `#[Url]`)

**Possíveis problemas:**
- ❌ Busca não funciona: Verificar se `wire:model.live.debounce.300ms="search"` está no input
- ❌ Erro JavaScript: Abrir console do navegador (F12) e verificar erros

---

### 3. Teste do MiniCart no Navbar

**O que verificar:**
- [ ] O ícone do carrinho aparece no navbar (canto superior direito)
- [ ] O contador de itens aparece quando há produtos no carrinho
- [ ] O link do carrinho leva para `/carrinho`

**Como testar:**
1. Adicionar um produto ao carrinho (ver teste 4)
2. Verificar se o contador no navbar atualiza automaticamente

**Possíveis problemas:**
- ❌ MiniCart não aparece: Verificar se `<livewire:mini-cart />` está no navbar
- ❌ Contador não atualiza: Verificar se o evento `cart-updated` está sendo disparado

---

### 4. Teste do AddToCart (Adicionar ao Carrinho)

**O que fazer:**
1. Encontrar um produto na lista
2. Clicar no botão "Adicionar" (ou ajustar quantidade e clicar)

**O que verificar:**
- [ ] O botão "Adicionar" aparece em cada produto
- [ ] Ao clicar, uma mensagem de sucesso aparece ("[Nome do Produto] foi adicionado ao carrinho!")
- [ ] O MiniCart no navbar atualiza automaticamente (contador aumenta)
- [ ] O produto é adicionado ao carrinho (verificar em `/carrinho`)

**Possíveis problemas:**
- ❌ Botão não funciona: Verificar se `wire:click="add"` está no botão
- ❌ Erro ao adicionar: Verificar logs e se o produto tem `sale_price` ou `price`
- ❌ MiniCart não atualiza: Verificar se `$this->dispatch('cart-updated')` está sendo chamado

---

### 5. Teste de Navegação

**O que verificar:**
- [ ] Clicar no nome/imagem do produto leva para `/produto/{slug}`
- [ ] O link "LOJA" no navbar está ativo quando na página `/loja`
- [ ] As categorias no navbar aparecem (se configuradas)

**Possíveis problemas:**
- ❌ Link quebrado: Verificar se `route('product.show', $product->slug)` está correto
- ❌ Categorias não aparecem: Verificar `AppServiceProvider` (ViewComposer)

---

### 6. Teste de Paginação

**O que fazer:**
1. Garantir que há mais de 12 produtos no banco (ou ajustar `paginate(12)` no código)
2. Navegar para a página 2

**O que verificar:**
- [ ] Os links de paginação aparecem na parte inferior
- [ ] Ao clicar em "Próxima" ou número da página, a lista atualiza
- [ ] A busca mantém o filtro ao mudar de página

**Possíveis problemas:**
- ❌ Paginação não aparece: Verificar se há mais de 12 produtos
- ❌ Erro ao mudar página: Verificar se `WithPagination` está sendo usado

---

## 🔍 Debugging - Comandos Úteis

### Verificar Rotas
```bash
php artisan route:list | findstr "loja\|cart\|checkout\|products"
```

### Verificar Logs
```bash
tail -f storage/logs/laravel.log
```

### Limpar Cache
```bash
php artisan cache:clear
php artisan view:clear
php artisan config:clear
php artisan route:clear
```

### Verificar Componentes Livewire
```bash
php artisan livewire:list
```

---

## 📝 Checklist Rápido (Resumo)

- [ ] Página `/loja` carrega
- [ ] Produtos aparecem em grid
- [ ] Campo de busca funciona
- [ ] MiniCart aparece no navbar
- [ ] Botão "Adicionar" funciona
- [ ] MiniCart atualiza ao adicionar produto
- [ ] Links de produtos funcionam
- [ ] Paginação funciona (se aplicável)

---

## 🚀 Próximos Testes (Após Validar /loja)

1. **Página do Carrinho** (`/carrinho`)
   - Verificar se itens aparecem
   - Testar incrementar/decrementar quantidade
   - Testar remover item
   - Verificar link para checkout

2. **Página de Checkout** (`/checkout`)
   - Verificar se carrega (requer autenticação)
   - Testar cálculo de frete
   - Testar finalização de pedido

---

## ⚠️ Problemas Comuns e Soluções

### Erro: "Target class [App\Livewire\ProductList] does not exist"
**Solução**: Verificar se o arquivo `app/Livewire/ProductList.php` existe e tem o namespace correto.

### Erro: "View [livewire.product-list] not found"
**Solução**: Verificar se o arquivo `resources/views/livewire/product-list.blade.php` existe.

### MiniCart não atualiza
**Solução**: 
1. Verificar se `#[On('cart-updated')]` está no método `render()` do `MiniCart`
2. Verificar se `$this->dispatch('cart-updated')` está sendo chamado no `AddToCart`

### Estilos não aparecem (TailwindCSS)
**Solução**: 
1. Verificar se `npm run dev` está rodando
2. Verificar se `@vite(['resources/css/app.css'])` está no layout
3. Limpar cache do navegador (Ctrl+F5)

---

## ✅ Critério de Sucesso

A página `/loja` está funcionando corretamente quando:
1. ✅ Carrega sem erros
2. ✅ Mostra produtos (ou mensagem se vazio)
3. ✅ Busca funciona
4. ✅ Adicionar ao carrinho funciona
5. ✅ MiniCart atualiza automaticamente

**Se todos os itens acima estão OK, você pode prosseguir para testar `/carrinho` e `/checkout`!** 🎉

