# Feature: Endereço Obrigatório para Clientes PJ

## 📍 Objetivo

Tornar o cadastro de endereço **obrigatório** para clientes **Pessoa Jurídica (PJ)**, visando futuramente criar relatórios de mapeamento de clientes utilizando **OpenStreetMap**.

---

## ✅ O Que Foi Implementado

### 1. Seção "Endereço Comercial" no UserResource

**Arquivo**: [`app/Filament/Resources/UserResource.php`](file:///c:/xampp/htdocs/ecommerce_shava/app/Filament/Resources/UserResource.php)

**Nova seção adicionada** (entre "Tipo de Pessoa" e "Permissões"):

#### Campos do Formulário:
- **CEP** (mask: `99999-999`) - required para PJ
- **Rua/Avenida** (2 colunas de grid) - required para PJ
- **Número** (1 coluna de grid) - required para PJ
- **Complemento** (opcional) - sala, andar, bloco
- **Cidade** - required para PJ
- **Estado** (Select com todos os 27 estados) - required para PJ, searchable

#### Características:
- ✅ **Visível apenas para PJ** (`visible quando user_type === 'pj'`)
- ✅ **Obrigatório apenas para PJ** (`required quando user_type === 'pj'`)
- ✅ **Seção colapsável** para economizar espaço
- ✅ **Descrição explicativa**: "Obrigatório para Pessoa Jurídica - será usado para mapeamento de clientes"
- ✅ **Máscaras** em CEP
- ✅ **Select de estados** com busca

---

### 2. Hooks de Salvamento

#### CreateUser.php

**Arquivo**: [`app/Filament/Resources/UserResource/Pages/CreateUser.php`](file:///c:/xampp/htdocs/ecommerce_shava/app/Filament/Resources/UserResource/Pages/CreateUser.php)

**Hook `afterCreate()`**:
- Verifica se `user_type === 'pj'`
- Se houver dados de endereço (`endereco_cep`), cria registro na tabela `enderecos`
- Usa relacionamento `$user->enderecos()->create()`

```php
protected function afterCreate(): void
{
    if ($user->user_type === 'pj' && $this->data['endereco_cep'] ?? null) {
        $user->enderecos()->create([...]);
    }
}
```

---

#### EditUser.php

**Arquivo**: [`app/Filament/Resources/UserResource/Pages/EditUser.php`](file:///c:/xampp/htdocs/ecommerce_shava/app/Filament/Resources/UserResource/Pages/EditUser.php)

**Hook `mutateFormDataBeforeFill()`**:
- Carrega endereço existente do PJ
- Popula campos do formulário (`endereco_cep`, `endereco_rua`, etc)
- Permite edição de endereço já cadastrado

**Hook `afterSave()`**:
- Verifica se `user_type === 'pj'` e há dados de endereço
- Se endereço já existe: **atualiza** (`update()`)
- Se não existe: **cria** (`create()`)

```php
protected function afterSave(): void
{
    if ($endereco) {
        $endereco->update($enderecoData);
    } else {
        $user->enderecos()->create($enderecoData);
    }
}
```

---

## 🗺️ Estrutura de Dados

### Tabela `enderecos` (já existente)

```
id
user_id (FK para users)
rua
numero
complemento (nullable)
cidade
estado
cep
created_at
updated_at
```

### Mapeamento Formulário → Banco

| Campo Formulário | Campo Banco | Tipo | Obrigatório PJ |
|------------------|-------------|------|----------------|
| `endereco_cep` | `cep` | string | Sim |
| `endereco_rua` | `rua` | string | Sim |
| `endereco_numero` | `numero` | string | Sim |
| `endereco_complemento` | `complemento` | string | Não |
| `endereco_cidade` | `cidade` | string | Sim |
| `endereco_estado` | `estado` | string (2 chars) | Sim |

---

## 🧪 Como Testar

### 1. Criar Cliente PJ com Endereço

1. Acesse `/admin/users`
2. Clique em "Novo Usuário"
3. Preencha informações básicas
4. Selecione **"Pessoa Jurídica"** em "Tipo de Pessoa"
5. A seção **"Endereço Comercial"** aparece automaticamente
6. Preencha TODOS os campos obrigatórios:
   - CEP (ex: `01310-100`)
   - Rua/Avenida
   - Número
   - Cidade
   - Estado (selecione da lista)
7. Salve
8. **Verifique no banco**:
   ```sql
   SELECT u.name, u.user_type, e.* 
   FROM users u
   LEFT JOIN enderecos e ON e.user_id = u.id
   WHERE u.user_type = 'pj';
   ```

### 2. Editar Endereço de PJ Existente

1. Edite um usuário PJ já cadastrado
2. A seção "Endereço Comercial" deve **carregar os dados existentes**
3. Altere algum campo (ex: número)
4. Salve
5. Verifique que o endereço foi **atualizado** (não duplicado)

### 3. Validação: Campos Obrigatórios

1. Tente criar PJ **sem preencher endereço**
2. Deve dar erro de validação
3. Preencha os campos obrigatórios
4. Deve salvar com sucesso

### 4. Pessoa Física (PF)

1. Crie/edite usuário PF
2. A seção "Endereço Comercial" **não deve aparecer**
3. Não deve ser obrigatório

---

## 🎯 Próximos Passos (Futuro)

### 1. Relatório de Mapeamento com OpenStreetMap

**Funcionalidades sugeridas**:
- Dashboard com mapa mostrando localização dos clientes PJ
- Filtros por estado, cidade
- Clustering de marcadores
- Popup com informações da empresa

**Tecnologias**:
- [Leaflet.js](https://leafletjs.com/) + OpenStreetMap
- Ou [Filament Maps Plugin](https://filamentphp.com/plugins) (se disponível)

**Query de exemplo**:
```php
$clientesPJ = User::where('user_type', 'pj')
    ->with('enderecos')
    ->get()
    ->map(function($user) {
        $endereco = $user->enderecos->first();
        return [
            'name' => $user->razao_social ?? $user->name,
            'address' => "{$endereco->rua}, {$endereco->numero} - {$endereco->cidade}/{$endereco->estado}",
            'cep' => $endereco->cep,
            // Geocoding: converter CEP em lat/lng
        ];
    });
```

### 2. Geocoding (Converter Endereço em Coordenadas)

**Opções**:
- API do Google Maps (paga após limite)
- [Nominatim](https://nominatim.org/) (OpenStreetMap, gratuita)
- [ViaCEP](https://viacep.com.br/) (Brasil-específico, gratuita)

**Implementação sugerida**:
- Adicionar campos `latitude` e `longitude` na tabela `enderecos`
- Ao salvar endereço, fazer geocoding assíncrono
- Armazenar coordenadas para plotting no mapa

---

## 📊 Estatísticas Potenciais

Com os dados de endereço, você poderá gerar:

1. **Mapa de calor** de concentração de clientes
2. **Análise regional** de vendas
3. **Rotas de entrega** otimizadas
4. **Expansão estratégica** (onde abrir novos pontos)
5. **Segmentação geográfica** para campanhas de marketing

---

## ✅ Checklist de Implementação

- [x] Adicionar seção "Endereço Comercial" no formulário
- [x] Tornar campos obrigatórios apenas para PJ
- [x] Implementar visibilidade condicional
- [x] Criar hook `afterCreate` para salvar endereço
- [x] Criar hook `mutateFormDataBeforeFill` para carregar endereço
- [x] Criar hook `afterSave` para atualizar/criar endereço
- [x] Testar criação de PJ com endereço
- [x] Testar edição de endereço existente
- [ ] Testar em produção
- [ ] Implementar geocoding (futuro)
- [ ] Criar dashboard de mapeamento (futuro)

---

## 🔍 Observações Técnicas

### Por que não adicionar campos diretamente na tabela users?

- ✅ **Normalização**: Endereço é uma entidade separada
- ✅ **Flexibilidade**: Users podem ter múltiplos endereços no futuro
- ✅ **Reutilização**: Relacionamento `hasMany` já existe no sistema
- ✅ **Consistência**: Pedidos (`orders`) já usam `endereco_id`

### Por que usar hooks em vez de relacionamento direto?

- ✅ **Separação de concerns**: Campos do formulário são temporários
- ✅ **Validação**: Filament valida antes de salvar
- ✅ **Flexibilidade**: Fácil adicionar lógica extra (ex: geocoding)
- ✅ **Controle**: Podemos decidir quando criar/atualizar

---

## 📁 Arquivos Modificados

1. ✅ [`app/Filament/Resources/UserResource.php`](file:///c:/xampp/htdocs/ecommerce_shava/app/Filament/Resources/UserResource.php) - Formulário
2. ✅ [`app/Filament/Resources/UserResource/Pages/CreateUser.php`](file:///c:/xampp/htdocs/ecommerce_shava/app/Filament/Resources/UserResource/Pages/CreateUser.php) - Hook de criação
3. ✅ [`app/Filament/Resources/UserResource/Pages/EditUser.php`](file:///c:/xampp/htdocs/ecommerce_shava/app/Filament/Resources/UserResource/Pages/EditUser.php) - Hooks de edição

**Nenhuma migration necessária** - usamos tabela `enderecos` existente!

---

## 🎉 Conclusão

Agora todo cliente PJ cadastrado terá **endereço obrigatório**, permitindo:
- **Rastreamento geográfico** de clientes
- **Análise territorial** de vendas  
- **Futuros relatórios** com OpenStreetMap
- **Base de dados completa** para expansão

A implementação é **reversível**, **escalável** e **pronta para evolução**!
