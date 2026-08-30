# MotorHUB

Sistema web desenvolvido em PHP para gerenciamento de veículos e marcas.

O projeto possui autenticação de usuário, CRUD de veículos e CRUD de marcas, utilizando MySQL como banco de dados.

## Funcionalidades

- Login de usuário com autenticação pelo banco de dados
- Proteção das páginas utilizando sessões PHP
- Cadastro, edição, listagem e exclusão de veículos
- Cadastro, edição, listagem e exclusão de marcas
- Associação de veículos com marcas cadastradas
- Validação básica dos formulários
- Interface responsiva em HTML e CSS

## Tecnologias utilizadas

- PHP 8.3
- MySQL 8.4
- HTML5
- CSS3
- PDO
- Docker
- Docker Compose
- phpMyAdmin

## Estrutura do projeto

```text
.
├── config/
│   └── database.php
├── css/
│   └── style.css
├── includes/
│   ├── auth.php
│   ├── header.php
│   └── footer.php
├── marcas/
│   ├── index.php
│   ├── criar.php
│   ├── editar.php
│   └── excluir.php
├── veiculos/
│   ├── index.php
│   ├── criar.php
│   ├── editar.php
│   └── excluir.php
├── database.sql
├── docker-compose.yml
├── index.php
├── login.php
└── logout.php
```

## Como executar

É necessário ter Docker e Docker Compose instalados.

### 1. Clonar o repositório

```bash
git clone https://github.com/Christopher-m-oliver/crud-veiculos-php.git
cd crud-veiculos-php
```

### 2. Iniciar os containers

```bash
docker compose up -d
```

### 3. Importar o banco de dados

```bash
docker exec -i sistema_mysql mysql -u root -proot < database.sql
```

O aviso sobre uso de senha na linha de comando pode ser ignorado neste ambiente de desenvolvimento.

### 4. Acessar o sistema

Aplicação:

```text
http://localhost:8080
```

phpMyAdmin:

```text
http://localhost:8081
```

## Acesso padrão

```text
Usuário: admin
Senha: 123456
```

O usuário padrão já é criado pelo arquivo `database.sql`.

## Banco de dados

O sistema utiliza o banco:

```text
sistema_veiculos
```

Tabelas principais:

- `usuarios`
- `marcas`
- `veiculos`

A tabela `veiculos` possui uma chave estrangeira para a tabela `marcas`.

## Observações

- O projeto foi desenvolvido para fins acadêmicos.
- O ambiente de desenvolvimento utiliza Docker para executar PHP, MySQL e phpMyAdmin.
- As senhas dos usuários são armazenadas utilizando hash e verificadas com `password_verify()`.

## Autor

Christopher Martins de Oliveira
