# Лабораторная работа №10  
## Управление секретами в контейнерах Docker

---

# Цель работы

Целью данной работы является изучение и практическое применение механизмов управления секретами в Docker-контейнерах, а также построение многосервисного приложения с использованием Docker Compose, MySQL и Docker Secrets.

---

# Задание

Создать многосервисное веб-приложение с использованием Docker, включающее:

- Frontend (Nginx)
- Backend (PHP-FPM)
- Database (MariaDB)
- Использование Docker Secrets для хранения конфиденциальных данных
- Подключение backend к базе данных через переменные окружения и секреты
- Проверка работы приложения

---

# Архитектура проекта

Проект состоит из трёх контейнеров:

- **Frontend (nginx)** — принимает HTTP запросы  
- **Backend (php-fpm)** — обрабатывает PHP логику  
- **Database (mariadb)** — хранит данные  

Также используется:

- Docker Networks (frontend, backend)
- Docker Secrets

---

# Структура проекта

```text
containers10/
├── site/
│   ├── modules/
│   │   ├── database.php
│   ├── config.php
│   └── index.php
├── secrets/
│   ├── root_secret
│   ├── user
│   └── secret
├── Dockerfile
├── docker-compose.yaml
├── nginx.conf
````

---

# Реализация

---

## Docker Compose

Сервисная структура:

* frontend (nginx)
* backend (php-fpm)
* database (mariadb)

Используются:

* networks
* secrets

---

## Docker Secrets

Секреты:

* root_secret
* user
* secret

<img width="1919" height="1079" alt="image" src="https://github.com/user-attachments/assets/873485da-4f54-497b-8881-daf06f2a1acb" />


Использование:

```yaml
services:
  frontend:
    image: nginx:latest
    ports:
      - "80:80"
    volumes:
      - ./site:/var/www/html
      - ./nginx.conf:/etc/nginx/conf.d/default.conf
    networks:
      - frontend
      - backend

  backend:
    build:
      context: .
      dockerfile: Dockerfile
    environment:
      MYSQL_HOST: database
      MYSQL_DATABASE: my_database
    secrets:
      - user
      - secret
    networks:
      - backend
      - frontend

  database:
    image: mariadb:latest
    environment:
      MYSQL_ROOT_PASSWORD_FILE: /run/secrets/root_secret
      MYSQL_DATABASE: my_database
      MYSQL_USER_FILE: /run/secrets/user
      MYSQL_PASSWORD_FILE: /run/secrets/secret
    secrets:
      - root_secret
      - user
      - secret
    networks:
      - backend

networks:
  frontend: {}
  backend: {}

secrets:
  root_secret:
    file: ./secrets/root_secret
  user:
    file: ./secrets/user
  secret:
    file: ./secrets/secret
```

---

## Backend (PHP)

Подключение:

```php
<?php

require_once __DIR__ . '/modules/Database.php';
require_once __DIR__ . '/config.php';

$dsn = "mysql:host={$config['db']['host']};dbname={$config['db']['database']};charset=utf8";

$db = new Database(
    $dsn,
    $config['db']['username'],
    $config['db']['password']
);

echo "Connected to database successfully!";
```

---

## Чтение secrets

```php
<?php

function get_file_contents($path) {
    return trim(file_get_contents($path));
}

$config['db']['host'] = getenv('MYSQL_HOST');
$config['db']['database'] = getenv('MYSQL_DATABASE');

$config['db']['username'] = get_file_contents('/run/secrets/user');
$config['db']['password'] = get_file_contents('/run/secrets/secret');
```

---

## Nginx

```nginx
server {
    listen 80;
    index index.php;
    root /var/www/html;

    location / {
        try_files $uri $uri/ /index.php?$args;
    }

    location ~ \.php$ {
        fastcgi_pass backend:9000;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME /var/www/html$fastcgi_script_name;
    }
}
```
---

# Запуск проекта

## Остановка:

```bash
docker-compose down -v
```

## Сборка:

```bash
docker-compose up --build
```
<img width="1919" height="1079" alt="image" src="https://github.com/user-attachments/assets/ddf53959-ec7f-4b95-9fc5-45f18fc30aed" />


---

# Проверка

```text
http://localhost
```

Результат:

```text
Connected to database successfully!
```
<img width="1919" height="1079" alt="image" src="https://github.com/user-attachments/assets/e1b228b0-6ca6-4dd8-b666-ba14f88cbcc2" />

---

# Проверка безопасности

```bash
docker scout quickview containers10-backend
```

<img width="1389" height="431" alt="image" src="https://github.com/user-attachments/assets/0fcaec13-7521-46a4-98dd-700202ac5c66" />

---

# Ответы на вопросы

## 1. Почему нельзя передавать секреты в образ?

* сохраняются в слоях image
* можно извлечь
* нарушение безопасности

---

## 2. Как правильно хранить секреты?

* Docker Secrets
* Vault системы
* env (ограниченно)

---

## 3. Как работают Docker Secrets?

* создаются как файлы
* монтируются в /run/secrets/
* читаются приложением

---

# Вывод

В ходе работы:

* создано многосервисное приложение
* настроен Docker Compose
* реализованы Docker Secrets
* подключена база данных MySQL
* обеспечена безопасность хранения данных

```
