# Especificação Técnica: Integração Google Wallet & Checkout Pro

## 1. Visão Geral da Arquitetura
Esta documentação detalha a implementação de alto nível da tokenização de pagamentos via Google Wallet (Google Pay) integrada ao gateway Checkout Pro. O fluxo utiliza criptografia assimétrica de ponta a ponta (E2EE) para garantir a integridade dos dados sensíveis do portador (PAN/CVV) durante o trânsito entre o Secure Element (SE) do dispositivo do usuário e os servidores de processamento de transações.

## 2. Pipeline de Tokenização e Criptografia
A integração adota o padrão **Google Pay Payment Data Cryptography** para descriptografia de cargas úteis de pagamento.

### 2.1. Estrutura do Token de Pagamento (PaymentData)
O objeto `PaymentData` retornado pela API do Google Payment contém a propriedade `paymentMethodData`, que encapsula o `tokenizationData`. Este segmento inclui:
- `protocolVersion`: Versão do protocolo de criptografia (ex: `ECv2`).
- `signature`: Assinatura digital do payload para validação de integridade.
- `intermediateSigningKey`: Chave intermediária assinada pela raiz de confiança do Google.
- `signedMessage`: Mensagem JSON contendo o `encryptedMessage`.

O `encryptedMessage` carrega o payload cifrado contendo:
- `messageExpiration`: Timestamp de expiração do criptograma (network time).
- `messageId`: Identificador único da transação (nonce).
- `paymentMethod`: Detalhes de rede (Visa/Mastercard) e tipo de autenticação (CRYPTOGRAM_3DS).

### 2.2. Fluxo de Decriptografia no Backend
O processamento do token requer a execução de operações criptográficas utilizando curvas elípticas (NIST P-256):
1. **Verificação da Assinatura**: Validação da `intermediateSigningKey` contra as chaves públicas rotativas do Google.
2. **Derivação de Chaves (HKDF-SHA256)**: Utilização do algoritmo *Hybrid Key Derivation Function* para derivar chaves simétricas efêmeras a partir da chave privada do comerciante e da chave pública efêmera presente no token.
3. **Decifragem Simétrica (AES-256-CTR)**: Descriptografia do payload `encryptedMessage` utilizando a chave derivada.

## 3. Orquestração de Transações no Checkout Pro
A comunicação com o Checkout Pro ocorre via API RESTful com idempotência garantida por chaves de idempotência (UUID v4).

### 3.1. Handshake e Configuração de Pagamento
O `PaymentIntent` deve ser instanciado com parâmetros específicos para carteiras digitais:
```json
{
  "transaction_amount": 150.00,
  "token": "TOK_REC_GOOGLE_PAY_...",
  "description": "Ordem #94821 - E-commerce Flow",
  "payment_method_id": "google_pay",
  "payer": {
    "email": "customer@email.com",
    "authentication_type": "3DS_2_0"
  },
  "binary_mode": true
}
```

### 3.2. Tratamento de Webhooks e Conciliação Assíncrona
Devido à natureza distribuída do processamento bancário, o sistema implementa um listener de notificações instantâneas (IPN) que processa eventos de `payment.updated`.
- **Validação de Assinatura HMAC-SHA256**: Cada notificação recebe um header `x-signature` que deve ser computado com a secret key do webhook para evitar ataques de *replay* ou *man-in-the-middle*.
- **Fila de Processamento (Redis/Horizon)**: Eventos são enfileirados para evitar *race conditions* na atualização de status de pedidos no banco relacional (ACID compliance).

## 4. Conformidade PCI-DSS e Segurança
Embora o Google Pay reduza o escopo PCI, o ambiente de processamento mantém conformidade SAQ-A-EP. Nenhuma informação de cartão (PAN) é persistida em logs ou bancos de dados locais. O ambiente utiliza TLS 1.3 com *Perfect Forward Secrecy* para todas as comunicações externas.

## 5. Fallback e Recuperação de Falhas
Implementação de *Circuit Breaker* para detecção de latência no gateway de pagamento. Em caso de timeout (>5000ms) ou falha na validação do criptograma, o sistema realiza o *soft decline* e solicita ao usuário um método de pagamento alternativo, mantendo a sessão de checkout ativa.
