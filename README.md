
INSTALLATION
------------

###  1 Clone

###  2 Run container 

~~~
docker-compose up -d
~~~

###  3 Run composer

~~~
docker-compose exec php composer install --prefer-dist
~~~

###  4 Create database

~~~
# before: chmod +x ./create_database
docker-compose exec mysql /opt/create_database
~~~

###  5 Apply migrations

~~~
docker-compose exec php yii migrate --interactive=0
~~~
  
You can then access the application through the following URL:

    http://127.0.0.1:8000

Задание
-------

Необходимо импортировать данные и изображения из приложенного файла https://yadi.sk/d/7nOPGKFD3PNmAi 

При этом:
1. Использовать Yii2 Framework, MySQL
2. Запуск импорта должен происходить по обращению к странице в браузере, 
например: test.ru/import 

    2.1. По завершению должен выдать сообщение на русском языке о результатах.
3. Результат должен быть представлен в виде стандартных CRUD:

    3.1. Категории
    
    3.2. Техника (с выводом изображений)
    
    3.3. Характеристики техники
    
4. Разрешено использовать чужие плагины, а также Gii-генератор
