# Trucker API
This API is made to register, modify and delete truckers, its different truck types and to keep a record of their trackings.

## First setup
This project implements Docker containers. First you have to install Docker and Docker Compose. It is easily installed by
following [Docker documentation](https://docs.docker.com/get-docker/).

After cloning this repository to your machine, get into the cloned directory by the terminal
and type `docker-composer up -d --build` to raise the containers. Once they are up, get into the PHP container by typing
`docker exec -it trucker-api_php_1 bash`. You can also list the three containers with `docker ps` if you want to.

The port to be reached by your dev tool (e.g. Postman) is `localhost:8001`. To import a Postman collection, [click here](https://www.getpostman.com/collections/96ae9afea98fe451703a).

### Inside PHP container
Once inside the container, you must run the command `composer install` go get the required packages. To create the database, run `php bin/console doctrine:database:create` and
then `php bin/console doctrine:migrations:migrate` to insert the tables into it.

_**Advice**: There is a User Fixture already setted, but the authenticator is off for evaluation purposes. If you want to turn it on,
delete line 30 and uncomment lines 31-34 in `trucker-api\src\Security\JwtAuthenticator.php` file. Then run
`php bin/console doctrine:fixtures:load`_ (admin@truckpad.com:admin) _in the container. Unlogged users can access `/login` and `GET` requests only._

**Beware!** It is going to wipe off Truck Types data loaded on migration.

Also here in the container you can run Unit Tests with `./vendor/bin/simple_phpunit`. It may run an installer in the first time. It will run the services unit tests.

## Endpoints
I will let the JSON examples to the end of this reading, so you don't get confused by the wall of text.
There is one endpoint to login, CRUD to truckers, truck types and trackings. You must mind that trackings need a trucker id and truckers need a track type.
If you are going to populate it, you must start by truck types, than truckers and finally trackings.

Login endpoint will validate your e-mail and password and return a JWT bearer token, so the API don't need to worry abount saving sessions.

Truck types need only a name, easy-peasy.

Truckers are composed by name, birthdate, gender, CNH type, a track type id and two other fields to check if (s)he is the owner of the truck and if it is loaded.

Trackings are the sturdy ones. They require a trucker id (the one who is tracking!), check-in date and hour in the terminal, as well as the check-out,
and its origin and destinations full addresses. This API needs the full addresses to look for its latitude and longitude values.

Besides that, you can also filter and sort responses as you prefer with query strings, adding it to the end of the URL with a `?` (examples below).

### Must haves
As required by the guidelines, here I explain how you will get the desired results.

- To update truckers info, do a PUT request at `/truckers` endpoint with a JSON (check its template in the next section).

- To have origin and destination of truckers, just hit `/tracking`. If you want to see all trackings of a trucker, add the filter `/tracking?trucker={id}`.

- The endpoint to find all unloaded truckers, add the filter `/truckers?is_loaded=0`. It works to find loaded truckers as well. By default, if a trucker is unloaded
and you are going to POST a new tracking to him(her), the API will set destination the same as the origin, even if the POST have a different destination.

- Truckers who check in the terminal will be listed by `/tracking/check_in` endpoint. It is possible to add filters like a date interval, if (s)he is loaded and if you prefer to have the recent first (descending order): (`?since=YYYY-MM-DD&until=YYYY-MM-DD&is_loaded={0|1}&recent_first={0|1}`. All filters are optional. Get default filters in the next section. Latitude and longitude information are given by [LocationIQ](https://locationiq.com/) through [Geocoder lib](https://github.com/geocoder-php/Geocoder).

- Now if you want to have checked in truckers by the last days, run `/tracking/recent?days={int}`. This way you can have a day, week, month or any other amount of days as you wish.

- To see truckers who got its own truck, use the filter `truckers?is_owner={0|1}`.

- To upload truckers data, you must run a PUT request on `/truckers/{id}` endpoint.

> I hope from the bottom of my soul you guys like it! Thank you for the opportunity :)

# Requests and responses
## Login
### (POST /login)
_Request (application/json)_
```json
{
    "email": "admin@truckpad.com",
    "password": "admin"
}
```
_Response (200, application/json)_
```json
{
    "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJlbWFpbCI6ImFkbWluQHRydWNrcGFkLmNvbSJ9.1IlzZfxv_KA6TFpI9BhaeohpnaMOgWt3r5M3cfNwSOc"
}
```
## Truckers
Valid filters: `?name=Name&birthdate=YYYY-MM-DD&gender=O&is_owner={0|1}&cnh_type=E&is_loaded={0|1}&truck_type=1`

Valid sortings: `?sort[fieldName]={ASC|DESC}`

### (GET /truckers) Get all truckers
_Response (200, application/json)_
```json
{
    "success": true,
    "page": 1,
    "itemsPerPage": 10,
    "data": [
        {
            "id": 1,
            "name": "Test",
            "birthdate": "2020-01-01",
            "gender": "O",
            "is_owner": true,
            "cnh_type": "E",
            "is_loaded": true,
            "truck_type": 1,
            "_links": [
                {
                    "rel": "self",
                    "path": "/truckers/1"
                },
                {
                    "rel": "truck_type",
                    "path": "/truck_types/1"
                },
                {
                    "rel": "trackings",
                    "path": "/tracking?trucker=1&sort[id]=DESC"
                }
            ]
        },
        {
            "id": 2,
            "name": "Test",
            "birthdate": "2020-01-01",
            "gender": "O",
            "is_owner": true,
            "cnh_type": "E",
            "is_loaded": true,
            "truck_type": 2,
            "_links": [
                {
                    "rel": "self",
                    "path": "/truckers/2"
                },
                {
                    "rel": "truck_type",
                    "path": "/truck_types/1"
                },
                {
                    "rel": "trackings",
                    "path": "/tracking?trucker=2&sort[id]=DESC"
                }
            ]
        }
    ]
}
```

### (GET /truckers/{id}) Get one trucker
_Response (200, application/json)_
```json
{
    "success": true,
    "page": 1,
    "itemsPerPage": null,
    "data": [
        {
            "id": 1,
            "name": "Test",
            "birthdate": "2020-01-01",
            "gender": "O",
            "is_owner": true,
            "cnh_type": "AE",
            "is_loaded": true,
            "truck_type": 2,
            "_links": [
                {
                    "rel": "self",
                    "path": "/truckers/1"
                },
                {
                    "rel": "truck_type",
                    "path": "/truck_types/2"
                },
                {
                    "rel": "trackings",
                    "path": "/tracking?trucker=1&sort[id]=DESC"
                }
            ]
        }
    ]
}
```
### (POST /truckers) Insert one trucker

_Request (application/json)_
```json
{
    "name": "Test",
    "birthdate": "2020-01-01",
    "gender": "O",
    "is_owner": true,
    "cnh_type": "AE",
    "is_loaded": true,
    "truck_type_id": 2
}
```
_Response (200, application/json)_
```json
{
    "success": true,
    "page": 1,
    "itemsPerPage": null,
    "data": [
        {
            "id": 3,
            "name": "Test",
            "birthdate": "2020-01-01",
            "gender": "O",
            "is_owner": true,
            "cnh_type": "AE",
            "is_loaded": true,
            "truck_type": 2,
            "_links": [
                {
                    "rel": "self",
                    "path": "/truckers/3"
                },
                {
                    "rel": "truck_type",
                    "path": "/truck_types/2"
                },
                {
                    "rel": "trackings",
                    "path": "/tracking?trucker=3&sort[id]=DESC"
                }
            ]
        }
    ]
}
```

### (PUT /truckers/{id}) Update one trucker

_Request (application/json)_
```json
{
    "name": "Updated",
    "birthdate": "2020-01-01",
    "gender": "O",
    "is_owner": true,
    "cnh_type": "AE",
    "is_loaded": true,
    "truck_type_id": 2
}
```
_Response (200, application/json)_
```json
{
    "success": true,
    "page": 1,
    "itemsPerPage": null,
    "data": [
        {
            "id": 1,
            "name": "Updated",
            "birthdate": "2020-01-01",
            "gender": "O",
            "is_owner": true,
            "cnh_type": "AE",
            "is_loaded": true,
            "truck_type": 2,
            "_links": [
                {
                    "rel": "self",
                    "path": "/truckers/1"
                },
                {
                    "rel": "truck_type",
                    "path": "/truck_types/2"
                },
                {
                    "rel": "trackings",
                    "path": "/tracking?trucker=1&sort[id]=DESC"
                }
            ]
        }
    ]
}
```

### (DELETE /truckers/{id}) Delete one trucker

_Response (204)_

## Truck Type
Valid filters: `?name=Caminhão Toco`

Valid sortings: `?sort[id,name]={ASC|DESC}`

### (GET /truck_types) Get all the truck types
_Response (200, application/json)_
```json
{
    "success": true,
    "page": 1,
    "itemsPerPage": 10,
    "data": [
        {
            "id": 1,
            "name": "Caminhão 3/4",
            "_links": [
                {
                    "rel": "self",
                    "path": "/truck_types/1"
                },
                {
                    "rel": "truckers",
                    "path": "/truckers?truck_type=1"
                }
            ]
        },
        {
            "id": 2,
            "name": "Caminhão Toco",
            "_links": [
                {
                    "rel": "self",
                    "path": "/truck_types/2"
                },
                {
                    "rel": "truckers",
                    "path": "/truckers?truck_type=2"
                }
            ]
        },
        {
            "id": 3,
            "name": "Caminhão Truck",
            "_links": [
                {
                    "rel": "self",
                    "path": "/truck_types/3"
                },
                {
                    "rel": "truckers",
                    "path": "/truckers?truck_type=3"
                }
            ]
        },
        {
            "id": 4,
            "name": "Carreta Simples",
            "_links": [
                {
                    "rel": "self",
                    "path": "/truck_types/4"
                },
                {
                    "rel": "truckers",
                    "path": "/truckers?truck_type=4"
                }
            ]
        },
        {
            "id": 5,
            "name": "Carreta Eixo Estendido",
            "_links": [
                {
                    "rel": "self",
                    "path": "/truck_types/5"
                },
                {
                    "rel": "truckers",
                    "path": "/truckers?truck_type=5"
                }
            ]
        }
    ]
}
```

### (GET /truck_types/{id}) Get one truck type
_Response (200, application/json)_
```json
{
    "success": true,
    "page": 1,
    "itemsPerPage": null,
    "data": [
        {
            "id": 1,
            "name": "Caminhão 3/4",
            "_links": [
                {
                    "rel": "self",
                    "path": "/truck_types/1"
                },
                {
                    "rel": "truckers",
                    "path": "/truckers?truck_type=1"
                }
            ]
        }
    ]
}
```
### (POST /truck_types) Insert one truck type
_Request (application/json)_
```json
{
    "name": "Bumblebee"
}
```
_Response (200, application/json)_
```json
{
    "success": true,
    "page": 1,
    "itemsPerPage": null,
    "data": [
        {
            "id": 6,
            "name": "Bumblebee",
            "_links": [
                {
                    "rel": "self",
                    "path": "/truck_types/6"
                },
                {
                    "rel": "truckers",
                    "path": "/truckers?truck_type=6"
                }
            ]
        }
    ]
}
```

### (PUT /truck_types/{id}) Update one track type
_Request (application/json)_
```json
{
    "name": "Batmovel"
}
```
_Response (200, application/json)_
```json
{
    "success": true,
    "page": 1,
    "itemsPerPage": null,
    "data": [
        {
            "id": 6,
            "name": "Batmovel",
            "_links": [
                {
                    "rel": "self",
                    "path": "/truck_types/6"
                },
                {
                    "rel": "truckers",
                    "path": "/truckers?truck_type=6"
                }
            ]
        }
    ]
}
```

### (DELETE /track_types/{id}) Delete one track type

_Response (204)_

## Tracking
Valid filters: `?check_in=YYYY-MM-DD HH:MM&check_out=YYYY-MM-DD HH:MM`

Valid sortings: `?sort[id,trucker,check_in,check_out]={ASC|DESC}`

### (GET /tracking) Get all trackings
_Response (200, application/json)_
```json
{
    "success": true,
    "page": 1,
    "itemsPerPage": 10,
    "data": [
        {
            "id": 1,
            "trucker_id": 2,
            "fromAddress": {
                "id": 1,
                "street_name": "Rua Santa Barbara",
                "street_number": "1500",
                "neighborhood": "Jardim Santa Barbara",
                "city": "Guarulhos",
                "state": "SP",
                "zip_code": "07191310",
                "latitude": "-23.439815",
                "longitude": "-46.519099",
                "_links": [
                    {
                        "rel": "self",
                        "path": "/address/1"
                    }
                ]
            },
            "toAddress": {
                "id": 2,
                "street_name": "Avenida Santa Barbara",
                "street_number": "300",
                "neighborhood": "Jardim Santa Barbara",
                "city": "Guarulhos",
                "state": "SP",
                "zip_code": "07191310",
                "latitude": "-23.458217",
                "longitude": "-46.507477",
                "_links": [
                    {
                        "rel": "self",
                        "path": "/address/2"
                    }
                ]
            },
            "check_in": "2020-01-01 01:01",
            "check_out": "2020-01-01 02:02",
            "_links": [
                {
                    "rel": "self",
                    "path": "/tracking/1"
                },
                {
                    "rel": "trucker",
                    "path": "/truckers/2"
                }
            ]
        },
        {
            "id": 2,
            "trucker_id": 2,
            "fromAddress": {
                "id": 1,
                "street_name": "Rua Santa Barbara",
                "street_number": "1500",
                "neighborhood": "Jardim Santa Barbara",
                "city": "Guarulhos",
                "state": "SP",
                "zip_code": "07191310",
                "latitude": "-23.439815",
                "longitude": "-46.519099",
                "_links": [
                    {
                        "rel": "self",
                        "path": "/address/1"
                    }
                ]
            },
            "toAddress": {
                "id": 2,
                "street_name": "Avenida Santa Barbara",
                "street_number": "300",
                "neighborhood": "Jardim Santa Barbara",
                "city": "Guarulhos",
                "state": "SP",
                "zip_code": "07191310",
                "latitude": "-23.458217",
                "longitude": "-46.507477",
                "_links": [
                    {
                        "rel": "self",
                        "path": "/address/2"
                    }
                ]
            },
            "check_in": "2020-01-01 01:01",
            "check_out": "2020-01-01 02:02",
            "_links": [
                {
                    "rel": "self",
                    "path": "/tracking/2"
                },
                {
                    "rel": "trucker",
                    "path": "/truckers/2"
                }
            ]
        }
    ]
}
```

### (GET /tracking/{id}) Get one tracking
_Response (200, application/json)_
```json
{
    "success": true,
    "page": 1,
    "itemsPerPage": null,
    "data": [
        {
            "id": 1,
            "trucker_id": 2,
            "fromAddress": {
                "id": 1,
                "street_name": "Rua Santa Barbara",
                "street_number": "1500",
                "neighborhood": "Jardim Santa Barbara",
                "city": "Guarulhos",
                "state": "SP",
                "zip_code": "07191310",
                "latitude": "-23.439815",
                "longitude": "-46.519099",
                "_links": [
                    {
                        "rel": "self",
                        "path": "/address/1"
                    }
                ]
            },
            "toAddress": {
                "id": 2,
                "street_name": "Avenida Santa Barbara",
                "street_number": "300",
                "neighborhood": "Jardim Santa Barbara",
                "city": "Guarulhos",
                "state": "SP",
                "zip_code": "07191310",
                "latitude": "-23.458217",
                "longitude": "-46.507477",
                "_links": [
                    {
                        "rel": "self",
                        "path": "/address/2"
                    }
                ]
            },
            "check_in": "2020-01-01 01:01",
            "check_out": "2020-01-01 02:02",
            "_links": [
                {
                    "rel": "self",
                    "path": "/tracking/1"
                },
                {
                    "rel": "trucker",
                    "path": "/truckers/2"
                }
            ]
        }
    ]
}
```

### (GET /tracking/check_in) Get trucks by date range
Special filters: `?since=YYYY-MM-DD&until=YYYY-MM-DD&is_loaded={0|1}&recent_first={0|1}`
- `since`: Oldest point of the date range
- `until`: The most recent date of the range. Default: `now`
- `is_loaded`: Filter by loaded trucks. Default: `1`
- `recent_first`: Order by descending check-in date; Default: `1`
The response have the same format of `(GET) /trucking`
_Response (200, application/json)_

### (GET /tracking/check_in/recent) Get the recent trucks that passed by the terminal
Special filters: `?days=1&is_loaded={0|1}&recent_first={0|1}`
- `days`: Number of days past now. Default: `1` (today)
- `is_loaded`: Filter by loaded trucks. Default: `1`
- `recent_first`: Order by descending check-in date; Default: `1`
The response is the same from `(GET) /trucking`
_Response (200, application/json)_

### (GET /tracking/truck_types) Get all trackings grouped by truck types
_Response (200, application/json)_
```
{
    "success": true,
    "page": 1,
    "itemsPerPage": null,
    "data": [
        {
            "1": {
                "truckTypeName": "Caminhão 3/4",
                "trackings": [
                    {
                        "id": 1,
                        "_links": {
                            "rel": "self",
                            "path": "/tracking/1"
                        },
                        "from": {
                            "id": 1,
                            "street_name": "Rua Santa Barbara",
                            "street_number": "1500",
                            "neighborhood": "Jardim Santa Barbara",
                            "city": "Guarulhos",
                            "state": "SP",
                            "zip_code": "07191310",
                            "latitude": "-23.439815",
                            "longitude": "-46.519099",
                            "_links": [
                                {
                                    "rel": "self",
                                    "path": "/address/1"
                                }
                            ]
                        },
                        "to": {
                            "id": 2,
                            "street_name": "Avenida Santa Barbara",
                            "street_number": "300",
                            "neighborhood": "Jardim Santa Barbara",
                            "city": "Guarulhos",
                            "state": "SP",
                            "zip_code": "07191310",
                            "latitude": "-23.458217",
                            "longitude": "-46.507477",
                            "_links": [
                                {
                                    "rel": "self",
                                    "path": "/address/2"
                                }
                            ]
                        }
                    },
                    {
                        "id": 2,
                        "_links": {
                            "rel": "self",
                            "path": "/tracking/2"
                        },
                        "from": {
                            "id": 1,
                            "street_name": "Rua Santa Barbara",
                            "street_number": "1500",
                            "neighborhood": "Jardim Santa Barbara",
                            "city": "Guarulhos",
                            "state": "SP",
                            "zip_code": "07191310",
                            "latitude": "-23.439815",
                            "longitude": "-46.519099",
                            "_links": [
                                {
                                    "rel": "self",
                                    "path": "/address/1"
                                }
                            ]
                        },
                        "to": {
                            "id": 2,
                            "street_name": "Avenida Santa Barbara",
                            "street_number": "300",
                            "neighborhood": "Jardim Santa Barbara",
                            "city": "Guarulhos",
                            "state": "SP",
                            "zip_code": "07191310",
                            "latitude": "-23.458217",
                            "longitude": "-46.507477",
                            "_links": [
                                {
                                    "rel": "self",
                                    "path": "/address/2"
                                }
                            ]
                        }
                    }
                ]
            },
            "2": {
                "truckTypeName": "Caminhão Toco",
                "trackings": []
            },
            "3": {
                "truckTypeName": "Caminhão Truck",
                "trackings": [
                    {
                        "id": 3,
                        "_links": {
                            "rel": "self",
                            "path": "/tracking/3"
                        },
                        "from": {
                            "id": 3,
                            "street_name": "Avenida Santa Barbara",
                            "street_number": "123",
                            "neighborhood": "Jardim Santa Barbara",
                            "city": "Guarulhos",
                            "state": "SP",
                            "zip_code": "07191310",
                            "latitude": "-23.458217",
                            "longitude": "-46.507477",
                            "_links": [
                                {
                                    "rel": "self",
                                    "path": "/address/3"
                                }
                            ]
                        },
                        "to": {
                            "id": 3,
                            "street_name": "Avenida Santa Barbara",
                            "street_number": "123",
                            "neighborhood": "Jardim Santa Barbara",
                            "city": "Guarulhos",
                            "state": "SP",
                            "zip_code": "07191310",
                            "latitude": "-23.458217",
                            "longitude": "-46.507477",
                            "_links": [
                                {
                                    "rel": "self",
                                    "path": "/address/3"
                                }
                            ]
                        }
                    }
                ]
            },
            "4": {
                "truckTypeName": "Carreta Simples",
                "trackings": [
                    {
                        "id": 4,
                        "_links": {
                            "rel": "self",
                            "path": "/tracking/4"
                        },
                        "from": {
                            "id": 5,
                            "street_name": "Avenida Santa Barbara",
                            "street_number": "2222",
                            "neighborhood": "Jardim Santa Barbara",
                            "city": "Guarulhos",
                            "state": "SP",
                            "zip_code": "07191310",
                            "latitude": "-23.458217",
                            "longitude": "-46.507477",
                            "_links": [
                                {
                                    "rel": "self",
                                    "path": "/address/5"
                                }
                            ]
                        },
                        "to": {
                            "id": 5,
                            "street_name": "Avenida Santa Barbara",
                            "street_number": "2222",
                            "neighborhood": "Jardim Santa Barbara",
                            "city": "Guarulhos",
                            "state": "SP",
                            "zip_code": "07191310",
                            "latitude": "-23.458217",
                            "longitude": "-46.507477",
                            "_links": [
                                {
                                    "rel": "self",
                                    "path": "/address/5"
                                }
                            ]
                        }
                    }
                ]
            },
            "5": {
                "truckTypeName": "Carreta Eixo Estendido",
                "trackings": []
            }
        }
    ]
}
```
### (POST /tracking) Insert one tracking
_Request (application/json)_
```json
{
    "trucker_id": 2,
    "from": {
        "street_name": "Rua Santa Barbara",
        "street_number": "1500",
        "neighborhood": "Jardim Santa Barbara",
        "zip_code": "07191310",
        "city": "Guarulhos",
        "state": "SP"
    },
    "to": {
        "street_name": "Avenida Santa Barbara",
        "street_number": "300",
        "neighborhood": "Jardim Santa Barbara",
        "zip_code": "07191310",
        "city": "Guarulhos",
        "state": "SP"
    },
    "check_in": "2020-01-01 01:01",
    "check_out": "2020-01-01 02:02"
}
```
_Response (200, application/json)_
```json
{
    "success": true,
    "page": 1,
    "itemsPerPage": null,
    "data": [
        {
            "id": 3,
            "trucker_id": 2,
            "fromAddress": {
                "id": 1,
                "street_name": "Rua Santa Barbara",
                "street_number": "1500",
                "neighborhood": "Jardim Santa Barbara",
                "city": "Guarulhos",
                "state": "SP",
                "zip_code": "07191310",
                "latitude": "-23.439815",
                "longitude": "-46.519099",
                "_links": [
                    {
                        "rel": "self",
                        "path": "/address/1"
                    }
                ]
            },
            "toAddress": {
                "id": 2,
                "street_name": "Avenida Santa Barbara",
                "street_number": "300",
                "neighborhood": "Jardim Santa Barbara",
                "city": "Guarulhos",
                "state": "SP",
                "zip_code": "07191310",
                "latitude": "-23.458217",
                "longitude": "-46.507477",
                "_links": [
                    {
                        "rel": "self",
                        "path": "/address/2"
                    }
                ]
            },
            "check_in": "2020-01-01 01:01",
            "check_out": "2020-01-01 02:02",
            "_links": [
                {
                    "rel": "self",
                    "path": "/tracking/3"
                },
                {
                    "rel": "trucker",
                    "path": "/truckers/2"
                }
            ]
        }
    ]
}
```
### (PUT /tracking/{id}) Update one tracking
_Request (application/json)_
```json
{
    "trucker_id": 2,
    "from": {
        "street_name": "Avenida Santa Barbara",
        "street_number": "123",
        "neighborhood": "Jardim Santa Barbara",
        "zip_code": "07191310",
        "city": "Guarulhos",
        "state": "SP"
    },
    "to": {
        "street_name": "Avenida Santa Barbara",
        "street_number": "8001",
        "neighborhood": "Jardim Santa Barbara",
        "zip_code": "07191310",
        "city": "Guarulhos",
        "state": "SP"
    },
    "check_in": "2020-01-01 01:01",
    "check_out": "2020-01-01 02:02"
}
```
_Response (200, application/json)_
```json
{
    "success": true,
    "page": 1,
    "itemsPerPage": null,
    "data": [
        {
            "id": 3,
            "trucker_id": 2,
            "fromAddress": {
                "id": 3,
                "street_name": "Avenida Santa Barbara",
                "street_number": "123",
                "neighborhood": "Jardim Santa Barbara",
                "city": "Guarulhos",
                "state": "SP",
                "zip_code": "07191310",
                "latitude": "-23.458217",
                "longitude": "-46.507477",
                "_links": [
                    {
                        "rel": "self",
                        "path": "/address/3"
                    }
                ]
            },
            "toAddress": {
                "id": 4,
                "street_name": "Avenida Santa Barbara",
                "street_number": "8001",
                "neighborhood": "Jardim Santa Barbara",
                "city": "Guarulhos",
                "state": "SP",
                "zip_code": "07191310",
                "latitude": "-23.458217",
                "longitude": "-46.507477",
                "_links": [
                    {
                        "rel": "self",
                        "path": "/address/4"
                    }
                ]
            },
            "check_in": "2020-01-01 01:01",
            "check_out": "2020-01-01 02:02",
            "_links": [
                {
                    "rel": "self",
                    "path": "/tracking/3"
                },
                {
                    "rel": "trucker",
                    "path": "/truckers/2"
                }
            ]
        }
    ]
}
```

### (DELETE /tracking/{id}) Delete one tracking
_Response (204)_
