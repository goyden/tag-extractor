# Тестовое задание

## Заметки
Есть пара кривых мест, которые были намеренно допущены для упрощения работы.

* Для контроля статуса анализа используются столбцы is_finished и is_failed в таблице analysis.
Это не самое хорошее решение, но для маленького проекта - пойдёт.

* Для работы с RabbitMQ используется бандл emag-tech-labs/rabbitmq-bundle, так как бандл от php-amqplib заброшен
и не поддерживает Symfony 5. Стоило реализовать работу с очередьми только силами библиотеки php-amqplib.

* Нет логирования.

* Ошибки валидации стоит сделать красивее.

* Не сохраняется причина провала анализа.

* AnalysisConsumer делает слишком много всего.

## Установка
Установить проект можно используя Docker или классическим путём.
Далее %project_root% - это корневая папка проекта.

#### Docker
* Скопировать %project_root%/docker/php/docker.env.dist в %project_root%/docker/php/docker.env
* Скопировать %project_root%/docker/postgres/docker.env.dist в %project_root%/docker/postgres/docker.env
* Перейти в %project_root% и выполнить docker-compose up.

docker.env файлы содержат настройки для контейнеров Docker, их можно отредактировать по желанию.

#### Классический метод
Установить и сделать доступными на localhost PostgreSQL 10, RabbitMQ 3, Nginx 1 и PHP 7.3.

Разворачивание под Vagrant/виртуальные машины подобно классическому методу и требует соответствующий изменений.

## Конфигурация
Конфигурация по умолчанию хранится в .env и может быть переопределена созданием файла .env.local.

#### Docker
Конфигурация в .env сразу верна и ничего менять не нужно.

#### Классический метод
Нужно отредактировать переменные окружения, отвечающие за доступ к PostgreSQL и RabbitMQ:
* DATABASE_URL
* RABBITMQ_HOST
* RABBITMQ_PORT
* RABBITMQ_USER
* RABBITMQ_PASSWORD

Подробней про **DATABASE_URL**

https://www.doctrine-project.org/projects/doctrine-dbal/en/latest/reference/configuration.html#connecting-using-a-url

## Разворачивание
Для работы проекта нужно выполнить серию шагов. Все последующие комманды нужно выполнять в %project_root%.

### Установка зависимостей
Composer установит PHP зависимости и проверит, все ли PHP расширения доступны. 

#### Docker
```
docker-compose exec php composer install
```
#### Классический метод
```
composer install
```

### Создание базы данных
#### Docker
```
База данных уже создана.
```
#### Классический метод
```
php bin/console doctrine:database:create
```

### Применение миграций 
#### Docker
```
docker-compose exec php php bin/console doctrine:migrations:migrate
```
#### Классический метод
```
php bin/console doctrine:migrations:migrate
```

### Запуск RabbitMQ consumer
#### Docker 
```
docker-compose exec php bash bin/start_consumers.sh
```
#### Классический метод
```
bash bin/console start_consumers.sh
```

### Использование

#### Создание анализа URL
Для создания запроса на анализ URL, нужно сделать запрос:
```http request
POST /tags
```
Передав при этом в теле запроса JSON объект, содержащий ключ url с целевым URL:
```json
{
  "url": "https://example.com"
}
``` 

В ответе будет ID созданного анализа: 
```json
{
    "id": 1
}
```

#### Получение результатов анализа
Для получения результатов анализа URL, нужно сделать запрос:
```http request
GET /tags?id=1
```
Где параметер id содержит ID анализа, полученный при его создании.

В случае успеха, в ответ будет содержать JSON объект, где ключи - найденные тэги а значение - сколько раз они
встречались в документе.
Пример:
```json
{
    "html": 1,
    "head": 1,
    "body": 1,
    "title": 1,
    "meta": 20
}
```

Ответ, если анализ с таким ID не найден:
```json
{
    "code": 404,
    "message": "Analysis with this ID was not found."
}
```

Ответ, если анализ ещё в процессе:
```json
{
    "code": 202,
    "message": "Analysis is in progress."
}
``` 

Ответ, в случае проблем с запросом URL:
```json
{
    "code": 400,
    "message": "Analysis is failed. There was some problems with requesting it's URL."
}
```

Если какой-то из параметров запроса был неверен, ответ будет подобным:
```json
{
    "code": 400,
    "message": "Validation Failed",
    "errors": {
        "children": {
            "id": {
                "errors": [
                    "This value should not be null."
                ]
            }
        }
    }
}
```
Где **children** будет содержать проблемное поле поле а **errors** - причину проблемы.