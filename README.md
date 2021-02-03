 Bookstore clinet & API example 
==========

Libraries:
 - [Symfony 5.2](https://symfony.com/what-is-symfony)
 - [PHP 8](https://www.php.net/releases/8.0/en.php)
 - [lexik/jwt-authentication-bundle](https://github.com/lexik/LexikJWTAuthenticationBundle)
 - [NelmioApiDocBundle](https://symfony.com/doc/current/bundles/NelmioApiDocBundle/index.html)

## Installation

### Local server

* If not already done, install [Docker](https://www.docker.com/) and [Docker Compose](https://docs.docker.com/compose/install/) 

* Clone the repository:  

  > ``` 
  > $ git clone git@github.com:ovidiuj/bookstore.git
  > ``` 
   
* Run 

  > ``` 
  > $ cd bookstore 
  > ``` 
   
* Run
   
  > ```
  > $ docker-compose up -d --build
  > ```
   
* Run 
   
  > ```
  > $ docker-compose ps
  > ```
   
  > This results in the following running containers: 

   
         Name                           Command               State                 Ports
         ----------------------------------------------------------------------------------------------------------------------------------------
         docker_nginx_1                   /docker-entrypoint.sh nginx     Up      0.0.0.0:443->443/tcp, 0.0.0.0:81->80/tcp, 0.0.0.0:9001->9001/tcp
         docker_php_1                     docker-php-entrypoint /bin ...  Up      9000/tcp 
         docker_postgres_1                docker-entrypoint.sh postgres   Up      127.0.0.1:5432->5432/tcp                                        
         docker_redis_1                   docker-entrypoint.sh redis ...  Up      0.0.0.0:6379->6379/tcp
      
   
* Run
   
  > ```
  > $ docker-compose exec -T php mkdir -p config/jwt  
  > ```
  
  > ```
  > $ docker-compose exec -T php openssl genpkey -out config/jwt/private.pem -aes256 -algorithm rsa -pkeyopt rsa_keygen_bits:4096  
  > ```
  
  > ```
  > $ docker-compose exec -T php openssl pkey -in config/jwt/private.pem -out config/jwt/public.pem -pubout  
  > ```
  
  > ```
  > $ docker-compose exec -T php chmod 644 config/jwt/public.pem config/jwt/private.pem
  > ```
   
  > You will be asked to enter the pass phrase, which is by default *bookstore*. If you add another pass phrase, then you should modify *.env* file and set *JWT_PASSPHRASE=your-pass-phrase*.  
   
* Run
   
  > ```
  >  docker-compose exec -T php ./setup.sh
  > ```
   
* Open `http://localhost/doc/api` in your favorite web browser. This is the documentation for all API endpoints.
* Open `http://localhost/book` in your favorite web browser. This is web client interface.

## Usage example

* By default there is default user added in the database. The user credentials are: 
  
   > ```
   > {
   >    "username": "admin",
   >    "password":"bookstore"
   > }
   > ```
  
