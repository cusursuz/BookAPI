# BooksAPI
BooksAPI is a PHP application based on the Symphony framework
## Instalation
```bash
# ...Clone the code repository and install its dependencies
$ git clone https://github.com/cusursuz/BooksAPI.git my_project
# ...Change directory
$ cd my_project/
# ...Install all the dependencies
$ composer install
```

## DB setup
Set up a connection to the Database in the .env file

## Populate DB
```bash
$ php bin/console doctrine:fixtures:load
```
## Start project
```bash
$ symfony serve
```

## Enjoy
)

## Autorization
For Create, Update or Delete use next credentials
Username: test_user
Password: test_password
## Documentation 
http://localhost:8000/api/doc