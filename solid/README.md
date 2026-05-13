# SOLID — Princípios de Design Orientado a Objetos

> Repositório de estudo prático dos **5 princípios SOLID**, com implementações em **PHP** e estrutura de pastas baseada em **DDD (Domain-Driven Design)**.

---

## O que é SOLID?

**SOLID** é um acrônimo criado por Robert C. Martin (Uncle Bob) para reunir cinco princípios fundamentais do design de software orientado a objetos. Aplicados em conjunto, esses princípios produzem sistemas mais legíveis, flexíveis, testáveis e fáceis de manter.

| Letra | Princípio | Definição resumida |
|---|---|---|
| **S** | Single Responsibility Principle | Uma classe deve ter um único motivo para mudar |
| **O** | Open/Closed Principle | Aberta para extensão, fechada para modificação |
| **L** | Liskov Substitution Principle | Subtipos devem poder substituir seus tipos base |
| **I** | Interface Segregation Principle | Prefira interfaces pequenas e focadas a uma interface genérica |
| **D** | Dependency Inversion Principle | Dependa de abstrações, não de implementações concretas |

---

## Por que SOLID importa?

Código sem princípios de design tende a se tornar um **Big Ball of Mud**: classes que fazem tudo, dependências escondidas, mudanças que quebram partes não relacionadas, e testes impossíveis de escrever. SOLID é uma resposta direta a esses problemas.

Cada princípio ataca uma categoria específica de acoplamento ruim:

- **SRP** evita classes que crescem sem limite porque "é mais fácil colocar aqui"
- **OCP** evita a necessidade de abrir código que já funciona para adicionar features
- **LSP** garante que herança não quebre contratos
- **ISP** evita que uma mudança em um contrato force alterações em quem não usa aquele trecho
- **DIP** inverte o controle para que módulos de alto nível não dependam de detalhes

---

## Proposta dos projetos

Cada subpasta deste repositório é um projeto independente que demonstra um dos princípios do SOLID em **PHP**. Os projetos compartilham uma abordagem comum:

- **Problema concreto**: cada projeto parte de um cenário de negócio real (e-commerce, notificações, leitura de arquivos, etc.)
- **Violação explícita**: o problema é apresentado mostrando *como* o princípio seria violado
- **Solução aplicada**: o código refatorado demonstra o princípio em ação
- **Testes com PHPUnit**: cada comportamento relevante é coberto por testes automatizados
- **Estrutura DDD**: o código é organizado em camadas (`Domain`, `Application`, `Infrastructure`) para simular um projeto próximo do mundo real

---

## Projetos

### S — [SRP - Single Responsibility Principle](SRP%20-%20Single%20Responsibility%20Principle/)

**Domínio:** sistema de pedidos de e-commerce.

Uma classe `Pedido` que gerencia dados do pedido **e** envia e-mail viola o SRP. O projeto demonstra como separar responsabilidades em classes coesas — `Pedido` cuida do pedido, `EmailService` cuida do e-mail — de modo que uma mudança no sistema de notificação nunca afete a lógica de negócio.

---

### O — [OCP - Open/Closed Principle](OCP%20-%20Open%20Closed%20Principle/)

**Domínio:** sistema de leitura de arquivos de dados.

Adicionar suporte a um novo formato de arquivo (TXT, XLSX) não deve exigir a abertura e modificação da classe `Leitor`. O projeto demonstra como usar interfaces e polimorfismo para estender comportamentos sem tocar em código existente.

---

### L — [LSP - Liskov Substitution Principle](LSP%20-%20Liskov%20Substitution%20Principle/)

**Domínio:** sistema de cálculo de área de polígonos.

O exemplo clássico do quadrado e retângulo: na geometria, um quadrado *é* um retângulo — mas herdar de `Retangulo` e sobrescrever setters para forçar lados iguais quebra o contrato da classe base. O projeto demonstra como modelar hierarquias de herança que respeitam o LSP.

---

### I — [ISP - Interface Segregation Principle](ISP%20-%20Interface%20Segregation%20Principle/)

**Domínio:** sistema de cadastro e notificação de usuários.

Uma `EntidadeInterface` com 10 métodos força toda entidade do sistema a implementar operações que nunca vai usar. O projeto demonstra como segregar contratos por intenção — `UsuarioReadInterface`, `UsuarioWriteInterface`, `NotificacaoRecipientInterface` — para que cada cliente dependa apenas do que realmente precisa.

---

### D — [DIP - Dependency Inversion Principle](DIP%20-%20Dependency%20Inversion%20Principle/)

**Domínio:** sistema de envio de notificações.

Módulos de alto nível não devem depender de módulos de baixo nível — ambos devem depender de abstrações. O projeto demonstra como inverter dependências via interfaces, desacoplando a lógica de negócio de implementações concretas de infraestrutura (banco, e-mail, log, etc.).

---

## Tecnologias

- **PHP 8.x**
- **PHPUnit** — testes automatizados
- **Composer** — gerenciamento de dependências
- **DDD** — organização das camadas de código

---

## Como executar os testes

Cada projeto possui seu próprio `composer.json`. Para rodar os testes de um princípio específico:

```bash
cd "SRP - Single Responsibility Principle"
composer install
./vendor/bin/phpunit
```

Substitua o nome da pasta pelo princípio desejado (`OCP`, `LSP`, `ISP`, `DIP`).
