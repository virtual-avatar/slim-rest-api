# slim-rest-api
SLIM v4, JSON, CRUD, REST API

#### Задание:
Необходимо написать маленькое приложение на Slim Framework предоставляющие REST API по работе с сущностью User.

REST API должно удовлетворять следующие возможности:
##### Добавление User
##### Получение списка User
##### Получение User по Id
##### Редактирование User по Id
##### Удаление User по Id

REST API должно работать с форматом данных JSON.

Сущность User должно состоять минимум из следующих полей:
##### Идентификатор пользователя
##### Отображаемое имя

Вы можете использовать дополнительные поля, если считаете нужным.

В качестве хранилища данных нужно использовать файл в формате JSON.

#### Подготовка:

git clone https://github.com/virtual-avatar/slim-rest-api.git

composer update

Поместить в каталог public файл .htaccess:
```
<IfModule mod_rewrite.c>
  RewriteEngine On
  RewriteCond %{REQUEST_URI}::$1 ^(/.+)/(.*)::\2$
  RewriteRule ^(.*) - [E=BASE:%1]
  # If the above doesn't work you might need to set the `RewriteBase` directive manually, it should be the
  # absolute physical path to the directory that contains this htaccess file.
  # RewriteBase /
  RewriteCond %{REQUEST_FILENAME} !-f
  RewriteRule ^ index.php [QSA,L]
</IfModule>
```

#### Решение:

<ul>
    <li>Используется фреймворк SLIM v4</li>
    <!--<li><a target="_blank" href="https://psychogenic-thermoc.000webhostapp.com/users">Демо-версия работы сайта</a></li>-->
</ul>

REST API :
1) Добавление User

POST запрос на адрес http://.../users

Поля:
```
{
   "id": "6",
   "name": "Иванов Иван Иванович",
   "phome": "+380974563223"
}
```
Ответ:
```
{
    "data": {
        "id": "6",
        "name": "Иванов Иван Иванович",
        "phome": "+380974563223"
    },
    "code": 1,
    "message": "Данные успешно добавлены"
}
```

2) Получение списка User

GET запрос адрес http://.../users

Ответ:
```
[
{
"id": "5",
"name": "Иванов Виктор Михайлович",
"phone": "+380974563223"
},
{
"id": "1",
"name": "Петров Василий Николаевич",
"phone": "+380974563223"
},
{
"id": "2",
"name": "Сидоров Степн Васильевич",
"phone": "+380974563223"
}
]
```
3) Получение User по Id

GET запрос адрес http://.../users/5

Ответ:
```
{
"data": {
"id": "5",
"name": "Иванов Виктор Михайлович",
"phome": "+380974563223"
},
"code": 1,
"message": "OK"
}
```
4) Редактирование User по Id

PUT запрос адрес http://.../users/1
```
{
    "data": [],
    "code": 0,
    "message": "Данные успешно обновлены"
}
```

5) Удаление User по Id

DELETE запрос адрес http://true-conf.local/users/1
```
{
    "data": [],
    "code": 0,
    "message": "Данные успешно удалены"
}
```

