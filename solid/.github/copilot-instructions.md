# GitHub Copilot Instructions — SOLID Principles (PHP)

## Visão geral do repositório

Este repositório contém **5 projetos independentes em PHP**, cada um demonstrando um princípio do SOLID com:

- Cenário de negócio real
- Estrutura em camadas baseada em DDD (`Domain`, `Application`, `Infrastructure`)
- Testes automatizados com PHPUnit 12.x
- PHP 8.1+, tipagem estrita (`declare(strict_types=1)`) em todos os arquivos

```
solid/
├── SRP - Single Responsibility Principle/   # Pedidos de e-commerce
├── OCP - Open Closed Principle/             # Leitura de arquivos
├── LSP - Liskov Substitution Principle/     # Polígonos e cálculo de área
├── ISP - Interface Segregation Principle/   # Cadastro e notificação de usuários
└── DIP - Dependency Inversion Principle/    # Envio de mensagens multicanal
```

Cada projeto tem seu próprio `composer.json`, `phpunit.xml` e `vendor/`.

---

## Convenções de código

### Nomenclatura
- **Classes/Interfaces/Enums:** PascalCase — `LeitorCsv`, `MensageiroInterface`, `EnumStatus`
- **Métodos/variáveis:** camelCase — `calcularValorTotal()`, `$emailService`
- **Testes unitários:** `snake_case` prefixado com `test_` — `test_enviar_mensagem_delega_para_o_canal`
- **Testes de entidade (CamelCase):** `testGetNomeRetornaValorCorreto`
- **Namespaces:** seguem a estrutura `<Projeto>\<Camada>\<Subpasta>` — ex: `SrpSingleresponsibilityprinciple\Domain\Service`

### Arquivos
- Todos os arquivos PHP começam com `<?php` + `declare(strict_types=1);`
- Um namespace e uma classe por arquivo
- Sem comentários óbvios ou decorativos; docblocks apenas quando explicam o *porquê*, não o *o quê*

### Testes
- Framework: PHPUnit 12.x
- Cada projeto tem testes espelhando a estrutura `src/` dentro de `tests/`
- `setUp()` inicializa o SUT (System Under Test) e seus colaboradores
- Mocks (`createMock`) para verificar interações; Stubs (`createStub`) para isolar dependências
- `ob_start()`/`ob_end_clean()` para capturar output nos testes de classes que fazem `echo`
- Arquivos de teste de princípio (ex: `SrpPrincipleTest.php`) validam o princípio por reflection e comportamento — não removê-los

---

## Estrutura de cada projeto

```
<Princípio>/
├── composer.json
├── phpunit.xml
├── index.php                  # Exemplo de uso (não é testado)
├── src/
│   ├── Application/
│   │   └── UseCase/           # Orquestração de casos de uso
│   ├── Domain/
│   │   ├── Contract/          # Interfaces do domínio
│   │   ├── Entity/            # Entidades
│   │   ├── Enum/              # Enums
│   │   ├── Service/           # Serviços de domínio
│   │   └── ValueObject/       # Value Objects imutáveis
│   └── Infrastructure/        # Implementações concretas (DB, Email, etc.)
└── tests/                     # Espelha src/
```

---

## Princípios implementados

### S — SRP (Single Responsibility Principle)
- **Problema:** pedidos de e-commerce
- **Regra:** cada classe tem um único motivo para mudar
- Classes-chave: `StatusDoPedido` (transições), `NotificarPedido` (e-mail), `CalculadoraPedido` (cálculos), `CarrinhoItens` (coleção)
- Nenhum serviço acessa responsabilidade de outro

### O — OCP (Open/Closed Principle)
- **Problema:** leitura de múltiplos formatos de arquivo
- **Regra:** aberta para extensão, fechada para modificação
- Novos formatos são adicionados criando `Leitor<Tipo>` — sem alterar `LerArquivo` ou `LeitorArquivoFactory`
- Factory usa convenção de nome para resolver o leitor correto via `class_exists`

### L — LSP (Liskov Substitution Principle)
- **Problema:** hierarquia de polígonos (Retângulo, Quadrado, Paralelogramo)
- **Regra:** subtipos substituem a classe base sem alterar o comportamento esperado
- `Quadrado` não herda setters de `Retangulo` — recebe apenas `$lado` no construtor
- Todos os subtipos passam pelo `LiskovSubstitutionTest` com `#[DataProvider]`

### I — ISP (Interface Segregation Principle)
- **Problema:** cadastro e notificação de usuários
- **Regra:** clientes não devem ser forçados a depender de métodos que não usam
- Interfaces segregadas: `UsuarioReadInterface`, `UsuarioWriteInterface`, `LogInterface`, `NotificacaoInterface`, `NotificacaoRecipientInterface`
- Entidades recebem serviços via injeção de dependência; nunca implementam contratos de serviço

### D — DIP (Dependency Inversion Principle)
- **Problema:** envio de mensagens por múltiplos canais (Email, SMS, WhatsApp)
- **Regra:** módulos de alto nível dependem de abstrações, não de implementações concretas
- `Mensageiro` (alto nível) depende de `MensageiroInterface`; `Email`, `Sms`, `WhatsApp` (baixo nível) implementam a interface

---

## Como rodar os testes

```bash
cd "SRP - Single Responsibility Principle"
./vendor/bin/phpunit --no-coverage
```

Substitua o nome da pasta pelo princípio desejado. Cada projeto tem `vendor/` já instalado.

---

## O que NÃO fazer ao sugerir código

- Não misturar responsabilidades entre camadas (`Domain` não depende de `Infrastructure`)
- Não fazer `Domain` depender de implementações concretas — sempre usar interfaces
- Não criar construtores com lógica que viola o princípio do projeto
- Não adicionar comentários que apenas descrevem o que o código faz (ex: `// calcula o total`)
- Não usar `echo` fora de `Infrastructure` ou dos arquivos `index.php`
- Não remover `declare(strict_types=1)` de nenhum arquivo
