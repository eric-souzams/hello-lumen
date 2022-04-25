# Transaction API

## Setup do projeto
- Lumen Framework
- Lumen Passport

## Objetivo
Temos 2 tipos de usuários, os comuns e lojistas, ambos têm carteira com dinheiro e realizam transferências entre eles. Vamos nos atentar **somente** ao fluxo de transferência entre dois usuários.

Requisitos:
- Para ambos tipos de usuário, precisamos do Nome Completo, CPF, e-mail e Senha. CPF/CNPJ e e-mails devem ser únicos no sistema. Sendo assim, seu sistema deve permitir apenas um cadastro com o mesmo CPF ou endereço de e-mail.

- Usuários podem enviar dinheiro (efetuar transferência) para lojistas e entre usuários.

- Lojistas **só recebem** transferências, não enviam dinheiro para ninguém.

- Antes de finalizar a transferência, deve-se consultar um serviço autorizador externo, use este mock para simular (https://run.mocky.io/v3/8fafdd68-a090-496f-8c9a-3442cf30dae6).

- A operação de transferência deve ser uma transação (ou seja, revertida em qualquer caso de inconsistência) e o dinheiro deve voltar para a carteira do usuário que envia.

- No recebimento de pagamento, o usuário ou lojista precisa receber notificação enviada por um serviço de terceiro e eventualmente este serviço pode estar indisponível/instável. Use este mock para simular o envio (https://run.mocky.io/v3/b19f7b9f-9cbf-4fc6-ad22-dc30601aec04).

- Este serviço deve ser RESTFul.

### Payload
```json
POST /transaction
{
    "value" : 100.00,
    "payer" : 4,
    "payee" : 15
}
```

## Database Model
```sql
    table: users
        id -> uuid
        name -> string
        email -> string
        document_id -> string
        password -> string

    table: retailers
        id -> uuid
        name -> string
        email -> string
        document_id -> string
        password -> string

    table: wallets
        id -> uuid
        user_id -> foreignUuid
        balance -> decimal
    
    table: wallet_transactions
        id -> uuid
        payer_wallet_id -> foreignUuid
        payee_wallet_id -> foreignUuid
        amount -> decimal
```

## Auth
[ ] Autenticação

[ ] Coverage (Controller/Repository)

## Transaction
[ ] Lojistas não podem fazer uma transferencia

[ ] Criar transferencia de uma conta para outra

[ ] Coverage


<br />

## Materiais úteis
- https://hub.packtpub.com/why-we-need-design-patterns/
- http://br.phptherightway.com/
- https://girorme.github.io/2019/09/04/psr-12-pt-br/
- https://www.atlassian.com/continuous-delivery/software-testing/types-of-software-testing
- https://github.com/exakat/php-static-analysis-tools
- https://martinfowler.com/articles/microservices.htm

## License
The Lumen framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
