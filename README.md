# Тестовое задание

## Заметки
Есть пара кривых мест, которые были намеренно допущены для упрощения работы.

* Для контроля статуса анализа используются столбцы is_finished и is_failed в таблице analysis.
Это не самое хорошее решение, но для маленького проекта - пойдёт.

* Для работы с RabbitMQ используется бандл emag-tech-labs/rabbitmq-bundle, так как бандл от php-amqplib заброшен
и не поддерживает Symfony 5. Стоило реализовать работу с очередьми только силами библиотеки php-amqplib.

* Нет логирования.

* AnalysisConsumer делает слишком много всего.

## Установка
Установить проект можно используя Docker или классическим путём.
Если используется Docker, достаточно выполнить docker-compose up в папке проекта.

Иначе, требуется установить и сделать доступными на localhost PostgreSQL 10, RabbitMQ 3, Nginx 1 и PHP 7.3.

Разворачивание под Vagrant подобно классическому методу и требует соответствующий изменений.

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
Для работы проекта нужно выполнить серию шагов. Все последующие комманды нужно выполнять из папки проекта.

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
docker-compose exec php bash bin/console start_consumers.sh
```
#### Классический метод
```
bash bin/console start_consumers.sh
```