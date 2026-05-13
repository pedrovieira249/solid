# SRP — Single Responsibility Principle

> Projeto de estudo sobre o **S** do **SOLID**, com estrutura de pastas baseada em **DDD (Domain-Driven Design)**.

---

## O que é o S do SOLID?

SOLID é um conjunto de 5 princípios de design de software orientado a objetos. O **S** representa o **Single Responsibility Principle (Princípio da Responsabilidade Única)**.

### Definição

> "Uma classe deve ter um, e apenas um, motivo para mudar."
>
> — Robert C. Martin (Uncle Bob)

Em termos práticos: **cada classe deve fazer apenas uma coisa**, e fazê-la bem.

### Como identificar uma violação?

Se você precisa usar a palavra **"e"** para descrever o que uma classe faz, ela provavelmente viola o SRP.

| Descrição | SRP |
|---|---|
| `Pedido` **gerencia pedidos** | ✅ Uma responsabilidade |
| `Pedido` **gerencia pedidos E envia e-mails** | ❌ Duas responsabilidades |
| `StatusDoPedido` **controla o estado do pedido** | ✅ Uma responsabilidade |
| `StatusDoPedido` **controla o estado E notifica o cliente** | ❌ Duas responsabilidades |

### Por que isso importa?

Imagine que você precisa trocar o sistema de e-mail de `mail()` para SendGrid. Se a lógica de e-mail estiver misturada com a lógica de pedidos, você vai ter que mexer numa classe que não deveria ter nada a ver com e-mail. Isso aumenta o risco de bugs e dificulta a manutenção.

Com o SRP aplicado, você só mexe em `EmailService` — o restante do sistema nem sabe que algo mudou.

---

## O que é DDD?

**Domain-Driven Design** é uma abordagem de arquitetura que organiza o código em torno do **domínio do negócio**, não em torno de detalhes técnicos.

O domínio é a área de conhecimento do problema que você está resolvendo. Neste projeto, o domínio é um **sistema de pedidos de e-commerce**.

### Conceitos fundamentais usados neste projeto

#### Entity (Entidade)
Objeto que possui **identidade única** e ciclo de vida. Duas entidades com o mesmo conteúdo mas IDs diferentes são objetos distintos.

```
Exemplo: dois clientes com o mesmo nome são pessoas diferentes.
         O que os distingue é o ID, não os dados.
```

#### Value Object (Objeto de Valor)
Objeto **imutável** que não possui identidade própria. Dois Value Objects com os mesmos dados são considerados iguais.

```
Exemplo: dois endereços com a mesma rua, número e CEP
         são o mesmo endereço — não importa "quem" é o endereço.
```

#### Service (Serviço de Domínio)
Lógica de negócio que **não pertence naturalmente a uma única entidade**. Geralmente coordena a interação entre duas ou mais entidades.

```
Exemplo: calcular o valor total do pedido envolve vários itens.
         Essa lógica não pertence ao Item nem ao Pedido isoladamente.
```

#### Infrastructure (Infraestrutura)
Detalhes **técnicos externos ao domínio**. O domínio não sabe como e-mails são enviados, como dados são gravados no banco, etc. Isso é responsabilidade da infraestrutura.

```
Exemplo: EmailService sabe usar o servidor de e-mail.
         O domínio só sabe que precisa notificar — não como.
```

---

## Estrutura de Pastas

```
src/
├── Application/               ← Orquestração dos casos de uso
│   └── UseCase/
│       ├── CriarPedidoUseCase.php
│       ├── FinalizarPedidoUseCase.php
│       └── CancelarPedidoUseCase.php
│
├── Domain/                    ← Tudo relacionado ao negócio
│   ├── Contract/              ← Interfaces/contratos do domínio
│   │   └── EmailServiceInterface.php
│   │
│   ├── Entity/                ← Objetos com identidade
│   │   ├── Carrinho.php
│   │   ├── Cliente.php
│   │   ├── DadosPedido.php
│   │   ├── Entregas.php
│   │   ├── Item.php
│   │   └── Pedido.php
│   │
│   ├── ValueObject/           ← Objetos imutáveis, sem identidade
│   │   ├── Email.php
│   │   └── Endereco.php
│   │
│   ├── Service/               ← Lógica de negócio entre entidades
│   │   ├── CalculadoraPedido.php
│   │   ├── CarrinhoItens.php
│   │   ├── NotificarPedido.php
│   │   └── StatusDoPedido.php
│   │
│   └── Enum/                  ← Tipos do domínio
│       ├── EnumStatus.php
│       └── EnumTipoEntregas.php
│
└── Infrastructure/            ← Detalhes técnicos externos
    └── Service/
        └── EmailService.php
```

### Por que cada arquivo foi para onde foi?

| Arquivo | Camada | Motivo |
|---|---|---|
| `CriarPedidoUseCase`, `FinalizarPedidoUseCase`, `CancelarPedidoUseCase` | `Application/UseCase` | Orquestram serviços de domínio; são o ponto de entrada da aplicação |
| `EmailServiceInterface` | `Domain/Contract` | Contrato que o domínio define; implementado pela infraestrutura |
| `Pedido`, `Cliente`, `Item` | `Domain/Entity` | Têm identidade (ID) e ciclo de vida |
| `Carrinho`, `DadosPedido` | `Domain/Entity` | Agregam dados com identidade no contexto do pedido |
| `Entregas` | `Domain/Entity` | Representa uma entrega vinculada a um pedido |
| `Endereco` | `Domain/ValueObject` | Imutável, sem ID — dois endereços iguais são o mesmo |
| `Email` | `Domain/ValueObject` | Imutável, representa os dados de uma mensagem |
| `StatusDoPedido` | `Domain/Service` | Lógica de transição de estado entre entidades |
| `CarrinhoItens` | `Domain/Service` | Gerencia a coleção de itens do carrinho |
| `CalculadoraPedido` | `Domain/Service` | Cálculo que envolve múltiplos itens |
| `NotificarPedido` | `Domain/Service` | Decide qual notificação enviar de acordo com o status |
| `EmailService` | `Infrastructure/Service` | Implementa `EmailServiceInterface`; detalhe técnico de envio |

---

## Responsabilidade de cada classe

### Application / UseCase

#### `CriarPedidoUseCase`
Orquestra a criação de um pedido: instancia `Pedido`, aciona `StatusDoPedido` para mudar o status para `PENDENTE` e `NotificarPedido` para enviar o e-mail.

**Responsabilidade única:** coordenar a criação de um pedido.

#### `FinalizarPedidoUseCase`
Orquestra a finalização: aciona `StatusDoPedido` para mudar o status para `FINALIZADO` e `NotificarPedido` para enviar o e-mail de confirmação.

**Responsabilidade única:** coordenar a finalização de um pedido.

#### `CancelarPedidoUseCase`
Orquestra o cancelamento: aciona `StatusDoPedido` para mudar o status para `CANCELADO` e `NotificarPedido` para enviar o e-mail de cancelamento.

**Responsabilidade única:** coordenar o cancelamento de um pedido.

---

### Domain / Contract

#### `EmailServiceInterface`
Interface definida **pelo domínio** que declara o contrato de envio de e-mail. O domínio depende desta abstração — nunca da implementação concreta.

**Por que aqui?** A direção de dependência deve sempre apontar para dentro do domínio. Se o domínio importasse `EmailService` (infraestrutura), uma mudança de provedor de e-mail forçaria alterações no domínio. Com a interface, o domínio fica isolado.

---

### Domain / Entity

#### `Pedido`
Aggregate Root — guarda o ID do pedido e o carrinho. Valida que o carrinho possui um cliente no momento da criação. Não orquestra serviços; isso é responsabilidade dos Use Cases.

**Responsabilidade única:** representar e proteger a integridade de um pedido.

#### `Carrinho`
Agrupa `Cliente` e `DadosPedido`. Representa o carrinho de compras antes de virar um pedido.

**Responsabilidade única:** manter a associação entre cliente e dados do pedido.

#### `Cliente`
Dados de um cliente. Permite cadastrar um endereço após a criação.

**Responsabilidade única:** representar um cliente e seus dados.

#### `Item`
Snapshot imutável de um produto no momento da compra. Não muda após ser criado.

**Responsabilidade única:** representar um item com seu preço e quantidade no momento do pedido.

#### `DadosPedido`
Armazena o estado atual do pedido (itens, valor total, status, entrega). Funciona como um agregador de dados mutável.

**Responsabilidade única:** ser o repositório de estado do pedido.

#### `Entregas`
Representa a entrega associada a um pedido, com tipo, endereço e valor.

**Responsabilidade única:** representar as informações de entrega.

---

### Domain / ValueObject

#### `Endereco`
Imutável. Se auto-valida no construtor. Dois endereços com os mesmos dados são equivalentes.

**Responsabilidade única:** representar e validar um endereço.

#### `Email`
Imutável. Carrega os dados de uma mensagem de e-mail (destinatário, assunto, mensagem).

**Responsabilidade única:** representar os dados de um e-mail.

---

### Domain / Service

#### `StatusDoPedido`
Gerencia as transições de estado de um pedido. Valida se a transição é permitida antes de executá-la.

**Responsabilidade única:** controlar as regras de transição de estado.

```
ABERTO → PENDENTE → FINALIZADO
ABERTO/PENDENTE → CANCELADO
```

#### `CarrinhoItens`
Gerencia a adição e remoção de itens no carrinho. Após qualquer alteração, aciona o recálculo do valor total. Se o pedido não estiver com status `ABERTO`, lança `DomainException` — não produz output, não retorna `false`.

**Responsabilidade única:** gerenciar a coleção de itens do carrinho.

#### `CalculadoraPedido`
Recebe a lista de itens e a entrega, e calcula o valor total.

**Responsabilidade única:** calcular o valor total de um pedido.

#### `NotificarPedido`
Verifica o status atual do pedido e envia o e-mail correspondente via `EmailServiceInterface` (injetada no construtor). Não instancia `EmailService` diretamente — o domínio não conhece a implementação concreta.

**Responsabilidade única:** decidir qual notificação enviar de acordo com o status.

---

### Domain / Enum

#### `EnumStatus`
Define os estados possíveis de um pedido: `ABERTO`, `PENDENTE`, `FINALIZADO`, `CANCELADO`.

#### `EnumTipoEntregas`
Define os tipos de entrega: `RETIRADA_LOJA`, `NORMAL`, `EXPRESSA`, `TURBO`.

---

### Infrastructure / Service

#### `EmailService`
Implementa `EmailServiceInterface`. Recebe o `Email` (Value Object) diretamente no método `send()` e executa o envio.

**Responsabilidade única:** enviar e-mails.

**Por que está na infraestrutura?** O domínio não precisa saber se o e-mail é enviado via `mail()`, SendGrid, Mailgun ou outro serviço. Isso é um detalhe técnico. Se o provedor mudar, cria-se uma nova implementação de `EmailServiceInterface` — o domínio não sabe e não precisa saber.

---

## Fluxo completo de um pedido

```
1. Criar Endereco (ValueObject)
2. Criar Cliente com o Endereco
3. Criar Carrinho com o Cliente
4. Adicionar Items ao Carrinho via CarrinhoItens
   └── CarrinhoItens aciona CalculadoraPedido e atualiza o valor total
5. CriarPedidoUseCase::executar()
   └── Instancia Pedido (valida que o carrinho tem cliente)
   └── StatusDoPedido → muda status para PENDENTE
   └── NotificarPedido → envia e-mail via EmailServiceInterface
6. FinalizarPedidoUseCase::executar()
   └── StatusDoPedido → muda status para FINALIZADO
   └── NotificarPedido → envia e-mail de confirmação
```

---

## Como rodar o projeto

### Pré-requisitos

- PHP 8.1 ou superior
- Composer instalado

### 1. Instalar as dependências

Dentro da pasta do projeto, execute:

```bash
composer install
```

### 2. Rodar pelo terminal

Para executar o `index.php` diretamente no terminal, sem servidor:

```bash
php index.php
```

A saída do `var_dump` do carrinho e do pedido será impressa no próprio terminal.

### 3. Rodar pelo browser

Para visualizar a saída no navegador, suba o servidor embutido do PHP:

```bash
php -S localhost:8080
```

Em seguida, abra o navegador e acesse:

```
http://localhost:8080
```

O PHP usará o `index.php` como ponto de entrada automaticamente. A saída do `var_dump` será exibida com formatação `<pre>` no navegador.

Para encerrar o servidor, pressione `Ctrl + C` no terminal.

---

## Testes

> Os testes unitários deste projeto foram criados com auxílio de IA (GitHub Copilot).

A suíte cobre todas as camadas da aplicação: `Entity`, `ValueObject`, `Service` e `Infrastructure`.

### Estrutura dos testes

```
tests/
├── Application/
│   └── UseCase/
│       ├── CancelarPedidoUseCaseTest.php
│       ├── CriarPedidoUseCaseTest.php
│       └── FinalizarPedidoUseCaseTest.php
├── Domain/
│   ├── Entity/
│   │   ├── CarrinhoTest.php
│   │   ├── ClienteTest.php
│   │   ├── DadosPedidoTest.php
│   │   ├── EntregasTest.php
│   │   ├── ItemTest.php
│   │   └── PedidoTest.php
│   ├── Service/
│   │   ├── CalculadoraPedidoTest.php
│   │   ├── CarrinhoItensTest.php
│   │   ├── NotificarPedidoTest.php
│   │   └── StatusDoPedidoTest.php
│   ├── ValueObject/
│   │   ├── EmailTest.php
│   │   └── EnderecoTest.php
│   └── SrpPrincipleTest.php
└── Infrastructure/
    └── Service/
        └── EmailServiceTest.php
```

### O que cada suite testa

| Arquivo de Teste | O que cobre |
|---|---|
| `CriarPedidoUseCaseTest` | Criação do pedido, mudança de status para PENDENTE, envio de e-mail |
| `FinalizarPedidoUseCaseTest` | Finalização do pedido, status FINALIZADO, exceção para pedido cancelado |
| `CancelarPedidoUseCaseTest` | Cancelamento do pedido, status CANCELADO, exceção para pedido já cancelado |
| `CarrinhoTest` | Getters de cliente e dados do pedido, cliente null permitido |
| `ClienteTest` | Getters, endereço null, cadastro de endereço pós-criação |
| `DadosPedidoTest` | Valores padrão, setters e getters de itens, status, valor e entrega |
| `EntregasTest` | Getters, valor de entrega padrão |
| `ItemTest` | Getters de produto, quantidade e valor unitário |
| `PedidoTest` | Criação com ID e carrinho, exceção sem cliente |
| `CalculadoraPedidoTest` | Cálculo sem itens, com múltiplos itens, com e sem entrega |
| `CarrinhoItensTest` | Adição e remoção de itens, recálculo de total, exceções de status |
| `NotificarPedidoTest` | E-mail correto por status, sem envio para ABERTO, exceção sem cliente |
| `StatusDoPedidoTest` | Todas as transições válidas e exceções para transições inválidas |
| `EmailTest` | Getters de destinatário, assunto e mensagem |
| `EnderecoTest` | Getters, complemento null, exceções para campos vazios |
| `EmailServiceTest` | Retorna true, exibe dados do e-mail na saída |
| `SrpPrincipleTest` | Demonstra que cada serviço atua isolado em sua responsabilidade: `StatusDoPedido` não aciona e-mail, `NotificarPedido` não muda status, `CalculadoraPedido` não altera estado, `CarrinhoItens` delega cálculo |

### Rodar os testes

```bash
./vendor/bin/phpunit
```

Para rodar com detalhamento de cada teste:

```bash
./vendor/bin/phpunit --testdox
```

Para rodar apenas um arquivo específico:

```bash
./vendor/bin/phpunit tests/Domain/Service/StatusDoPedidoTest.php
```

---

## Lição principal do SRP aplicada neste projeto

Antes de aplicar o SRP, `DadosPedido` fazia tudo:

```php
// ❌ Antes — uma classe, três responsabilidades
class DadosPedido {
    public function addItem() { ... }      // gerencia itens
    public function finalizar() { ... }    // gerencia estado
    public function getValorTotal() { ... }// calcula valor
}
```

Após aplicar o SRP, cada responsabilidade tem seu lugar:

```php
// ✅ Depois — cada classe faz uma coisa
class CarrinhoItens    { public function addItem() { ... } }
class StatusDoPedido   { public function finalizar() { ... } }
class CalculadoraPedido{ public function calcularValorTotal() { ... } }
```

Se amanhã a regra de cálculo mudar (ex: adicionar desconto), você toca **apenas** em `CalculadoraPedido`. Nenhuma outra classe precisa ser alterada ou testada novamente.

Isso é o SRP na prática.
