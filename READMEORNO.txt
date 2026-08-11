10. Anexo A — README (Como Rodar a Aplicação)
 MyReceitas

Sistema web desenvolvido em PHP para compartilhamento, gerenciamento e visualização de receitas culinárias. A plataforma permite diferentes tipos de usuários (Usuários, Chefs e Restaurantes), oferecendo funcionalidades específicas para cada perfil, além de um sistema de favoritos e gerenciamento de conteúdo.

---

 Índice

- Sobre o Projeto
- Funcionalidades
- Tecnologias Utilizadas
- Pré-requisitos
- Instalação
- Configuração do Banco de Dados
- Configuração da Aplicação
- Estrutura do Projeto
- Desenvolvedores
- Licença

---

 Sobre o Projeto

O MyReceitas é uma plataforma desenvolvida com foco no compartilhamento de receitas culinárias entre usuários, chefs e restaurantes.

O sistema foi projetado utilizando PHP, MySQL e JavaScript, seguindo uma arquitetura organizada baseada em DAO (Data Access Object), proporcionando melhor separação entre regras de negócio e acesso aos dados.

Cada tipo de usuário possui funcionalidades específicas, tornando a plataforma flexível e escalável para futuras implementações.

---

 Funcionalidades

 Sistema de Usuários

- Cadastro e autenticação de usuários.
- Diferentes perfis de acesso:
  - Usuário comum
  - Chef
  - Restaurante
- Login seguro utilizando PHP e PDO.


 Receitas

- Cadastro de receitas.
- Visualização detalhada.
- Pesquisa de receitas.
- Exibição de ingredientes.
- Modo de preparo.
- Informações nutricionais.
- Imagens das receitas.

 Sistema de Favoritos

- Curtir e remover curtidas em receitas.
- Caderno pessoal de receitas favoritas.
- Atualização dinâmica diretamente no banco de dados.

 Chefs

- Perfil personalizado.
- Cadastro de especialidades.
- Gerenciamento das próprias receitas.

 Restaurantes

- Perfil completo do restaurante.
- Horário de funcionamento.
- Serviços oferecidos.
- Associação de chefs cadastrados na plataforma.
- Publicação das receitas do estabelecimento.

---

 Tecnologias Utilizadas

 Backend

- PHP 8.x
- PDO

 Banco de Dados

- MySQL

 Frontend

- HTML5
- CSS3
- JavaScript (Vanilla)

 Bibliotecas

- Font Awesome 6

---

 Pré-requisitos

Antes de executar o projeto, é necessário possuir instalado:

- PHP 8 ou superior
- MySQL
- Git
- Um servidor local, como:
  - XAMPP
  - Laragon
  - WampServer
- Visual Studio Code (recomendado)

---

 Instalação

 1. Clonar o repositório

Acesse a pasta pública do seu servidor local.

Exemplo no XAMPP:

```bash
cd C:/xampp/htdocs
```

Clone o projeto:

```bash
git clone https://github.com/matheusfermiano-lgtm/myreceitas
```

---

 Configuração do Banco de Dados

1. Inicie o MySQL.

2. Acesse:

```
http://localhost/phpmyadmin
```

3. Crie um banco de dados.

Exemplo:

```
chef_receitas
```

4. Importe o arquivo `.sql` do projeto.

Caso exista, utilize o arquivo localizado na raiz do projeto.

 Importante

Verifique se a tabela responsável pelas curtidas contém os campos:

- recipe_id
- user_id
- user_type

---

 Configuração da Aplicação

Abra o arquivo:

```
config/database.php
```

Configure as credenciais de acordo com seu ambiente.

```php
$host = "localhost";
$dbname = "chef_receitas";
$username = "root";
$password = "";
```

---

 Executando o Projeto

Após concluir as configurações, acesse:

```
http://localhost:8000 / http://localhost:8080
```

ou

```
http://localhost:8000/index.php / http://localhost:8080/index.php
```

---

 Estrutura do Projeto

```
├── config/
│   └── database.php
│
├── models/
│   └── dao/
│       └── recipeDAO.php
│
├── views/
│   ├── receitas_curtidas.php
│   ├── recipe_view.php
│   └── recipes/
│       └── recipe_like_action.php
│
├── base.php
├── index.php
└── README.md
```
