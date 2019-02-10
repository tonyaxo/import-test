
INSTALLATION
------------

###  1 Clone

###  2 Run container 

~~~
docker-compose up -d
~~~

###  3 Apply migrations

~~~
docker-compose exec php yii migrate --interactive=0
~~~
  
###  4 Create database

~~~
# before: chmod +x ./create_database
docker-compose exec mysql /opt/create_database
~~~

You can then access the application through the following URL:

    http://127.0.0.1:8000
