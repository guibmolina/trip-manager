##
## Trip Manager - Gerenciador de Viagens
## Como rodar:

Faça o clone do repositório na sua máquina

```bash
git clone git@github.com:guibmolina/TripManager.git
```

### Configuração do ambiente
***

**Para configuração do ambiente é necessário ter o [Docker](https://docs.docker.com/desktop/) instalado em sua máquina.**

Dentro da pasta do projeto, rode o seguinte comando: `docker compose up -d --build`.

Copie o arquivo `.env.example` e renomeie para `.env` dentro da pasta raíz da aplicação.

```bash
cp .env.example .env
```
É no  `.env` que estão as configurações de ambiente para o envio de email, como teste, utilizei o serviço https://mailtrap.io/ que é bem fácil de configurar, só realizar o login no site e mudar as credenciais do`.env`. Como o exemplo a seguir:
```bash
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=75e25d9765f1a7
MAIL_PASSWORD=7fe0ef832e862a
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="email@example.com"
```

Após criar o arquivo `.env`, será necessário acessar o container da aplicação para rodar alguns comandos de configuração do Laravel.

Digite o seguinte comando:

```bash
docker exec -it trip-manager-api bash -c "composer install && php artisan key:generate && php artisan migrate && php artisan db:seed && php artisan jwt:secret && php artisan optimize"
```

###  Testes

`$ docker exec -it trip-manager-api php artisan test `

