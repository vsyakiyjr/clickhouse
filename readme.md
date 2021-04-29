### Установка ###
```
cd /path/to/project
composer install 
php artisan key:generate
```

Заполнить переменные .env:
- Для БД:
```
DB_CONNECTION
DB_HOST
DB_PORT
DB_DATABASE
DB_USERNAME
DB_PASSWORD
``` 

- для отправки почты:
```
MAIL_DRIVER
MAIL_HOST
MAIL_PORT
MAIL_USERNAME
MAIL_PASSWORD
MAIL_FROM_NAME
MAIL_FROM_ADDRESS
MAIL_ENCRYPTION
```
- Полный путь к PHP для корректной работы парсера: 
`PHP`
- Email, на который отправляется письмо-уведомление о новом заказе: `SUPPORT_EMAIL` 

### Сборка фронта: ###

* `npm run dev`        (development build mode)
* `npm run watch`      (development watcher)
* `npm run production` (production build mode)
