<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Authentication-Microservice
The authentication and authorization microservice is responsible for authenticating users and authorizing access to protected resources in a microservice-based application architecture. This microservice interacts with Laravel Passport, which provides OAuth2 functionality and allows the microservice to generate access tokens for authenticated users.

When a user attempts to log in, the authentication and authorization microservice first validates the user's credentials, typically by checking them against a user database. If the credentials are valid, the microservice generates an access token using Laravel Passport.

The access token is a secure string that represents the authenticated user and is used to authorize subsequent API requests made by the user. The authentication and authorization microservice stores the access token in a database, along with information about the user and the token's expiration time.

When a user makes an API request, the request is first routed through the API gateway to the appropriate microservice. If the microservice requires authentication and authorization, the API gateway extracts the access token from the request and sends it to the authentication and authorization microservice for validation.

The authentication and authorization microservice validates the access token by checking it against the database of valid tokens. If the token is valid and has not expired, the microservice authorizes the API request and sends a response to the API gateway indicating that the request is authorized.

If the token is invalid or has expired, the authentication and authorization microservice sends an error response to the API gateway indicating that the request is unauthorized. The API gateway then sends an error response back to the client application.

In summary, the authentication and authorization microservice is responsible for authenticating users and authorizing access to protected resources in a microservice-based application architecture. Laravel Passport is used to generate secure access tokens for authenticated users, which are then validated and authorized by the authentication and authorization microservice.

---

## Guide to using Authentication-Microservice locally
1. Enable extension=fileinfo, extension=pdo_sqlite, extension=sqlite3, extension=pdo_mysql in php.ini
2. Run composer install
3. Run docker-compose up in the terminal
4. Comment out "PDO::MYSQL_ATTR_SSL_CA => storage_path('DigiCertGlobalRootCA.crt.pem')" in config/database.php
4. Run php artisan passport:client --password in the terminal and use 'AuthClient' as the name and type in 'users' as the user provider.
5. In the .env file, set the PASSPORT_CLIENT_ID and PASSPORT_CLIENT_SECRET values with what you got in step 4.
6. Run php artisan migrate:reset --env=testing & php artisan migrate --env=testing to setup the test database
7. Run php artisan migrate to setup the actual database
8. php artisan serve --port=8000 for the local webserver
9. php artisan serve --port=8001 in another terminal for the secondary webserver for the oauth. Without this, you cannot use the endpoints since PHP is single-threaded and
it will have issues handling the internal call the oauth server will do.
10. Open Postman and you can call the endpoints described in this README.

---

## Guide to using Authentication-Microservice on Azure Kubernetes
1. Run Azure Pipeline (already setup)
2. FIRST TIME ONLY:
```
kubectl get pods -n label-dev
kubectl exec -it [pod_name] -- /bin/bash
php artisan passport:client --password
    - Name: AuthClient
    - User provider: users

Copy the client id and secret values and place them in the Azure Pipeline Variables.
```

---


## How to open MySQL database using SSMS
1. Install the MySQL ODBC driver on your machine. You can download the driver from the official MySQL website (https://dev.mysql.com/downloads/connector/odbc/).
2. CTRL + R, type in "odbcad32" and press ENTER.
    * Left-click on "System DSN" and select "Add".
    * Fill in the details:
        > <b>Data Source Name:</b> Auth MySQL <br>
        > <b>Description:</b> Database for the Auth microservice <br>
        > <b>TCP/IP Server:</b> localhost <br>
        > <b>Port:</b> 3306 <br>
        > <b>User:</b> auth <br>
        > <b>Password:</b> [SECRET] | See in .env <br>
        > <b>Database:</b> authentication

2. Open SSMS and go to the "Object Explorer" window. Open "Server Objects", right-click on the "Linked Servers" folder and select "New Linked Server".
3. In the "New Linked Server" dialog box, fill out the following fields:
    > <b>Linked server:</b> AUTH <br>
    > <b>Server type:</b> Choose "Other data source" from the drop-down list. <br>
    > <b>Provider:</b> Choose "Microsoft OLE DB Provider for ODBC Drivers" from the drop-down list. <br>
    > <b>Product name:</b> AuthMicroDB <br>
    > <b>Data source:</b> Auth MySQL <br>
    > <b>Provider string:</b> DRIVER={MySQL ODBC 8.0 Unicode Driver}; SERVER=localhost; PORT=3306; DATABASE=authentication; USER=auth; PASSWORD=[SECRET] ; <br>


4. Click "OK" to save the linked server.
5. You can now expand the "Linked Servers" folder in the "Object Explorer" window to see the linked server you just created. You can expand it further to see the tables and other objects in the database.
6. You can execute SQL queries against the database using SSMS, for example:
```sql
SELECT * FROM openquery(AUTH, 'SELECT * FROM users')
DELETE openquery(AUTH, 'SELECT * FROM users')
```
Note that you may need to open port 3306 on your firewall or router to allow external access to the MySQL database. Also, make sure that the MySQL container is running and that you can connect to it using a MySQL client before attempting to connect using SSMS.

If you're facing issues connecting to the Linked Server, right-click on AUTH, go to "Security", and click on "Be made using this security context". Fill in the login information of the MySQL database and try to connect again by writing a query.

---
## Adjust database configuration for SSL/TLS connection

1. First, you need to download the SSL certificate from Azure Flexible Server. You can download the certificate from the Azure portal by navigating to the MySQL server instance and selecting the "SSL/ TLS settings" option under the Security section. Then, click on the "Download public certificate" button to download the certificate.

2. Once you have the SSL certificate, copy it to your /storage.

3. In your Laravel application, open the database configuration file located at config/database.php.

4. Update the MySQL database configuration to include the SSL parameters. Here's an example configuration:

```
// Add SSL parameters
    'options'   => array(
        PDO::MYSQL_ATTR_SSL_CA => 'storage/DigiCertGlobalRootCA.crt.pem',
        PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false,
    ),
```

---
## Errors

1. Message: Client authentication failed
```
Run step 4 and 5 of the local guide
OR
Run step 2 of the kubernetes guide
```
---

## URL
<p>Local URL: <a href="http://localhost:8000">http://localhost:8000</a><p>
<p>Azure URL: <a href="https://label-service.uk">https://label-service.uk</a><p>

----

## Endpoints
<h3>Endpoint: Register</h3>
<p><b>Request URL:</b> /api/v1/auth/register</p>
<p><b>Request method:</b> POST</p>
<p><b>Request Parameters:</b> name, email, password</p>
<p><b>Request Body:</b></p>

```php
{
    "name": "Michael",
    "email": "test+17@gmail.com",
    "password": "password",
    "password_confirmation": "password"
}
```

<p><b>Response Status Code:</b> 200</p>
<p><b>Response Body:</b></p>

```php
{
    "data": {
        "type": "tokens",
        "attributes": {
            "access_token": "[JWT_TOKEN]",
            "refresh_token": "[JWT_TOKEN]"
        },
        "relationships": {
            "user": {
                "data": {
                    "id": "7",
                    "type": "users"
                }
            }
        }
    },
    "included": [
        {
            "id": "7",
            "type": "users",
            "attributes": {
                "name": "Michael",
                "email": "1678543442@gmail.com",
                "role": "manager"
            }
        }
    ],
    "meta": {
        "http_code": "200"
    }
}
```

<br>

---

<h3>Endpoint: Login</h3>
<p><b>Request URL:</b> /api/v1/auth/login</p>
<p><b>Request method:</b> POST</p>
<p><b>Request Parameters:</b> email, password</p>
<p><b>Request Body:</b></p>

```php
{
    "email": "test@gmail.com",
    "password": "password"
}
```

<p><b>Response Status Code:</b> 200</p>
<p><b>Response Body:</b></p>

```php
{
    "data": {
        "type": "tokens",
        "attributes": {
            "access_token": "[JWT_TOKEN]",
            "refresh_token": "[JWT_TOKEN]"
        },
        "relationships": {
            "user": {
                "data": {
                    "id": "7",
                    "type": "users"
                }
            }
        }
    },
    "included": [
        {
            "id": "7",
            "type": "users",
            "attributes": {
                "name": "Michael",
                "email": "1678543442@gmail.com",
                "role": "manager"
            }
        }
    ],
    "meta": {
        "http_code": "200"
    }
}
```

<br>

---

<h3>Endpoint: Generate new access_token without authenticating</h3>
<p><b>Request URL:</b> /api/v1/auth/oauth/token</p>
<p><b>Request method:</b> POST</p>
<p><b>Request Parameters:</b> refresh_token</p>
<p><b>Request Body:</b></p>

```php
{
    "refresh_token": "[Refresh Token]"
}
```

<p><b>Response Status Code:</b> 200</p>
<p><b>Response Body:</b></p>

```php
{
    "data": {
        "type": "tokens",
        "attributes": {
            "access_token": "[JWT_TOKEN]",
            "refresh_token": "[JWT_TOKEN]"
        },
        "relationships": {
            "user": {
                "data": {
                    "id": "7",
                    "type": "users"
                }
            }
        }
    },
    "included": [
        {
            "id": "7",
            "type": "users",
            "attributes": {
                "name": "Michael",
                "email": "1678543442@gmail.com",
                "role": "manager"
            }
        }
    ],
    "meta": {
        "http_code": "200"
    }
}
```

<br>

---

<h3>Endpoint: Logout by revoking access and refresh tokens</h3>
<p><b>Request URL:</b> /api/v1/auth/logout</p>
<p><b>Request method:</b> POST</p>
<p><b>Request Parameters:</b> None</p>
<p><b>Request Body:</b> None</p>
<p><b>Response Status Code:</b> 200</p>
<p><b>Response Body:</b></p>

```php
{
    "data": {
        "type": "messages",
        "attributes": {
            "message": "You have been successfully logged out."
        }
    },
    "meta": {
        "http_code": "200"
    }
}
```

<br>

---

<h3>Endpoint: Fetch list of all users</h3>
<p><b>Request URL:</b> /api/v1/auth/users</p>
<p><b>Request method:</b> GET</p>
<p><b>Request Parameters:</b> None</p>
<p><b>Request Body:</b> None</p>
<p><b>Response Status Code:</b> 200</p>
<p><b>Response Body:</b></p>

```php
{
    "data": {
        "type": "users",
        "attributes": [
            {
                "id": "1",
                "type": "users",
                "attributes": {
                    "name": "Michael",
                    "email": "1683277837@gmail.com",
                    "role": "operator"
                }
            },
            {
                "id": "2",
                "type": "users",
                "attributes": {
                    "name": "Michael",
                    "email": "1683277900@gmail.com",
                    "role": "operator"
                }
            },
            {
                "id": "3",
                "type": "users",
                "attributes": {
                    "name": "Michael",
                    "email": "1683278201@gmail.com",
                    "role": "operator"
                }
            }
        ],
        "relationships": {
            "user": {
                "data": {
                    "id": "3",
                    "type": "users"
                }
            }
        }
    },
    "meta": {
        "http_code": "200"
    }
}
```