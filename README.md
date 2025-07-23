# Sistema de Vendas com Laravel + Livewire

Este é um sistema de vendas simples, direto e funcional, que estou desenvolvendo como estudo e prática com **Laravel 10**, **Livewire 3** e **TailwindCSS**. A ideia é criar um projeto completo que vai além do CRUD, com foco também em dashboard, relatórios, testes e visual moderno.

## Tecnologias Utilizadas

- Laravel 10
- Livewire 3
- TailwindCSS
- Alpine.js
- MySQL
- Pest (para testes automatizados)
- Eloquent ORM
- Chart.js (gráficos no dashboard)

## Funcionalidades

### Produtos
- Cadastro, edição, listagem e exclusão de produtos
- Controle de estoque (quantidade e unidade)
- Preço e descrição no formulário

### Clientes
- Cadastro e gerenciamento de clientes
- Vendas associadas ao cliente

### Vendas
- Registro de vendas com múltiplos produtos
- Cálculo automático de totais
- Associação com clientes
- Visualização em tempo real usando Livewire

### Dashboard
- Visão geral com métricas importantes
- Cards com:
  - Produto mais vendido
  - Produto com maior receita
  - Cliente que mais comprou
- Gráfico de barras:
  - Quantidade de vendas por mês
  - Agrupamento por faixa (1-5, 6-10, etc.)
  - Seletor de ano
  - Scroll horizontal para visualizar todos os meses

## Estrutura

O projeto segue a organização padrão do Laravel, com os componentes do Livewire separados por entidade:


## Como rodar o projeto

### Pré-requisitos
- PHP 8.3+
- Composer
- MySQL
- Node.js e NPM

### Instalação

```bash
git clone https://github.com/arthurxlzb/vendas-livewire.git
cd vendas-livewire

composer install
cp .env.example .env
php artisan key:generate

# configure o .env com seu banco de dados local

php artisan migrate
npm install && npm run dev
php artisan serve
