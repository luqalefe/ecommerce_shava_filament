# Integração Stripe - Documentação

## ✅ O que foi implementado

1. **Instalação do Stripe SDK**
   - Pacote `stripe/stripe-php` instalado via Composer

2. **Configuração**
   - Variáveis adicionadas em `config/services.php`
   - Suporte para chaves de teste e produção

3. **Backend (CheckoutPage.php)**
   - Método `createPaymentIntent()`: Cria Payment Intent do Stripe
   - Método `placeOrderWithStripe()`: Finaliza pedido após confirmação do pagamento
   - Método `updatedPaymentMethod()`: Cria Payment Intent automaticamente quando cartão é selecionado
   - Suporte para ambos os métodos: PIX (Abacate Pay) e Cartão (Stripe)

4. **Frontend (checkout-page.blade.php)**
   - Seleção de método de pagamento (PIX ou Cartão)
   - Formulário Stripe Elements para dados do cartão
   - JavaScript para processar pagamento com `confirmCardPayment()`
   - Integração com Livewire 3

## 🔧 Configuração do .env

Adicione as seguintes variáveis ao seu arquivo `.env`:

```env
# Stripe (Test Mode)
STRIPE_KEY=pk_test_SUA_CHAVE_PUBLICA_AQUI
STRIPE_SECRET=sk_test_SUA_CHAVE_SECRETA_AQUI
```

**Importante**: 
- `STRIPE_KEY` é a chave **pública** (pode ser exposta no frontend)
- `STRIPE_SECRET` é a chave **secreta** (NUNCA deve ser exposta)

## 🚀 Como funciona

### Fluxo PIX (Abacate Pay)
1. Cliente seleciona PIX
2. Preenche endereço e seleciona frete
3. Clica em "Finalizar Pedido"
4. Sistema cria pedido e redireciona para Abacate Pay
5. Cliente paga via PIX
6. Abacate Pay redireciona de volta para `/checkout/pedido-realizado`

### Fluxo Cartão (Stripe)
1. Cliente seleciona Cartão de Crédito
2. Preenche endereço e seleciona frete
3. Sistema cria Payment Intent automaticamente
4. Formulário Stripe Elements aparece
5. Cliente preenche dados do cartão
6. Clica em "Finalizar Pedido"
7. JavaScript intercepta e chama `stripe.confirmCardPayment()`
8. Se pagamento confirmado, chama `placeOrderWithStripe()` no Livewire
9. Sistema cria pedido com status `processing` (já pago)
10. Redireciona para `/checkout/pedido-realizado`

## 🧪 Testando com Cartões de Teste

Use os seguintes cartões de teste do Stripe:

- **Sucesso**: `4242 4242 4242 4242`
- **Falha**: `4000 0000 0000 0002`
- **3D Secure**: `4000 0025 0000 3155`

**Data de validade**: Qualquer data futura (ex: 12/25)
**CVC**: Qualquer 3 dígitos (ex: 123)
**CEP**: Qualquer CEP válido

## 📝 Notas Técnicas

- O Payment Intent é criado automaticamente quando o método de pagamento muda para "card" e o frete está selecionado
- O Payment Intent é recriado quando o frete muda (para atualizar o valor)
- O pagamento com cartão é processado **antes** de criar o pedido no banco
- Se o pagamento falhar, o pedido não é criado
- O status do pedido com cartão é `processing` (já pago), enquanto PIX é `pending` (aguardando pagamento)

## 🔒 Segurança

- Chave secreta do Stripe nunca é exposta no frontend
- Payment Intent é criado no backend
- Dados do cartão nunca passam pelo servidor (processados diretamente pela Stripe)
- Validação de Payment Intent antes de criar pedido

