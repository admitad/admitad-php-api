admitad-api
==================

A PHP wrapper around the Admitad API

Install
-------

Install http://getcomposer.org/ and run the following command:

```
php composer.phar require admitad/api dev-master
```

Examples
-------

#### Request access token

* By username / password

```php
$api = new Admitad\Api\Api()
$response = $api->authorizeByPassword($clientId, $clientPassword, $scope, $username, $password);
$result = $response->getResult(); // or $response->getArrayResult();
```
* OAuth2

```php
// 1 step - get oauth authorization url
$api = new Admitad\Api\Api();
$response = $api->getAuthorizeUrl($clientId, $redirectUri, $scope);
$accessToken = $response->getResult();

// 2 step - request access token by OAuth2 code returned from authorization url
$response = $api->requestAccessToken($clientId, $clientSecret, $code, $redirectUri);
$result = $response->getResult();
```
* Signed Request (for applications on apps.admitad.com)

```php
$api = new Admitad\Api\Api();
$data = $api->parseSignedRequest($signedRequest, $clientSecret);
// this method throws Admitad\Api\Exception\InvalidSignedRequestException when $signedRequest is invalid
```

#### Refresh token

```php
$result = $api->refreshToken($clientId, $clientSecret, $refreshToken)->getResult();
```

#### Methods
There are 2 common methods to communicate with api:
```php
$api = new Admitad\Api\Api($accessToken);

$api->get($path, $params);
$api->post($path, $params);
```

For some api methods library contains shorthand functions, for example
```php
$api->me(); //equivalent for $api->get('/me/');
```
