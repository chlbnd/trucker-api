# Trucker API
This API is made to register, modify and delete truckers, its different truck types and to keep a record of their trackings.

## First setup
This project implements Docker containers. First you have to install Docker and Docker Compose. It is easily installed by
following [Docker documentation](https://docs.docker.com/get-docker/).


After cloning this repository to your machine, get into the cloned directory by the terminal
and type `docker-composer up -d --build` to raise the containers. Once they are up, get into the PHP container by typing
`docker exec -it trucker-api_php_1 bash`. You can also list the three containers with `docker ps` if you want to.

### Inside PHP container
Once inside the container, you must run the command to create the database: `php bin/console doctrine:database:create` and
then `php bin/console doctrine:migrations:migrate` to insert the tables into it.

_**Advice**: There is a User Fixture already setted, but the authenticator is off for evaluation purposes. If you want to turn it on,
delete line 30 and uncomment lines 31-34 in `trucker-api\src\Security\JwtAuthenticator.php` file. Then run
`php bin/console doctrine:fixtures:load`_ (admin@truckpad.com:admin) _in the container. Unlogged users can access `/login` and `GET` requests only._

**Beware!** It is going to wipe off Truck Types data loaded on migration.

Also here in the container you can run Unit Tests by `php bin/phpunit`. It may run an installer in the first time. It will run the services unit tests.

## Endpoints
I will let the JSON examples to the end of this reading, so you don't get confused by the wall of text.
There is one endpoint to login, CRUD to truckers, truck types and trackings. You must mind that trackings need a trucker id and truckers need a track type.
If you are going to populate it, you must start by truck types, than truckers and finally trackings.

Login endpoint will validate your e-mail and password and return a bearer token, so the API don't need to worry abount saving sessions.

Truck types need only a name, easy-peasy.

Truckers are composed by name, birthdate, gender, CNH type, a track type id and two other fields to check if (s)he is the owner of the truck and if it is loaded.

Trackings are the sturdy ones. They require a trucker id (the one who is tracking!), check-in date and hour in the terminal, as well as the check-out,
and its origin and destinations full addresses. This API needs the full addresses to look for its latitude and longitude values.

Besides that, you can also filter and sort responses as you prefer with query strings, adding it to the end of the URL with a `?` (examples below).

## Requests and responses documentation
