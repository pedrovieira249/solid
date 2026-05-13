# DIP — Dependency Inversion Principle

> Projeto de estudo sobre o **D** do **SOLID**, com estrutura de pastas baseada em **DDD (Domain-Driven Design)**.

---

## O que é o D do SOLID?

SOLID é um conjunto de 5 princípios de design de software orientado a objetos. O **D** representa o **Dependency Inversion Principle (Princípio da Inversão de Dependência)**.

### Definição

> "Módulos de alto nível não devem depender de módulos de baixo nível. Ambos devem depender de abstrações."
> "Abstrações não devem depender de detalhes. Detalhes devem depender de abstrações."
>
> — Robert C. Martin (Uncle Bob)

Em termos práticos: **dependa de interfaces, não de implementações concretas**. Quem define o contrato é o domínio — a infraestrutura se adapta a ele, não o contrário.

### Como identificar uma violação?

Se um módulo de alto nível instancia diretamente uma classe concreta de infraestrutura, o DIP está sendo violado.

| Situação | DIP |
|---|---|
| `Mensageiro` recebe `MensageiroInterface` via construtor | ✅ Depende da abstração |
| `Mensageiro` instancia `new Email()` internamente | ❌ Acoplado ao detalhe concreto |
| `Email`, `Sms`, `WhatsApp` implementam `MensageiroInterface` | ✅ Detalhes dependem da abstração |
| `MensageiroInterface` definida no domínio | ✅ O domínio define o contrato |
| `MensageiroInterface` definida junto com `Email` na infraestrutura | ❌ O detalhe estaria definindo o contrato |

### Por que isso importa?

Imagine que `Mensageiro` instanciasse `new Email()` diretamente. Se você precisar trocar para SMS:

- Precisa **modificar** `Mensageiro` — viola o OCP também
- Testes exigem que o servidor de e-mail esteja disponível
- Não há como testar `Mensageiro` em isolamento

Com o DIP:

```php
// ❌ Sem DIP — Mensageiro acoplado ao detalhe
class Mensageiro
{
    public function enviarMensagem(string $contato, string $mensagem): bool
    {
        return (new Email())->enviarMensagem($contato, $mensagem);
    }
}

// ✅ Com DIP — Mensageiro depende da abstração
class Mensageiro
{
    public function __construct(
        private MensageiroInterface $canal
    ) {}

    public function enviarMensagem(string $contato, string $mensagem): bool
    {
        return $this->canal->enviarMensagem($contato, $mensagem);
    }
}
```

Com a abstração:

- Qualquer canal (`Email`, `Sms`, `WhatsApp`, ou qualquer novo canal futuro) pode ser injetado sem alterar `Mensageiro`
- Testes usam um mock de `MensageiroInterface` — sem dependências externas
- O domínio permanece estável mesmo quando a infraestrutura muda

---

## O que é DDD?

**Domain-Driven Design** é uma abordagem de arquitetura que organiza o código em torno do **domínio do negócio**, não em torno de detalhes técnicos.

O domínio é a área de conhecimento do problema que você está resolvendo. Neste projeto, o domínio é um **sistema de envio de mensagens**.

### Conceitos fundamentais usados neste projeto

#### Contract (Contrato / Interface)
Define **o que** um objeto deve fazer, sem ditar **como**. No DIP, o contrato é **sempre definido pelo domínio** — nunca pela infraestrutura.

```
Exemplo: MensageiroInterface declara enviarMensagem() e enviarToken().
         O domínio define o contrato; Email, Sms e WhatsApp o implementam.
```

#### Service (Serviço de Domínio)
Lógica de negócio que não pertence a uma entidade. Aqui, `Mensageiro` é um serviço que **orquestra** o envio via qualquer canal injetado — sem saber qual canal é.

```
Exemplo: Mensageiro recebe MensageiroInterface e delega para ela.
         Não sabe se é Email, Sms ou WhatsApp.
```

#### Infrastructure (Infraestrutura)
Detalhes **técnicos externos ao domínio**. Os canais de comunicação concretos (`Email`, `Sms`, `WhatsApp`) são detalhes de infraestrutura — eles implementam o contrato do domínio, mas o domínio não os conhece.

```
Exemplo: Email sabe como enviar via SMTP.
         O domínio só sabe que existe algo que implementa MensageiroInterface.
```

---

## Estrutura de Pastas

```
src/
├── Domain/                            ← Tudo relacionado ao negócio
│   ├── Contract/                      ← Abstrações definidas pelo domínio
│   │   └── MensageiroInterface.php    ← Contrato de envio de mensagens
│   │
│   └── Service/                       ← Serviços de domínio
│       └── Mensageiro.php             ← Módulo de alto nível; depende da abstração
│
└── Infrastructure/                    ← Detalhes técnicos externos
    └── Messaging/                     ← Implementações concretas dos canais
        ├── Email.php
        ├── Sms.php
        └── WhatsApp.php

tests/
├── Domain/
│   └── Service/
│       └── MensageiroTest.php         ← Testa delegação e comportamento com mocks
│
└── Infrastructure/
    └── Messaging/
        ├── EmailTest.php
        ├── SmsTest.php
        └── WhatsAppTest.php
```

### Por que cada arquivo foi para onde foi?

| Arquivo | Camada | Motivo |
|---|---|---|
| `MensageiroInterface` | `Domain/Contract` | O domínio define o contrato — a infraestrutura se adapta a ele |
| `Mensageiro` | `Domain/Service` | Módulo de alto nível; só conhece a abstração `MensageiroInterface` |
| `Email` | `Infrastructure/Messaging` | Detalhe técnico de envio; implementa o contrato do domínio |
| `Sms` | `Infrastructure/Messaging` | Detalhe técnico de envio; implementa o contrato do domínio |
| `WhatsApp` | `Infrastructure/Messaging` | Detalhe técnico de envio; implementa o contrato do domínio |

### Por que `Email`, `Sms` e `WhatsApp` não ficam em `Domain/Entity`?

`Entity` é reservado para objetos com **identidade única e ciclo de vida** (ex: `Usuario`, `Pedido`). `Email`, `Sms` e `WhatsApp` são **implementações de um mecanismo técnico de entrega** — eles são detalhes de infraestrutura, não entidades de negócio. Colocá-los em `Domain/Entity` seria misturar a lógica de domínio com detalhes técnicos, o que viola a separação de camadas do DDD.

### Por que `MensageiroInterface` fica no `Domain` e não na `Infrastructure`?

Esse é o ponto central do DIP: **quem define o contrato é quem depende dele**, não quem o implementa. `Mensageiro` (domínio) precisa de um canal de envio — então ele define como esse canal deve se comportar via `MensageiroInterface`. As classes de infraestrutura (`Email`, `Sms`, `WhatsApp`) então se adaptam a esse contrato. Se a interface ficasse na infraestrutura, o domínio dependeria da infraestrutura — exatamente o que o DIP proíbe.

---

## Responsabilidade de cada classe

### Domain / Contract

#### `MensageiroInterface`
Define o contrato de qualquer canal de envio de mensagens:

- `enviarMensagem(string $contato, string $mensagem): bool`
- `enviarToken(string $contato, string $token): bool`

Qualquer canal que implemente esta interface pode ser injetado em `Mensageiro` sem nenhuma alteração no domínio.

---

### Domain / Service

#### `Mensageiro`
Serviço de alto nível responsável por enviar mensagens e tokens. Recebe `MensageiroInterface` via construtor e delega todas as operações para ela.

**Responsabilidade única:** orquestrar o envio através do canal injetado, sem conhecer qual canal é.

```php
$mensageiro = new Mensageiro(new Email());
$mensageiro->enviarMensagem('contato@example.com', 'Olá!');

$mensageiro = new Mensageiro(new Sms());
$mensageiro->enviarToken('5531987654321', '123456');
```

---

### Infrastructure / Messaging

#### `Email`
Implementa `MensageiroInterface`. Contém a lógica concreta de envio via e-mail.

#### `Sms`
Implementa `MensageiroInterface`. Contém a lógica concreta de envio via SMS.

#### `WhatsApp`
Implementa `MensageiroInterface`. Contém a lógica concreta de envio via WhatsApp.

Todas as três são **detalhes** — podem ser substituídas, adicionadas ou removidas sem que `Mensageiro` precise ser alterado.

---

## Testes

Os testes estão organizados espelhando a estrutura de `src/` e cobrem dois focos distintos:

### `MensageiroTest` — valida o DIP em ação

Usa mocks e stubs de `MensageiroInterface` para testar `Mensageiro` em total isolamento — sem instanciar nenhuma classe de infraestrutura:

| Teste | O que valida |
|---|---|
| `test_enviar_mensagem_delega_para_o_canal` | `enviarMensagem` chama exatamente uma vez o método do canal injetado |
| `test_enviar_token_delega_para_o_canal` | `enviarToken` chama exatamente uma vez o método do canal injetado |
| `test_enviar_mensagem_retorna_false_quando_canal_falha` | `Mensageiro` propaga o `false` retornado pelo canal |
| `test_enviar_token_retorna_false_quando_canal_falha` | `Mensageiro` propaga o `false` retornado pelo canal |
| `test_aceita_qualquer_implementacao_de_mensageiro_interface` | Qualquer stub de `MensageiroInterface` pode ser injetado |

> **Nota:** os testes sem `expects()` usam `createStub()` em vez de `createMock()` — prática correta no PHPUnit 12, que emite notice quando um mock object não tem expectativas configuradas.

### `EmailTest`, `SmsTest`, `WhatsAppTest` — validam a infraestrutura

| Teste | O que valida |
|---|---|
| `test_implementa_mensageiro_interface` | A classe satisfaz o contrato do domínio (`assertInstanceOf`) |
| `test_enviar_mensagem_retorna_true` | O método retorna `true` em execução normal |
| `test_enviar_token_retorna_true` | O método retorna `true` em execução normal |
| `test_enviar_mensagem_exibe_contato_e_mensagem` | O output contém o contato e a mensagem (`expectOutputRegex`) |
| `test_enviar_token_exibe_contato_e_token` | O output contém o contato e o token (`expectOutputRegex`) |

### Resultado

```
OK (20 tests, 33 assertions)
```

---

## Como executar

```bash
# Instalar dependências
composer install

# Rodar os testes
./vendor/bin/phpunit --testdox

# Rodar o exemplo
php index.php
```
