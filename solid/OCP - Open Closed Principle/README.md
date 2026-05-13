# OCP — Open/Closed Principle

> Projeto de estudo sobre o **O** do **SOLID**, com estrutura de pastas baseada em **DDD (Domain-Driven Design)**.

---

## O que é o O do SOLID?

SOLID é um conjunto de 5 princípios de design de software orientado a objetos. O **O** representa o **Open/Closed Principle (Princípio do Aberto/Fechado)**.

### Definição

> "Entidades de software devem estar abertas para extensão, mas fechadas para modificação."
>
> — Bertrand Meyer / Robert C. Martin (Uncle Bob)

Em termos práticos: **você deve conseguir adicionar novos comportamentos sem alterar o código existente**.

### Como identificar uma violação?

Se toda vez que você adiciona um novo tipo ou comportamento precisa abrir uma classe existente e adicionar um `if` ou `switch`, o OCP está sendo violado.

| Situação | OCP |
|---|---|
| Adicionar suporte a TXT cria um novo `LeitorTxt` | ✅ Extensão sem modificação |
| Adicionar suporte a TXT exige `if ($tipo === 'TXT')` dentro do `Leitor` | ❌ Modificação de código existente |
| Nova regra de validação cria nova classe | ✅ Extensão sem modificação |
| Nova regra de validação adiciona `elseif` numa classe existente | ❌ Modificação de código existente |

### Por que isso importa?

Imagine que o sistema lê arquivos CSV hoje. Amanhã surge a necessidade de ler TXT, depois XLSX. Se toda adição exige mexer na classe `Leitor`, você:

- Aumenta o risco de quebrar o que já funciona
- Precisa retestar tudo que já existia
- Viola o contrato com quem depende da classe

Com o OCP aplicado, você cria um `LeitorXlsx` — e nenhuma outra classe é alterada.

---

## O que é DDD?

**Domain-Driven Design** é uma abordagem de arquitetura que organiza o código em torno do **domínio do negócio**, não em torno de detalhes técnicos.

O domínio é a área de conhecimento do problema que você está resolvendo. Neste projeto, o domínio é um **sistema de leitura de arquivos de dados**.

### Conceitos fundamentais usados neste projeto

#### Entity (Entidade)
Objeto que possui **identidade única** e ciclo de vida. Neste projeto, `Arquivo` é a entidade central: carrega nome, tipo e diretório que a identificam de forma única.

```
Exemplo: dois arquivos com o mesmo nome mas em diretórios diferentes
         são arquivos distintos — o caminho completo os diferencia.
```

#### Use Case (Caso de Uso)
Orquestra entidades e serviços para realizar **uma ação de negócio específica**. Pertence à camada `Application`, que conecta o domínio à infraestrutura sem pertencer a nenhum dos dois.

```
Exemplo: LerArquivo recebe um Arquivo e um leitor, e retorna os dados.
         Ele não sabe como o CSV é parseado nem onde o arquivo está no disco.
```

#### Service (Serviço de Domínio)
Lógica de negócio que **não pertence naturalmente a uma única entidade**. Aqui, a validação do arquivo e o contrato de leitura vivem em serviços.

```
Exemplo: validar se o arquivo existe e se a extensão bate com o tipo
         não é responsabilidade do Arquivo nem do Leitor isoladamente.
```

#### Infrastructure (Infraestrutura)
Detalhes **técnicos externos ao domínio**. O domínio não sabe como um CSV é parseado nem como um TXT é lido linha a linha. Isso é responsabilidade da infraestrutura.

```
Exemplo: LeitorCsv sabe usar str_getcsv e tratar CRLF.
         O domínio só sabe que precisa de um leitor — não como ele funciona.
```

---

## Estrutura de Pastas

```
src/
├── Application/                     ← Casos de uso (orquestração)
│   └── UseCase/
│       └── LerArquivo.php
│
├── Domain/                          ← Tudo relacionado ao negócio
│   ├── Entity/                      ← Objetos com identidade
│   │   └── Arquivo.php
│   │
│   ├── Service/                     ← Contratos e lógica de negócio
│   │   ├── LeitorArquivoInterface.php
│   │   └── ValidarArquivo.php
│   │
│   └── Enum/                        ← Tipos do domínio
│       └── EnumTipoArquivo.php
│
├── Infrastructure/                  ← Detalhes técnicos externos
│   └── Leitor/
│       ├── LeitorArquivoFactory.php
│       ├── LeitorCsv.php
│       └── LeitorTxt.php
│
└── resources/                       ← Arquivos de dados
    ├── dados.csv
    └── dados.txt
```

### Por que cada arquivo foi para onde foi?

| Arquivo | Camada | Motivo |
|---|---|---|
| `Arquivo` | `Domain/Entity` | Representa um arquivo com identidade (nome + diretório + tipo) |
| `LeitorArquivoInterface` | `Domain/Service` | Contrato do domínio — define **o que** um leitor deve fazer |
| `ValidarArquivo` | `Domain/Service` | Lógica de validação que não pertence à entidade `Arquivo` sozinha |
| `EnumTipoArquivo` | `Domain/Enum` | Define os tipos de arquivo reconhecidos pelo domínio |
| `LerArquivo` | `Application/UseCase` | Orquestra `Arquivo` + `LeitorArquivoInterface` — **não é entidade** |
| `LeitorCsv` | `Infrastructure/Leitor` | Detalhe técnico de como ler e parsear um CSV |
| `LeitorTxt` | `Infrastructure/Leitor` | Detalhe técnico de como ler e parsear um TXT posicional |
| `LeitorArquivoFactory` | `Infrastructure/Leitor` | Decide qual leitor instanciar com base no tipo do arquivo |

### Por que a interface fica no domínio e as implementações na infraestrutura?

O **domínio define o contrato**: *"preciso de algo que saiba ler um arquivo e retornar um array"*. Isso é conhecimento de negócio.

Já `LeitorCsv` e `LeitorTxt` sabem **como** ler: usam `file()`, `str_getcsv()`, tratam `\r\n` do Windows, aplicam regex. Isso é detalhe técnico — infraestrutura.

Essa separação segue o **DIP (Dependency Inversion Principle)**, o D do SOLID:

```
Domínio                              Application              Infraestrutura
──────────────────────────           ─────────────────────    ──────────────────────────────────
LeitorArquivoInterface  ◄────────────LerArquivo (UseCase) ◄── LeitorCsv implements LeitorArquivoInterface
                                                          ◄── LeitorTxt implements LeitorArquivoInterface
```

O domínio aponta para dentro (abstrações). A camada `Application` depende apenas do domínio. A infraestrutura aponta para o domínio (implementações). Trocar `LeitorCsv` por qualquer outra implementação não toca no domínio nem no use case.

---

## O OCP aplicado: como adicionar um novo tipo de arquivo

A `LeitorArquivoFactory` resolve o leitor automaticamente por **convenção de nomenclatura**: ela monta o nome da classe como `Leitor` + tipo com a primeira letra maiúscula (`LeitorCsv`, `LeitorTxt`, `LeitorXlsx`, etc.) e verifica se a classe existe.

Para adicionar suporte a um novo formato (ex: XLSX), o processo é:

1. Adicionar `case XLSX` no `EnumTipoArquivo` *(já existe)*
2. Criar `LeitorXlsx` implementando `LeitorArquivoInterface` em `Infrastructure/Leitor/`

**Nenhuma outra classe é alterada** — nem mesmo a factory. `Arquivo`, `LerArquivo`, `ValidarArquivo`, `LeitorArquivoFactory`, `LeitorCsv` e `LeitorTxt` permanecem intocados — isso é o OCP na prática.

---

## Responsabilidade de cada classe

### Application / UseCase

#### `LerArquivo`
Recebe um `LeitorArquivoInterface` no construtor e expõe o método `execute(Arquivo $arquivo): array`. Orquestra a leitura sem saber como o arquivo é parseado nem conhecer a infraestrutura diretamente.

**Responsabilidade única:** executar a ação de leitura de um arquivo, conectando domínio e infraestrutura.

---

### Domain / Entity

#### `Arquivo`
Representa um arquivo no sistema de arquivos com nome, tipo e diretório. Valida no construtor se o nome tem extensão, se a extensão bate com o tipo informado e se o arquivo de fato existe no disco.

**Responsabilidade única:** representar e validar a identidade de um arquivo.

---

### Domain / Service

#### `LeitorArquivoInterface`
Define o contrato que todo leitor de arquivo deve cumprir: receber um caminho e retornar um array de dados.

**Responsabilidade única:** ser o contrato do domínio para leitura de arquivos.

#### `ValidarArquivo`
Centraliza as regras de validação de um `Arquivo`: nome com extensão, compatibilidade entre extensão e tipo, e existência no disco.

**Responsabilidade única:** validar um arquivo segundo as regras do domínio.

---

### Domain / Enum

#### `EnumTipoArquivo`
Define os tipos de arquivo reconhecidos pelo sistema: `CSV`, `XLSX`, `ODS`, `XLS`, `TXT`.

---

### Infrastructure / Leitor

#### `LeitorCsv`
Lê arquivos CSV com cabeçalho na primeira linha e retorna um array associativo, tratando terminadores CRLF do Windows. Suporta separador configurável (padrão `;`).

**Responsabilidade única:** parsear arquivos CSV em arrays associativos.

#### `LeitorTxt`
Lê arquivos TXT com dados posicionais (nome colado ao CPF, separado do e-mail por espaços), usando regex para extrair os três campos por linha.

**Responsabilidade única:** parsear arquivos TXT posicionais em arrays associativos.

#### `LeitorArquivoFactory`
Recebe um `Arquivo` e resolve automaticamente qual leitor instanciar por **convenção de nomenclatura**: monta o nome `Leitor` + tipo (ex: `LeitorCsv`, `LeitorTxt`) e verifica se a classe existe no namespace `Infrastructure\Leitor`. Lança exceção se a classe não for encontrada.

**Responsabilidade única:** instanciar o leitor correto para cada tipo de arquivo sem depender de configuração ou registro manual.

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

A saída do `var_dump` dos arquivos e leitores será impressa no próprio terminal.

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

A suíte cobre todas as camadas da aplicação: `Entity`, `Service`, `Enum` e `Infrastructure`.

### Estrutura dos testes

```
tests/
├── Application/
│   └── UseCase/
│       └── LerArquivoTest.php
├── Domain/
│   ├── Entity/
│   │   └── ArquivoTest.php
│   ├── Service/
│   │   └── ValidarArquivoTest.php
│   └── OcpPrincipleTest.php
└── Infrastructure/
    └── Leitor/
        ├── LeitorArquivoFactoryTest.php
        ├── LeitorCsvTest.php
        └── LeitorTxtTest.php
```

### O que cada suite testa

| Arquivo de Teste | O que cobre |
|---|---|
| `ArquivoTest` | Instanciação válida, getters, e as 3 exceções do construtor |
| `LerArquivoTest` | Leitura de CSV e TXT via use case, formato associativo dos dados |
| `ValidarArquivoTest` | Validação de nome, tipo, existência e arquivo removido |
| `LeitorCsvTest` | Parsing de CSV, cabeçalho, CRLF, separador customizado |
| `LeitorTxtTest` | Parsing de TXT posicional, nomes compostos, regex de CPF |
| `LeitorArquivoFactoryTest` | Instância correta por tipo, exceção para tipo sem leitor |
| `OcpPrincipleTest` | Demonstra que `LerArquivo` é fechado para modificação e aberto para extensão — novos leitores funcionam sem alterar nenhuma classe existente |

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
./vendor/bin/phpunit tests/Infrastructure/Leitor/LeitorCsvTest.php
```

---

## Lição principal do OCP aplicada neste projeto

Antes de aplicar o OCP, a lógica de leitura ficaria toda dentro do `Leitor`:

```php
// ❌ Antes — fechado para extensão, aberto para modificação
class Leitor {
    public function ler(): void {
        if ($this->arquivo->getTipo() === EnumTipoArquivo::CSV) {
            // lógica CSV...
        } elseif ($this->arquivo->getTipo() === EnumTipoArquivo::TXT) {
            // lógica TXT...
        } elseif ($this->arquivo->getTipo() === EnumTipoArquivo::XLSX) {
            // lógica XLSX... toda vez que surge novo tipo, mexe aqui
        }
    }
}
```

Após aplicar o OCP, cada formato tem seu próprio leitor:

```php
// ✅ Depois — aberto para extensão, fechado para modificação
class LeitorCsv implements LeitorArquivoInterface { public function ler(string $caminho): array { ... } }
class LeitorTxt implements LeitorArquivoInterface { public function ler(string $caminho): array { ... } }
class LeitorXlsx implements LeitorArquivoInterface { public function ler(string $caminho): array { ... } }
```

Se amanhã surgir o formato JSON, você cria `LeitorJson` em `Infrastructure/Leitor/` — `LerArquivo`, `LeitorCsv`, `LeitorTxt` e até a própria `LeitorArquivoFactory` **não são tocados**. Isso é o OCP na prática.
