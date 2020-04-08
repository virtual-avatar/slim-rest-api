<?php

use App\Model\User\User;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Jajo\JSONDB;



require __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();
$app->addBodyParsingMiddleware();

$json_db = new JSONDB( __DIR__."/../src/database");

//дефолтный роут
$app->get('/', function (Request $request, Response $response, $args ) {
    $response->getBody()->write("Hello world!");
    return $response;
});

//Отобразить все записи из БД
$app->get('/users', function (Request $request, Response $response, $args) {

    $users = new User();
    $response->getBody()->write(json_encode($users->userListAll(),JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    return $response
        ->withHeader('Content-Type', 'application/json');
});

//отобразить запись по ID
$app->get('/users/{id}', function (Request $request, Response $response, $args)  {

    $users = new User();
    $findUser = $users->findUserById($request,$response);

    if(!empty($findUser)) {
        $response->getBody()->write(json_encode([
            "data" => [
                "id" => $findUser[0]["id"],
                "name" => $findUser[0]["name"],
                "phome" => $findUser[0]["phone"]
            ],
            "code" => 1,
            "message" => "OK"
        ],JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    } else {
        $response->getBody()->write(json_encode([
            "data" => [],
                "code" => 0,
                "message" => "Данные не обнаружены"
            ],JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }
    return $response
        ->withHeader('Content-Type', 'application/json');
});

//добавить запись в БД
$app->post('/users', function (Request $request, Response $response, $args) {
    $requestBody = $request->getParsedBody();

    $users = new User();
    $findUser = $users->findUserById($request,$response);

    if(empty($findUser)) {
        $users->addUser($request,$response);
        $response->getBody()->write(json_encode([
            "data" => [
                "id" => $requestBody['id'],
                "name" => $requestBody['name'],
                "phome" => $requestBody['phone']
            ],
            "code" => 1,
            "message" => "Данные успешно добавлены"
        ],JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    } else {
        $response->getBody()->write(json_encode([
            "data" => [
            ],
            "code" => 0,
            "message" => "В базе уже существует запись с ID=".$requestBody['id']
        ],JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    return $response
        ->withHeader('Content-Type', 'application/json');

});
//редактировать запись в БД по ID
$app->put("/users/{id}", function (Request $request, Response $response, $args) use ($json_db) {
    $id = $request->getAttribute('id');
    $users = new User();
    $findUser = $users->findUserById($request,$response);

    if(!empty($findUser)) {
        $users->editUser($request,$response);
        $response->getBody()->write(json_encode([
            "data" => [
            ],
            "code" => 0,
            "message" => "Данные успешно обновлены"
        ],JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    } else {
        $response->getBody()->write(json_encode([
            "data" => [
            ],
            "code" => 0,
            "message" => "В базе не существует запись с ID=".$id
        ],JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    return $response
        ->withHeader('Content-Type', 'application/json');

});

//удалить запись по ID
$app->delete("/users/{id}", function (Request $request, Response $response, $args) use ($json_db) {
    $id = $request->getAttribute('id');
    $users = new User();
    $findUser = $users->findUserById($request,$response);

    if(!empty($findUser)) {
        $users->delUser($request,$response);

        $response->getBody()->write(json_encode([
            "data" => [
            ],
            "code" => 0,
            "message" => "Данные успешно удалены"
        ],JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    } else {
        $response->getBody()->write(json_encode([
            "data" => [
            ],
            "code" => 0,
            "message" => "В базе не существует запись с ID=".$id
        ],JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    return $response
        ->withHeader('Content-Type', 'application/json');

});

$app->run();