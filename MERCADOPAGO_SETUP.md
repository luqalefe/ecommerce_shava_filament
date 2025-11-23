# Configuração Mercado Pago - Integração Completa

## ✅ O que foi implementado

1. ✅ SDK do Mercado Pago instalado (`mercadopago/dx-php`)
2. ✅ Configuração em `config/services.php`
3. ✅ Serviço `App\Services\MercadoPagoService` criado
4. ✅ `CheckoutPage` atualizado para suportar Mercado Pago
5. ✅ View atualizada com opção de pagamento Mercado Pago

## 🔧 Configuração do .env

Adicione as seguintes variáveis no seu arquivo `.env`:

```env
# Mercado Pago
MERCADOPAGO_ACCESS_TOKEN=APP_USR-7164337348782001-112011-14611bd1655627cbeb4ee00b47b6c124-3004659556
MERCADOPAGO_PUBLIC_KEY=APP_USR-06238026-36ac-46d6-bd08-d1eb92f61994
```

**Nota:** As chaves acima são do ambiente Sandbox/Teste. Quando for para produção, substitua pelas chaves de produção.

## 📋 Como funciona

### Fluxo de Pagamento

1. **Cliente seleciona "Mercado Pago"** no checkout
2. **Sistema cria uma preferência** no Mercado Pago com todos os itens do carrinho
3. **Cliente é redirecionado** para o checkout do Mercado Pago (Checkout Pro)
4. **Cliente escolhe** entre Cartão de Crédito ou Pix no próprio checkout do MP
5. **Após pagamento**, cliente é redirecionado de volta para `checkout.success`

### Métodos de Pagamento Disponíveis

- ✅ **PIX** (via Abacate Pay) - Pagamento instantâneo
- ✅ **Mercado Pago** (Cartão de Crédito ou Pix) - Checkout Pro com redirect

## 🎯 Funcionalidades Implementadas

### MercadoPagoService

- `createPreference()` - Cria uma preferência de pagamento com:
  - Lista completa de itens do carrinho
  - Frete como item separado
  - Dados do pagador (nome, email, CPF, telefone, endereço)
  - URLs de retorno (success, failure, pending)
  - Referência externa (ID do pedido)

- `getPayment()` - Busca informações de um pagamento pelo ID

### CheckoutPage

- Suporte para múltiplos métodos de pagamento
- Validação de dados do usuário
- Criação de pedido antes do pagamento
- Redirecionamento automático para checkout do Mercado Pago

## 🔍 Verificação

### Teste em Sandbox

1. Acesse o checkout com um carrinho com itens
2. Selecione "Mercado Pago" como forma de pagamento
3. Preencha o endereço e selecione o frete
4. Clique em "Finalizar Pedido"
5. Você será redirecionado para o checkout do Mercado Pago

### Cartões de Teste (Sandbox)

Use os cartões de teste do Mercado Pago:
- **Aprovado:** 5031 7557 3453 0604
- **Recusado:** 5031 4332 1540 6351
- **CVV:** Qualquer 3 dígitos
- **Data:** Qualquer data futura
- **Nome:** Qualquer nome

## 📝 Próximos Passos (Opcional)

1. **Webhook**: Configure webhook para receber notificações de pagamento
   - Descomente a linha em `MercadoPagoService.php`: `$preference->notification_url`
   - Crie a rota e controller para processar webhooks

2. **Checkout Transparente**: Se preferir checkout sem redirect, implemente usando Mercado Pago Bricks
   - Requer JavaScript adicional no frontend
   - Mais complexo, mas melhor UX

3. **Produção**: Quando estiver pronto para produção:
   - Substitua as chaves no `.env` pelas chaves de produção
   - Teste todos os fluxos antes de ir ao ar

## 🐛 Troubleshooting

### Erro: "Mercado Pago Access Token não configurado"
- Verifique se as variáveis estão no `.env`
- Execute `php artisan config:clear` para limpar cache

### Erro ao criar preferência
- Verifique os logs em `storage/logs/laravel.log`
- Confirme que o token está correto e ativo
- Verifique se todos os itens têm preço válido

### Redirecionamento não funciona
- Verifique se a rota `checkout.success` existe
- Confirme que `APP_URL` está configurado corretamente no `.env`

## 📚 Documentação

- [SDK PHP do Mercado Pago](https://www.mercadopago.com.br/developers/pt/docs/sdks-library/server-side/sdk-php)
- [API de Preferências](https://www.mercadopago.com.br/developers/pt/reference/preferences/_checkout_preferences/post)
- [Checkout Pro](https://www.mercadopago.com.br/developers/pt/docs/checkout-pro/landing)

