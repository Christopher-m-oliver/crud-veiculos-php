# MotorHUB

Sistema web desenvolvido em PHP para gerenciamento de veículos e marcas.

O projeto possui autenticação de usuário, CRUD de veículos e CRUD de marcas, utilizando MySQL como banco de dados.

## Funcionalidades

- Login de usuário com autenticação pelo banco de dados
- Proteção das páginas utilizando sessões PHP
- Cadastro, edição, listagem e exclusão de veículos
- Cadastro, edição, listagem e exclusão de marcas
- Associação de veículos com marcas cadastradas
- Validação dos dados de veículos no servidor
- Upload de foto (opcional) dos veículos, armazenada no sistema de arquivos
- Proteção CSRF nos formulários de veículos e exclusão apenas via POST
- Mensagens de retorno após criar, editar e excluir veículos
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
│   ├── csrf.php
│   ├── flash.php
│   ├── header.php
│   ├── footer.php
│   ├── upload.php
│   └── validacao.php
├── marcas/
│   ├── index.php
│   ├── criar.php
│   ├── editar.php
│   └── excluir.php
├── uploads/
│   └── veiculos/
├── veiculos/
│   ├── index.php
│   ├── criar.php
│   ├── editar.php
│   └── excluir.php
├── database.sql
├── docker-compose.yml
├── index.php
├── login.php
├── logout.php
└── migracao_foto.sql
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

Se o banco já existia antes do upload de fotos, **não reimporte** o `database.sql` (ele falharia). Aplique somente a migração, uma única vez:

```bash
docker exec -i sistema_mysql mysql -u root -proot < migracao_foto.sql
```

### 4. Permitir a gravação na pasta de uploads

O Apache precisa gravar em `uploads/veiculos/`. Em ambiente de desenvolvimento:

```bash
chmod 777 uploads/veiculos
```

### 5. Acessar o sistema

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

A tabela `veiculos` possui uma chave estrangeira para a tabela `marcas` e a coluna `foto` (`VARCHAR(255)`, opcional), que guarda apenas o nome do arquivo da imagem.

## Upload de fotos dos veículos

Cada veículo pode ter uma foto, enviada nos formulários de cadastro e edição e exibida na listagem.

### Como funciona

- O formulário usa `enctype="multipart/form-data"`, que permite enviar arquivos.
- O PHP recebe o arquivo em `$_FILES['foto']`, valida e move para `uploads/veiculos/`.
- No banco fica **apenas o nome** do arquivo. O arquivo em si fica no sistema de arquivos.
- Formatos aceitos: JPG, PNG e WebP, de até 2 MB.
- Na edição é possível manter, trocar ou remover a foto. Ao excluir o veículo, o arquivo também é removido.

### Por que sistema de arquivos e não banco de dados

- O Apache entrega a imagem direto do disco, sem passar por PHP e MySQL.
- O banco guarda só um texto curto, o que mantém o banco e os backups leves.
- Exibir a imagem exige apenas um `<img src>`.
- Desvantagem: banco e disco podem ficar fora de sincronia. O sistema reduz o risco apagando o arquivo novo se o banco falhar e apagando o antigo somente depois de o banco confirmar a alteração.

### Segurança

- O tipo é verificado pelo **conteúdo** do arquivo (`finfo` e `getimagesize`), e não pelo nome nem pelo tipo informado pelo navegador.
- O nome salvo é gerado pelo servidor (`random_bytes`), evitando sobrescrita e nomes maliciosos.
- Só são apagados arquivos cujo nome segue o formato gerado pelo sistema.
- As validações dos dados do veículo (modelo, marca, potência, ano e tipo) são feitas no servidor, e os formulários de criar, editar e excluir usam token CSRF. A exclusão só aceita POST.

### Limitações conhecidas

- Envios acima de 8 MB (`post_max_size` do PHP) são rejeitados pelo PHP antes do código da aplicação. Com `display_errors` ligado, como no ambiente Docker de desenvolvimento, o aviso do PHP aparece na tela.
- O campo de foto não é preenchido novamente após um erro de validação (limitação dos navegadores): é preciso escolher o arquivo outra vez.
- O módulo de marcas ainda não utiliza as validações e proteções adicionadas ao módulo de veículos.

## Observações

- O projeto foi desenvolvido para fins acadêmicos.
- O ambiente de desenvolvimento utiliza Docker para executar PHP, MySQL e phpMyAdmin.
- As senhas dos usuários são armazenadas utilizando hash e verificadas com `password_verify()`.

## Autor

Christopher Martins de Oliveira