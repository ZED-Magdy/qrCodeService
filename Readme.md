# Zatca Qr code microservice
### Best choice if you have multiple projects that requires zatca qr code
created using [SlimPhp](https://www.slimframework.com) , [ SallaApp /
ZATCA ](https://github.com/SallaApp/ZATCA) and [Symfony Validator](https://github.com/symfony/validator)
### Installing and using
```shell
composer install
```
```shell
php -S localhost:8000 -t .
```
this service requires 5 parameters in the request
```http request
POST / HTTP/1.1
Content-Type: application/json
Accept: application/json # or text/html for qr code rendered image
Request-Body: 
{
    "SellerName": "test seller",
    "taxRecord": "123456cas",
    "bookingDate": "2021-12-31 04:31 PM",
    "total": 55.25,
    "vat": 7.20
}
```
### Deploy
##### Copy project folder into your host, do the installation as shown, and you are done