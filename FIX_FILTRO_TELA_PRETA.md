# Solução: Tela Preta ao Usar Filtro de Usuários

## Problema

Ao selecionar um tipo de usuário (PF ou PJ) no filtro, a tela fica preta.

## Soluções Aplicadas

### 1. Removido `sortable()` da Coluna
- Coluna "Tipo" não é mais ordenável
- Previne cliques acidentais

### 2. Adicionado `deferFilters()`
- Filtros agora requerem clicar em "Aplicar"
- Evita requisições AJAX imediatas
- Mais estável

### 3. Adicionado `persistFiltersInSession()`
- Mantém filtros entre sessões
- Melhor UX

## Como Usar Agora

1. Acesse `/admin/users`
2. Clique no ícone de filtro (funil)
3. Selecione "Tipo de Pessoa" (PF ou PJ)
4. **Clique no botão "Aplicar" (Apply)**
5. A tabela filtra sem tela preta

## Se o Problema Persistir

### Opção 1: Limpar Cache do Navegador
```
Ctrl+Shift+Delete → Limpar cache
OU
Ctrl+Shift+N (modo anônimo)
```

### Opção 2: Verificar Console do Navegador
1. Pressione F12
2. Vá em "Console"
3. Veja se há erros JavaScript
4. Me envie o erro

### Opção 3: Verificar Assets
Se houver erro 404 ou problemas de carregamento:
```bash
npm run build
php artisan filament:assets
```

### Opção 4: Remover o Filtro Temporariamente
Se nada funcionar, podemos:
- Remover o filtro dropdown
- Usar tabs ou outra forma de filtrar

## Código Aplicado

```php
->filters([
    Tables\Filters\SelectFilter::make('user_type')
        ->label('Tipo de Pessoa')
        ->options([
            'pf' => 'Pessoa Física',
            'pj' => 'Pessoa Jurídica',
        ])
        ->placeholder('Todos'),
])
->deferFilters()  // ← IMPORTANTE: aplica filtro apenas ao clicar "Aplicar"
->persistFiltersInSession()
```

## Teste Agora

1. Recarregue a página (F5)
2. Use o filtro
3. Clique em "Aplicar"
4. Me confirme se funcionou!
