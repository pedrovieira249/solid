# LSP — Liskov Substitution Principle

> Projeto de estudo sobre o **L** do **SOLID**, com estrutura de pastas baseada em **DDD (Domain-Driven Design)**.

---

## O que é o L do SOLID?

SOLID é um conjunto de 5 princípios de design de software orientado a objetos. O **L** representa o **Liskov Substitution Principle (Princípio da Substituição de Liskov)**.

### Definição

> "Se S é um subtipo de T, então objetos do tipo T podem ser substituídos por objetos do tipo S sem alterar as propriedades corretas do programa."
>
> — Barbara Liskov, 1987

Em termos práticos: **qualquer subtipo deve poder ser usado no lugar da classe base sem que o comportamento esperado seja quebrado**.

### Como identificar uma violação?

Se ao substituir uma classe base por uma de suas subclasses o programa passa a se comportar de forma diferente ou inesperada, o LSP está sendo violado.

| Situação | LSP |
|---|---|
| `Quadrado` e `Retangulo` estendem a mesma classe base e funcionam de forma intercambiável | ✅ Substituição segura |
| `Quadrado` estende `Retangulo` e precisa sobrescrever `setLargura` para manter consistência | ❌ Subtipo altera comportamento da base |
| Código que recebe `PoligonoQuadrilateros` funciona igual para `Retangulo`, `Quadrado` e `Paralelogramo` | ✅ Substituição segura |
| Método da subclasse lança exceção que a base não lança | ❌ Contrato quebrado |

### O problema clássico: Quadrado e Retângulo

O exemplo mais famoso de violação do LSP é a relação entre `Quadrado` e `Retangulo`.

Na geometria, um quadrado **é um** retângulo (com lados iguais). Isso sugere herança:

```php
// ❌ Parece intuitivo, mas viola o LSP
class Quadrado extends Retangulo
{
    public function setLargura(float $largura): void
    {
        parent::setLargura($largura);
        parent::setAltura($largura); // força lados iguais
    }

    public function setAltura(float $altura): void
    {
        parent::setAltura($altura);
        parent::setLargura($altura); // força lados iguais
    }
}
```

O problema aparece ao usar a classe base:

```php
function aumentarLargura(Retangulo $r): void
{
    $alturaOriginal = $r->getAltura();
    $r->setLargura(10.0);

    // Para um Retangulo, isso é sempre verdade
    // Para um Quadrado, isso FALHA — setLargura também mudou a altura
    assert($r->getAltura() === $alturaOriginal); // ❌ quebra com Quadrado
}
```

O `Quadrado` não pode substituir o `Retangulo` sem quebrar o comportamento esperado. A hierarquia estava errada.

### A solução: uma abstração comum

Em vez de fazer `Quadrado` herdar de `Retangulo`, ambos herdam de uma classe base abstrata que representa o contrato comum entre polígonos quadriláteros:

```php
// ✅ Correto — hierarquia baseada em comportamento, não em geometria intuitiva
abstract class PoligonoQuadrilateros
{
    abstract public function calcularArea(): float;
}

class Retangulo extends PoligonoQuadrilateros { ... }
class Quadrado extends PoligonoQuadrilateros { ... }
```

Agora qualquer código que dependa de `PoligonoQuadrilateros` funciona igualmente com `Retangulo`, `Quadrado` ou `Paralelogramo` — isso é o LSP aplicado.

### Por que isso importa?

Sem o LSP, o polimorfismo vira uma armadilha. Você cria uma hierarquia, confia nela, e descobre que substituir a classe base por uma subclasse quebra o sistema em casos específicos. O resultado:

- Código cheio de `instanceof` para tratar subtipos de forma diferente
- Testes que passam para a base e falham para os filhos
- Comportamento imprevisível quando a dependência é injetada em runtime

---

## O que é DDD?

**Domain-Driven Design** é uma abordagem de arquitetura que organiza o código em torno do **domínio do negócio**, não em torno de detalhes técnicos.

O domínio é a área de conhecimento do problema que você está resolvendo. Neste projeto, o domínio é um **sistema de cálculo de áreas de polígonos quadriláteros**.

### Conceitos fundamentais usados neste projeto

#### Value Object (Objeto de Valor)
Objeto que representa um conceito do domínio e é definido pelos seus **atributos**, não por uma identidade única. Dois retângulos com as mesmas dimensões são equivalentes — não há um "retângulo #1" distinto do "retângulo #2".

```
Exemplo: Retangulo(5.0, 3.0) e outro Retangulo(5.0, 3.0) são o mesmo valor.
         Não faz sentido ter um ID para distingui-los.
```

#### Service (Serviço de Domínio)
Lógica de negócio que **não pertence naturalmente a um único Value Object**. O cálculo da área poderia estar dentro do próprio polígono, mas ao delegá-lo a um serviço a lógica de cálculo fica isolada e testável de forma independente.

```
Exemplo: CalcularAreaRetangulo encapsula a fórmula largura × altura.
         Se a fórmula mudar (ex: área com fator de conversão), só o serviço é alterado.
```

#### Por que não há Entity, Enum, Application ou Infrastructure aqui?

Este projeto é **puramente de domínio**, sem camadas externas. Os polígonos não têm identidade persistível (não são entidades), não há enumerações de tipos e não há interação com infraestrutura (banco de dados, arquivos, APIs). A ausência dessas pastas é intencional — não é um esquecimento.

---

## Estrutura de Pastas

```
src/
└── Domain/                          ← Tudo relacionado ao negócio
    ├── Entity/                      ← (vazio — polígonos não têm identidade)
    │
    ├── Enum/                        ← (vazio — sem tipos enumerados neste domínio)
    │
    ├── Service/                     ← Lógica de cálculo de área
    │   ├── CalcularAreaRetangulo.php
    │   ├── CalcularAreaQuadrado.php
    │   └── CalcularAreaParalelogramo.php
    │
    └── ValueObject/                 ← Polígonos definidos por suas dimensões
        ├── PoligonoQuadrilateros.php  ← Classe base abstrata
        ├── Retangulo.php
        ├── Quadrado.php
        └── Paralelogramo.php
```

### Por que cada arquivo foi para onde foi?

| Arquivo | Camada | Motivo |
|---|---|---|
| `PoligonoQuadrilateros` | `Domain/ValueObject` | Define o contrato comum de todos os polígonos: dimensões e cálculo de área |
| `Retangulo` | `Domain/ValueObject` | Polígono definido por largura e altura — sem identidade, puro valor |
| `Quadrado` | `Domain/ValueObject` | Polígono definido por um único lado — sem identidade, puro valor |
| `Paralelogramo` | `Domain/ValueObject` | Polígono definido por base e altura — sem identidade, puro valor |
| `CalcularAreaRetangulo` | `Domain/Service` | Encapsula a fórmula `largura × altura`, separada do Value Object |
| `CalcularAreaQuadrado` | `Domain/Service` | Encapsula a fórmula `lado × lado`, separada do Value Object |
| `CalcularAreaParalelogramo` | `Domain/Service` | Encapsula a fórmula `base × altura`, separada do Value Object |

### Por que `Entity/` e `Enum/` estão vazios?

**Entity** exige identidade: dois objetos com os mesmos atributos são instâncias distintas apenas se tiverem IDs diferentes. Um retângulo 5×3 não precisa de ID — qualquer retângulo 5×3 é o mesmo valor. Logo, não há entidades neste domínio.

**Enum** seria usado para enumerar tipos conhecidos em tempo de compilação. Aqui não há necessidade de categorizar tipos de polígonos via enum — os próprios subtipos definem os tipos.

As pastas existem para manter a consistência com a estrutura DDD padrão do projeto, sinalizando que foram consideradas e descartadas intencionalmente.

---

## Responsabilidade de cada classe

### Domain / ValueObject

#### `PoligonoQuadrilateros`
Classe base **abstrata** que define o contrato de todos os polígonos deste sistema. Armazena `largura` e `altura` como propriedades privadas e expõe `getLargura()` e `getAltura()`. Declara `calcularArea()` como método abstrato — toda subclasse é **obrigada** a implementar.

**Responsabilidade única:** definir o contrato mínimo de um polígono quadrilátero: ter dimensões e calcular área.

---

#### `Retangulo`
Estende `PoligonoQuadrilateros` recebendo `largura` e `altura` no construtor (via `parent::__construct`). Implementa `calcularArea()` delegando ao serviço `CalcularAreaRetangulo`.

**Responsabilidade única:** representar um retângulo com largura e altura independentes.

---

#### `Quadrado`
Estende `PoligonoQuadrilateros` recebendo apenas `float $lado` no construtor e passando `$lado, $lado` para o pai — garantindo que largura e altura sejam sempre iguais **por construção**, sem precisar sobrescrever setters. Implementa `calcularArea()` delegando ao serviço `CalcularAreaQuadrado`.

**Responsabilidade única:** representar um quadrado com lados iguais, sem expor a possibilidade de dimensões assimétricas.

---

#### `Paralelogramo`
Estende `PoligonoQuadrilateros` recebendo `base` e `altura` no construtor. Implementa `calcularArea()` delegando ao serviço `CalcularAreaParalelogramo`.

**Responsabilidade única:** representar um paralelogramo com base e altura independentes.

---

### Domain / Service

#### `CalcularAreaRetangulo`
Recebe um `Retangulo` e retorna `getLargura() * getAltura()`.

**Responsabilidade única:** calcular a área de um retângulo.

---

#### `CalcularAreaQuadrado`
Recebe um `Quadrado` e retorna `getLargura() * getLargura()` (equivalente a `lado²`).

**Responsabilidade única:** calcular a área de um quadrado.

---

#### `CalcularAreaParalelogramo`
Recebe um `Paralelogramo` e retorna `getLargura() * getAltura()` (base × altura).

**Responsabilidade única:** calcular a área de um paralelogramo.

---

## O LSP em ação: substituição transparente

O `LiskovSubstitutionTest` demonstra o princípio de forma direta: uma função que recebe `PoligonoQuadrilateros` funciona corretamente para **qualquer** subtipo, sem `instanceof`, sem condicionais, sem surpresas.

```php
// Código que depende apenas da abstração
function exibirArea(PoligonoQuadrilateros $poligono): void
{
    echo $poligono->calcularArea(); // funciona para Retangulo, Quadrado e Paralelogramo
}

exibirArea(new Retangulo(5.0, 3.0));    // 15.0
exibirArea(new Quadrado(4.0));          // 16.0
exibirArea(new Paralelogramo(6.0, 4.0)); // 24.0
```

Nenhum ajuste foi necessário ao adicionar `Paralelogramo`. Isso é o LSP: o sistema aceita novos subtipos sem que o código consumidor seja alterado.

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

A saída do `var_dump` dos polígonos e suas áreas será impressa no próprio terminal.

---

## Testes

> Os testes unitários deste projeto foram criados com auxílio de IA (GitHub Copilot).

A suíte cobre os Value Objects, os Services de domínio e o contrato LSP em si.

### Estrutura dos testes

```
tests/
└── Domain/
    ├── Service/
    │   ├── CalcularAreaRetanguloTest.php
    │   ├── CalcularAreaQuadradoTest.php
    │   └── CalcularAreaParalelogramoTest.php
    └── ValueObject/
        ├── RetanguloTest.php
        ├── QuadradoTest.php
        ├── ParalelogramoTest.php
        └── LiskovSubstitutionTest.php
```

### O que cada suíte testa

| Arquivo de Teste | O que cobre |
|---|---|
| `RetanguloTest` | Getters `getLargura` e `getAltura`, resultado de `calcularArea` |
| `QuadradoTest` | Construtor com lado único, `largura === altura`, resultado de `calcularArea` |
| `ParalelogramoTest` | Getters `getLargura` e `getAltura`, resultado de `calcularArea` |
| `CalcularAreaRetanguloTest` | Cálculo `largura × altura` via serviço isolado |
| `CalcularAreaQuadradoTest` | Cálculo `lado²` via serviço isolado |
| `CalcularAreaParalelogramoTest` | Cálculo `base × altura` via serviço isolado |
| `LiskovSubstitutionTest` | Demonstra que todos os subtipos de `PoligonoQuadrilateros` cumprem o mesmo contrato: implementam `calcularArea`, expõem `getLargura` e `getAltura`, e retornam área positiva — sem nenhum `instanceof` |

### Como o `LiskovSubstitutionTest` funciona

O teste usa `#[DataProvider]` para rodar o mesmo conjunto de verificações contra todos os subtipos de uma vez:

```php
#[DataProvider('provedorPoligonos')]
public function test_todo_subtipo_implementa_calcularArea(PoligonoQuadrilateros $poligono, float $areaEsperada): void
{
    $this->assertSame($areaEsperada, $poligono->calcularArea());
}

public static function provedorPoligonos(): array
{
    return [
        'Retangulo 5x3'      => [new Retangulo(5.0, 3.0), 15.0],
        'Quadrado lado 4'    => [new Quadrado(4.0), 16.0],
        'Paralelogramo 6x4'  => [new Paralelogramo(6.0, 4.0), 24.0],
    ];
}
```

4 verificações × 3 subtipos = **12 combinações** testadas automaticamente. Se um novo polígono for adicionado ao provider, os 4 contratos são verificados sem nenhuma linha de teste nova.

> **Nota:** O PHPUnit 12 exige o atributo `#[DataProvider]` (PHP 8 attribute). A anotação `@dataProvider` em docblock gera aviso de depreciação e será removida em versões futuras.

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
./vendor/bin/phpunit tests/Domain/ValueObject/LiskovSubstitutionTest.php
```

---

## Lição principal do LSP aplicada neste projeto

### O problema: herança intuitiva que viola o contrato

A tentação natural é fazer `Quadrado extends Retangulo` porque, na geometria, um quadrado é um caso especial de retângulo. Mas na orientação a objetos isso viola o LSP:

```php
// ❌ Antes — Quadrado extends Retangulo
class Retangulo
{
    public function __construct(
        private float $largura,
        private float $altura
    ) {}

    public function setLargura(float $largura): void { $this->largura = $largura; }
    public function setAltura(float $altura): void   { $this->altura  = $altura; }
}

class Quadrado extends Retangulo
{
    public function __construct(float $lado)
    {
        parent::__construct($lado, $lado);
    }

    // Precisa sobrescrever para manter lados iguais — isso altera o comportamento da base
    public function setLargura(float $largura): void
    {
        parent::setLargura($largura);
        parent::setAltura($largura); // efeito colateral inesperado
    }
}

// Código que depende de Retangulo — quebra silenciosamente com Quadrado
$r = new Quadrado(5.0);
$r->setLargura(10.0);
echo $r->getAltura(); // esperado: 5.0 — obtido: 10.0 ❌
```

### A solução: abstração baseada em comportamento

Mover o contrato para uma classe base abstrata que representa o que `Retangulo`, `Quadrado` e `Paralelogramo` têm **em comum de verdade** — dimensões imutáveis e cálculo de área:

```php
// ✅ Depois — hierarquia correta
abstract class PoligonoQuadrilateros
{
    public function __construct(
        private float $largura,
        private float $altura
    ) {}

    public function getLargura(): float { return $this->largura; }
    public function getAltura(): float  { return $this->altura; }

    abstract public function calcularArea(): float;
}

class Retangulo extends PoligonoQuadrilateros
{
    public function calcularArea(): float
    {
        return (new CalcularAreaRetangulo())->calcularArea($this);
    }
}

class Quadrado extends PoligonoQuadrilateros
{
    public function __construct(float $lado)
    {
        parent::__construct($lado, $lado); // lados iguais garantidos por construção
    }

    public function calcularArea(): float
    {
        return (new CalcularAreaQuadrado())->calcularArea($this);
    }
}
```

O `Quadrado` não precisa mais sobrescrever nenhum setter. Suas dimensões são imutáveis após a construção — não há comportamento da base a ser violado. Qualquer código que dependa de `PoligonoQuadrilateros` aceita `Retangulo`, `Quadrado` e `Paralelogramo` de forma intercambiável e previsível.
