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
$api = new \Admitad\Api\Api()
$response = $api->authorizeByPassword($clientId, $clientPassword, $scope, $username, $password);
$result = $api->getArrayResultFromResponse($response); // or $response->getArrayResult();
```
* OAuth2

```php
// 1 step - get oauth authorization url
$api = new \Admitad\Api\Api();
$authorizeUrl = $api->getAuthorizeUrl($clientId, $redirectUri, $scope);
// redirect user to authorizeUrl


// 2 step - request access token by OAuth2 code returned from authorization url
$response = $api->requestAccessToken($clientId, $clientSecret, $code, $redirectUri);
$result = $api->getArrayResultFromResponse($response);
```
* Signed Request (for applications on apps.admitad.com)

```php
$api = new \Admitad\Api\Api();
$data = $api->parseSignedRequest($signedRequest, $clientSecret);
// this method throws Admitad\Api\Exception\InvalidSignedRequestException when $signedRequest is invalid
```

#### Refresh token

```php
$response = $api->refreshToken($clientId, $clientSecret, $refreshToken);
$result = $api->getArrayResultFromResponse($response);
```

#### Methods
There are 2 common methods to communicate with api:

```php
$api = new \Admitad\Api\Api($accessToken);

$api->get($path, $params);
$api->post($path, $params);

//for example
$response = $api->get('/advcampaigns/', array(
    'limit' => 20,
    'offset' => 0
));

$result = $api->getArrayResultFromResponse($response);

```

Paginated-result methods can be iterated in this way (instead of manually call methods with different offsets)

```php
$iterator = $api->getIterator('/advcampaigns/', array(
    'order_by' => 'id'
));

foreach ($iterator as $campaign) {
    // do smth with campaign
}
```
