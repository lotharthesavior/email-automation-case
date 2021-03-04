# Email Send API Test

[![Tests](https://github.com/lotharthesavior/email-automation-case/actions/workflows/php.yml/badge.svg)](https://github.com/lotharthesavior/email-automation-case/actions/workflows/php.yml)

## Requirement Details:

- Emails must be asynchronous.
- Must persist the dispatched email.
- Must persist the dispatched email in the database.
- Must be protected by token - **for this let's use [JWT](https://github.com/tymondesigns/jwt-auth)**.
- Must be run queues using Horizon/Redis style.
- Use any mail provider of choice - **let's use [Mailtrap](https://mailtrap.io/)**.



## Prepare environment:

Before everything, update the `.env` file with the proper information.

### Docker

At the docker-compose, I set this project to use a static IP, that requires an extra step to set, at the `.env` file at the root of the directory: update the parameters `DOCKER_APP` (give your instance a name) and DOCKER_APP_IP (add a prefix to your app's ip, e.g. 10.2).

After that, to start the environment, just run:

```shell
$ docker-compose up -d
```



### Sample `.env` for testing:

This example works with the local environment prepared with `docker-compose.yml`:

```
APP_NAME=SomeAppName
APP_ENV=local
APP_KEY=base64:WXnfdGiPd6EaALnkGUwfl57vyOWtDmuTxaJiA5MElSA=
APP_DEBUG=true
# Don't forget to set this domain locally or change this to "localhost"
APP_URL=http://localhost

LOG_CHANNEL=stack
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=the-database
DB_USERNAME=root
DB_PASSWORD=secret

BROADCAST_DRIVER=log
CACHE_DRIVER=file
QUEUE_CONNECTION=redis
SESSION_DRIVER=file
SESSION_LIFETIME=120

MEMCACHED_HOST=127.0.0.1

REDIS_HOST=redis
REDIS_PASSWORD=null
REDIS_PORT=6379

# Add some mailtrap config here...
MAIL_MAILER=
MAIL_HOST=
MAIL_PORT=
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_ENCRYPTION=
MAIL_FROM_ADDRESS=
MAIL_FROM_NAME="${APP_NAME}"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=

PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_APP_CLUSTER=mt1

MIX_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
MIX_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"

# Docker stuff
DOCKER_APP=email-automation
DOCKER_APP_IP=10.13
```



## Automated tests:

This is the command for the tests:

```shell
$ vendor/bin/phpunit --testsuite Feature
```

If you are running from in a dockerized environment, this would do the job:

```shell
$ docker-compose exec php bash -v "vendor/bin/phpunit --testsuite Feature"
```



## Endpoints



### Endpoint 1

```
POST api/send?token={your JWT token here}

Body:
[
    {
        "email": string (required)
        "subject": string (required)
        "body": string (required)
        "attachment": [
            {
                "name": string (required if provided)
                "value": string (base64) (required if provided)
            }
        ]
    }
]

Response:
{
    "data": [
        {
            "email": string,
            "subject": string,
            "body": string,
            "user_id": int,
            "updated_at": string,
            "created_at": string,
            "id": int,
            "attachments": [
                {
                    "id": int,
                    "path": string,
                    "name": string,
                    "email_id": int,
                    "created_at": string,
                    "updated_at": string
                }
            ]
        }
    ]
}
```



### Endpoint 2

```
GET api/list?token={your JWT token here}

Response:
{
	"data": [
         {
            "id": int,
            "email": string,
            "subject": string,
            "body": string,
            "user_id": int,
            "created_at": string,
            "updated_at": string,
            "attachments": [
                {
                    "id": int,
                    "path": string,
                    "name": string,
                    "email_id": int,
                    "created_at": string,
                    "updated_at": string,
                    "link": string
                }
            ]
        }
    ]
}
```



### Endpoint 3

```
POST /api/auth/login

Body:
{
    "email": string,
    "password": string
}

Response:
{
    "access_token": string,
    "token_type": string,
    "expires_in": int
}
```



## Extras



- The token can be sent at the header `Authorization Bearer {{token}}` or at the url `{{url}}?token={{token}}`.
- At the root diretory of the project a Postman Json Collection of requests can be found: `EmailAutomationCase.postman_collection.json`. There you'll find the pre-requests scripts set up ready to work as soon as you have an user.



by Savio Resende - 2021-03-03