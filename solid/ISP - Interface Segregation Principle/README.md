# ISP — Interface Segregation Principle

> Projeto de estudo sobre o **I** do **SOLID**, com estrutura de pastas baseada em **DDD (Domain-Driven Design)**.

---

## O que é o I do SOLID?

SOLID é um conjunto de 5 princípios de design de software orientado a objetos. O **I** representa o **Interface Segregation Principle (Princípio da Segregação de Interface)**.

### Definição

> "Nenhum cliente deve ser forçado a depender de métodos que não usa."
>
> — Robert C. Martin (Uncle Bob)

Em termos práticos: **prefira várias interfaces pequenas e focadas a uma interface grande e genérica**. Cada classe deve implementar apenas os contratos que realmente precisam dela.

### Como identificar uma violação?

Se uma classe implementa uma interface mas precisa deixar alguns métodos vazios ou lançar exceção porque "não se aplica", o ISP está sendo violado.

| Situação | ISP |
|---|---|
| `Lead` implementa apenas `LeadInterface` (só `gerar()`) | ✅ Contrato focado |
| `Lead` implementa uma `EntidadeInterface` com `salvar()`, `editar()`, `excluir()`, `gerar()`, `enviarEmail()` | ❌ Força métodos que Lead não usa |
| `UsuarioWriteInterface` só tem operações de escrita | ✅ Segregada por intenção |
| `UsuarioInterface` mistura `buscarPorId()` com `salvar()`, `editar()`, `excluir()` | ❌ Clientes que só leem são forçados a depender de escrita |
| `Notificacao` recebe `NotificacaoRecipientInterface` (só `getNome()` e `getEmail()`) | ✅ Contrato mínimo necessário |
| `Notificacao` recebe `Usuario` concreto (expõe `getId()`, `salvar()`, `excluir()`...) | ❌ Acoplamento a métodos que Notificacao nunca usa |

### Por que isso importa?

Imagine uma interface `EntidadeInterface` com 10 métodos. Toda entidade do sistema teria que implementar todos — mesmo que uma entidade de leitura nunca precise de `salvar()` ou `excluir()`. Com o ISP:

- Cada classe implementa exatamente o que faz
- Mudanças em contratos de escrita não afetam clientes de leitura
- Mocks em testes ficam menores e mais precisos — você stubba só o que o colaborador realmente precisa

---

## O que é DDD?

**Domain-Driven Design** é uma abordagem de arquitetura que organiza o código em torno do **domínio do negócio**, não em torno de detalhes técnicos.

O domínio é a área de conhecimento do problema que você está resolvendo. Neste projeto, o domínio é um **sistema de cadastro e notificação de usuários**.

### Conceitos fundamentais usados neste projeto

#### Entity (Entidade)
Objeto que possui **identidade única** e ciclo de vida. Duas entidades com o mesmo conteúdo mas IDs diferentes são objetos distintos.

```
Exemplo: dois usuários com o mesmo nome são pessoas diferentes.
         O que os distingue é o ID, não os dados.
```

#### Contract (Contrato / Interface)
Define **o que** um objeto deve fazer, sem ditar **como**. No contexto do ISP, cada contrato é pequeno e focado em um único tipo de cliente.

```
Exemplo: NotificacaoRecipientInterface declara só getNome() e getEmail().
         Notificacao não precisa saber mais nada sobre o destinatário.
```

#### Service (Serviço de Domínio)
Lógica de negócio que **não pertence naturalmente a uma única entidade**. Serviços implementam contratos do domínio — são testáveis, trocáveis e isolados.

```
Exemplo: Log implementa LogInterface.
         Quem usa o logger não depende da classe Log — depende da interface.
```

#### Application (Camada de Aplicação)
Orquestra entidades e serviços para realizar uma ação. Implementações concretas de contratos que envolvem infraestrutura ou coordenação entre camadas pertencem aqui.

```
Exemplo: Notificacao concreta fica em Application/Service porque
         enviar notificações é coordenação de aplicação, não lógica de domínio puro.
```

#### Infrastructure (Infraestrutura)
Detalhes **técnicos externos ao domínio**. O domínio não sabe como a conexão com o banco é feita. `DBConnection` é um detalhe técnico.

```
Exemplo: DBConnection sabe conectar ao MySQL.
         As entidades estendem DBConnection apenas para ter acesso à conexão,
         mas o domínio não depende dos detalhes do driver.
```

---

## Estrutura de Pastas

```
src/
├── Application/                       ← Coordenação de casos de uso
│   └── Service/
│       └── Notificacao.php            ← Implementação concreta de NotificacaoInterface
│
├── Domain/                            ← Tudo relacionado ao negócio
│   ├── Contract/                      ← Interfaces pequenas e segregadas
│   │   ├── ContratoInterface.php
│   │   ├── LeadInterface.php
│   │   ├── LogInterface.php
│   │   ├── NotificacaoInterface.php
│   │   ├── NotificacaoRecipientInterface.php
│   │   ├── UsuarioReadInterface.php
│   │   └── UsuarioWriteInterface.php
│   │
│   ├── Entity/                        ← Objetos com identidade
│   │   ├── Contrato.php
│   │   ├── Lead.php
│   │   └── Usuario.php
│   │
│   └── Service/                       ← Serviços de domínio
│       └── Log.php
│
└── Infrastructure/                    ← Detalhes técnicos externos
    └── Database/
        └── DBConnection.php
```

### Por que cada arquivo foi para onde foi?

| Arquivo | Camada | Motivo |
|---|---|---|
| `Notificacao` | `Application/Service` | Enviar notificações é coordenação de aplicação — não lógica de domínio puro |
| `ContratoInterface` | `Domain/Contract` | Contrato focado: só `salvar()` |
| `LeadInterface` | `Domain/Contract` | Contrato focado: só `gerar()` |
| `LogInterface` | `Domain/Contract` | Contrato focado: só `registrarLog()` |
| `NotificacaoInterface` | `Domain/Contract` | Contrato focado: só `enviarNotificacao()` |
| `NotificacaoRecipientInterface` | `Domain/Contract` | Expõe o mínimo necessário para notificar: `getNome()` e `getEmail()` |
| `UsuarioReadInterface` | `Domain/Contract` | Segregada para leitura: só `buscarPorId()` |
| `UsuarioWriteInterface` | `Domain/Contract` | Segregada para escrita: `salvar()`, `editar()`, `excluir()`, `cadastrarOuAtualizar()` |
| `Contrato`, `Lead`, `Usuario` | `Domain/Entity` | Possuem identidade e ciclo de vida |
| `Log` | `Domain/Service` | Implementa `LogInterface`; lógica de registro pertence ao domínio |
| `DBConnection` | `Infrastructure/Database` | Detalhe técnico de conexão ao banco de dados |

### Por que `UsuarioInterface` foi dividida em duas?

Um módulo que só precisa **buscar** um usuário não deve ser forçado a conhecer `salvar()` ou `excluir()`. Se as duas estivessem em uma interface única:

- Um repositório de leitura seria obrigado a implementar `editar()` mesmo não fazendo sentido para ele
- Uma mudança na assinatura de `salvar()` quebraria o tipo de quem só usa leitura

Com a segregação, cada cliente declara exatamente o que precisa:

```php
// Módulo de leitura — depende apenas de UsuarioReadInterface
function exibirPerfil(UsuarioReadInterface $usuario): void { ... }

// Módulo de escrita — depende apenas de UsuarioWriteInterface
function cadastrar(UsuarioWriteInterface $usuario): void { ... }
```

### Por que `Notificacao` está em `Application` e não em `Domain`?

`Notificacao` coordena o envio de mensagens para um destinatário. Esse é um concern de **aplicação** — a orquestração de "o quê enviar e para quem". O domínio define apenas o contrato (`NotificacaoInterface`) e o que é necessário do destinatário (`NotificacaoRecipientInterface`). A implementação concreta fica em `Application` porque pode, no futuro, depender de infraestrutura (e-mail, SMS, push) sem contaminar o domínio.

---

## O ISP aplicado: por que as entidades não implementam `LogInterface`

Antes da refatoração, `Lead` e `Usuario` implementavam `LogInterface` diretamente:

```php
// ❌ Violação do ISP: Lead é forçado a SE TORNAR um logger
class Lead implements LeadInterface, LogInterface
{
    public function registrarLog(string $message): void
    {
        (new Log())->registrarLog($message); // instancia dentro da própria classe
    }
}
```

O problema: `Lead` não É um logger — ele PRECISA de um. Implementar `LogInterface` força `Lead` a expor um contrato que não faz parte da sua identidade, e cria um acoplamento desnecessário com a classe `Log`.

Após a correção:

```php
// ✅ ISP respeitado: Lead recebe o logger via DI, não é um logger
class Lead implements LeadInterface
{
    public function __construct(
        private string $nome,
        private string $email,
        private LogInterface $logger  // injeção de dependência
    ) {}
}
```

Resultado: `Lead` depende apenas do seu próprio contrato. O logger pode ser qualquer implementação de `LogInterface` — inclusive um mock nos testes.

---

## Responsabilidade de cada classe

### Application / Service

#### `Notificacao`
Implementa `NotificacaoInterface`. Recebe um `NotificacaoRecipientInterface` e uma mensagem, e exibe a notificação. Não conhece `Usuario` — depende apenas do contrato mínimo.

**Responsabilidade única:** enviar notificações para qualquer recipient que satisfaça `NotificacaoRecipientInterface`.

---

### Domain / Contract

#### `ContratoInterface`
Define o contrato de um contrato jurídico/comercial: apenas `salvar(): bool`.

#### `LeadInterface`
Define o contrato de um lead: apenas `gerar(): bool`.

#### `LogInterface`
Define o contrato de registro de log: apenas `registrarLog(string $message): void`.

#### `NotificacaoInterface`
Define o contrato de envio de notificação: `enviarNotificacao(NotificacaoRecipientInterface $recipient, string $mensagem): bool`. O recipient é passado na chamada — elimina dependência circular e permite reusar o serviço com qualquer destinatário.

#### `NotificacaoRecipientInterface`
Define o mínimo que um destinatário de notificação precisa expor: `getNome()` e `getEmail()`. Qualquer entidade que implemente esta interface pode receber notificações — sem precisar expor `getId()`, `salvar()` ou qualquer outro método irrelevante para o envio.

#### `UsuarioReadInterface`
Contrato de **leitura** do usuário: apenas `buscarPorId(): static|null`.

#### `UsuarioWriteInterface`
Contrato de **escrita** do usuário: `salvar()`, `editar()`, `excluir()` e `cadastrarOuAtualizar()`.

---

### Domain / Entity

#### `Contrato`
Implementa apenas `ContratoInterface`. Não é forçado a implementar `gerar()`, `registrarLog()` ou qualquer outro método que não pertence a um contrato.

**Responsabilidade única:** representar e persistir um contrato.

#### `Lead`
Implementa apenas `LeadInterface`. Recebe `LogInterface` via construtor — não implementa a interface, apenas a usa.

**Responsabilidade única:** representar um lead e delegar o registro de log ao serviço injetado.

#### `Usuario`
Implementa `UsuarioWriteInterface`, `UsuarioReadInterface` e `NotificacaoRecipientInterface`. Recebe `LogInterface` e `NotificacaoInterface` via construtor — não implementa nenhuma das duas, apenas as usa.

Além dos métodos exigidos pelas interfaces, expõe dois métodos de conveniência que delegam para os serviços injetados:

- `registrarLog(string $mensagem)` — prefixia a mensagem com o nome do usuário e repassa ao logger
- `enviarNotificacao(string $mensagem)` — chama `$this->notificacao->enviarNotificacao($this, $mensagem)` passando o próprio `Usuario` como recipient (o que é válido pois `Usuario` implementa `NotificacaoRecipientInterface`)

**Responsabilidade única:** representar um usuário e delegar log e notificação aos serviços injetados.

---

### Domain / Service

#### `Log`
Implementa `LogInterface`. Exibe a mensagem no output.

**Responsabilidade única:** registrar mensagens de log.

---

### Infrastructure / Database

#### `DBConnection`
Simula uma conexão com o banco de dados. As entidades estendem esta classe para ter acesso à conexão.

**Responsabilidade única:** gerenciar a conexão com o banco de dados.

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

A saída das entidades e serviços será impressa no próprio terminal.

### 3. Rodar pelo browser

Para visualizar a saída no navegador, suba o servidor embutido do PHP:

```bash
php -S localhost:8080
```

Em seguida, abra o navegador e acesse:

```
http://localhost:8080
```

O PHP usará o `index.php` como ponto de entrada automaticamente. A saída será exibida com formatação `<pre>` no navegador.

Para encerrar o servidor, pressione `Ctrl + C` no terminal.

---

## Testes

> Os testes unitários deste projeto foram criados com auxílio de IA (GitHub Copilot).

A suíte cobre todas as camadas da aplicação: `Entity`, `Service` e `Application`.

### Estrutura dos testes

```
tests/
├── Application/
│   └── Service/
│       └── NotificacaoTest.php
└── Domain/
    ├── Entity/
    │   ├── ContratoTest.php
    │   ├── LeadTest.php
    │   └── UsuarioTest.php
    ├── Service/
    │   └── LogTest.php
    └── IspPrincipleTest.php
```

### O que cada suite testa

| Arquivo de Teste | O que cobre |
|---|---|
| `LogTest` | Implementa `LogInterface`, output da mensagem de log |
| `NotificacaoTest` | Implementa `NotificacaoInterface`, exibe nome e e-mail do recipient, funciona com qualquer `NotificacaoRecipientInterface` sem depender de `Usuario` concreto |
| `ContratoTest` | Implementa `ContratoInterface`, `salvar()` retorna `true` |
| `LeadTest` | Implementa `LeadInterface`, não implementa `LogInterface`, delega log ao serviço injetado |
| `UsuarioTest` | Implementa as interfaces corretas, getters, operações de CRUD, delegação de log e notificação via DI |
| `IspPrincipleTest` | **Valida o princípio ISP** via Reflection: interfaces de leitura e escrita são distintas e sem sobreposição, `NotificacaoRecipientInterface` tem apenas os 2 métodos necessários, `Notificacao` depende da interface e não da entidade concreta, entidades não implementam `LogInterface` |

### Como o `IspPrincipleTest` funciona

Ao contrário dos testes de comportamento, o `IspPrincipleTest` usa **PHP Reflection** para inspecionar os contratos em si — não apenas o resultado das operações. Isso permite verificar o princípio estruturalmente:

```php
// Verifica que UsuarioReadInterface não tem métodos de escrita
public function test_interface_de_leitura_nao_contem_metodos_de_escrita(): void
{
    $readMethods = array_map(
        fn(\ReflectionMethod $m) => $m->name,
        (new \ReflectionClass(UsuarioReadInterface::class))->getMethods()
    );

    $this->assertContains('buscarPorId', $readMethods);
    $this->assertNotContains('salvar', $readMethods);  // ← ISP garantido estruturalmente
}

// Verifica que Notificacao depende da interface, não da entidade concreta
public function test_notificacao_depende_de_recipient_interface_nao_de_usuario_concreto(): void
{
    $param = (new \ReflectionClass(Notificacao::class))
        ->getMethod('enviarNotificacao')
        ->getParameters()[0];

    $this->assertSame(NotificacaoRecipientInterface::class, $param->getType()->getName());
}
```

As verificações cobrem 5 grupos:

| Grupo | O que valida |
|---|---|
| Serviços implementam apenas seu contrato | `Log` não implementa `NotificacaoInterface`; `Notificacao` não implementa `LogInterface` |
| Entidades não implementam contratos de serviço | `Lead` e `Usuario` não implementam `LogInterface` — usam via DI |
| Interfaces de leitura e escrita são segregadas | `UsuarioReadInterface` ∩ `UsuarioWriteInterface` = ∅ (sem sobreposição) |
| `NotificacaoRecipientInterface` é mínima | Exatamente 2 métodos: `getNome()` e `getEmail()` — nada mais |
| `Notificacao` depende de interface, não de `Usuario` | O parâmetro de `enviarNotificacao` é `NotificacaoRecipientInterface`, não `Usuario` |


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
./vendor/bin/phpunit tests/Domain/IspPrincipleTest.php
```

---

## Lição principal do ISP aplicada neste projeto

### O problema: interface gorda que força dependências desnecessárias

Antes de aplicar o ISP, seria tentador criar uma única interface para tudo que um usuário pode fazer:

```php
// ❌ Antes — interface única e inflada
interface UsuarioInterface
{
    public function buscarPorId(): static|null;
    public function salvar(): bool;
    public function editar(): bool;
    public function excluir(): bool;
    public function cadastrarOuAtualizar(): static;
    public function registrarLog(string $mensagem): void;
    public function enviarNotificacao(string $mensagem): bool;
    public function getNome(): string;
    public function getEmail(): string;
}

// Toda classe que só precisa exibir o nome do usuário
// é forçada a depender de salvar(), excluir(), registrarLog()...
function exibirPerfil(UsuarioInterface $usuario): void
{
    echo $usuario->getNome(); // usa 1 de 9 métodos
}

// Serviço de notificação — só precisa de nome e email
// mas é forçado a conhecer salvar(), editar(), excluir()...
function notificar(UsuarioInterface $usuario, string $msg): void
{
    echo "Para: {$usuario->getNome()} ({$usuario->getEmail()})\n{$msg}";
}
```

### A solução: interfaces pequenas e focadas por cliente

Após aplicar o ISP, cada contrato declara exatamente o que seu cliente precisa:

```php
// ✅ Depois — interfaces segregadas
interface UsuarioReadInterface
{
    public function buscarPorId(): static|null;  // só quem lê usa isso
}

interface UsuarioWriteInterface
{
    public function salvar(): bool;
    public function editar(): bool;
    public function excluir(): bool;
    public function cadastrarOuAtualizar(): static;
}

interface NotificacaoRecipientInterface
{
    public function getNome(): string;   // mínimo para notificar
    public function getEmail(): string;
}

// Agora cada cliente declara apenas o contrato que precisa
function exibirPerfil(UsuarioReadInterface $u): void { ... }        // depende de 1 método
function cadastrar(UsuarioWriteInterface $u): void { ... }          // depende de escrita
function notificar(NotificacaoRecipientInterface $r): void { ... }  // depende de 2 métodos
```

Adicionar um novo tipo de destinatário de notificação (ex: um `Lead`) não toca em `UsuarioReadInterface` nem em `UsuarioWriteInterface`. Mudar a assinatura de `salvar()` não afeta quem só usa `buscarPorId()`. Cada contrato cresce, muda e evolui de forma independente — isso é o ISP na prática.
