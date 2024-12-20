

## Descrição do Projeto

Este é um sistema de Relógio Ponto desenvolvido em Laravel 10, com PHP 8.1, utilizando tecnologias modernas como Vite,Tailwind CSS e Docker. O sistema permite gerenciar registros de ponto de forma eficiente e oferece uma interface moderna e responsiva.


- Organização por departamentos e seus respectivos supervisores. 
- Login via CPF para facilitar importação de funcionários. 
- Modo escuro adaptativo. 
- Dashboards para cada nível de acesso. 

## Requisitos do Sistema

- **PHP:** >= 8.1
- **Node.js:** >= 18.x
- **Composer:** >= 2.5
- **Docker:** >= 20.x (recomendado para desenvolvimento e produção)
- **NPM:** >= 8.x


## Setup do Ambiente

1. Clone este repositório:
   ```bash
   git clone https://github.com/zzeis/relogioponto.git
   cd relogio-ponto
```

2.  Instale as dependências do PHP e do front-end:
``` 
	 composer install 
     npm install 
```

3.  Configure o .env 
```
	cp .env.example .env 
```
4. Execute o ambiente Docker:
    ``` 
	docker-compose up -d
	```
5.  Gere a chave da aplicação Laravel(Dentro do container App): 
```
	docker exec -it app(nome do seu container app) php artisan key:generate
```

6. Rode as migrações para criar o banco de dados:
```
	docker exec -it app(nome do seu containre app) php artisan migrate
```
7. Acesse o sistema no navegador: 
 - URL: [http://localhost:8989](http://localhost:8989)
 
## Framework
- [[Laravel]] 10 

## Bibliotecas Utilizadas

No arquivo `resources/js/app.js`, utilizamos as seguintes bibliotecas para melhorar a funcionalidade e a experiência do usuário no sistema:

### 1. **Alpine.js**

- **Propósito:** Utilizado para criar interações dinâmicas e reativas no front-end de forma simples e sem dependência de frameworks maiores, como Vue.js ou React.
- **Uso no projeto:** Controle de componentes interativos, como dropdowns, modais ou atualizações dinâmicas de elementos da página.

### 2. **Axios**

- **Propósito:** Uma biblioteca para fazer requisições HTTP de forma simples e eficiente.
- **Uso no projeto:** Comunicação com o back-end para envio de dados ou recuperação de informações via APIs.

### 3. **SweetAlert**

- **Propósito:** Biblioteca para exibir mensagens e alertas interativos com design moderno.
- **Uso no projeto:** Exibição de alertas, mensagens de sucesso/erro e confirmações, como confirmação para exclusões ou notificações importantes.

### 4. **Tailwind CSS**

- **Propósito:** Framework CSS utilitário para estilização de componentes com classes simples e reutilizáveis.
- **Uso no projeto:** Design responsivo e estilização do sistema, incluindo botões, layouts, tabelas e outros elementos visuais.


### Como incluir dependências no projeto

Para instalar e gerenciar as dependências front-end, usamos o [npm](https://www.npmjs.com/). Certifique-se de rodar o comando abaixo dentro do containers app para instalar todas as dependências listadas no arquivo `package.json`:

**npm install**


---



## Execução das Filas

Para processar tarefas em background, este projeto utiliza o sistema de filas do Laravel com Redis como driver.

- O container `app` executa os workers automaticamente com o seguinte comando:
  ```bash
  php artisan queue:work --queue=pontos,redis --sleep=5 --timeout=0 --max-jobs=1000

```

Caso precise reiniciar o worker:
```
docker exec -it app(nome do seu container app) php artisan queue:restart
```


## Estrutura do Projeto

- **`resources/js/`**: Contém os arquivos JavaScript do projeto. 
- **`resources/css/`**: Contém os arquivos CSS (gerenciados pelo Tailwind CSS). 
- **`routes/`**: Define as rotas do sistema, incluindo APIs e rotas web. 
- **`app/`**: Contém o código principal do Laravel, incluindo models, controllers, jobs e eventos. -
- **`database/`**: Scripts de migração e seeders para o banco de dados. 
- **`dockerfile`**: Configurações específicas para o ambiente Docker.
- **`docker-compose.yml`**: Configurações específicas para o ambiente Docker, incluindo Nginx,Redis,MySQL, APP, Queue-worker.
