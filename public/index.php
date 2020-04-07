<?php
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
$app->get('/users', function (Request $request, Response $response, $args) use ($json_db) {

    $users = $json_db->select( '*' )
        ->from( 'users.json' )
        ->get();
    $response->getBody()->write(json_encode($users,JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    return $response
        ->withHeader('Content-Type', 'application/json');
});

//отобразить запись по ID
$app->get('/users/{id}', function (Request $request, Response $response, $args) use ($json_db) {

    $users = $json_db->select( '*'  )
        ->from( 'users.json' )
        ->where( [ 'id' => $request->getAttribute('id') ] )
        ->get();

    if(!empty($users)) {
        $response->getBody()->write(json_encode([
            "data" => [
                "id" => $users[0]["id"],
                "name" => $users[0]["name"],
                "phome" => $users[0]["phone"]
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
$app->post('/users', function (Request $request, Response $response, $args) use ($json_db) {
    $requestBody = $request->getParsedBody();
    $users = $json_db->select( '*'  )
        ->from( 'users.json' )
        ->where( [ 'id' => $requestBody['id']
            ] )
        ->get();

    if(empty($users)) {
        $json_db->insert( 'users.json',
            [
                'id' => $requestBody['id'],
                "name" => $requestBody['name'],
                "phone" => $requestBody['phone']
            ]
        );
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
    $requestBody = $request->getParsedBody();
    $id = $request->getAttribute('id');

    $users = $json_db->select( '*'  )
        ->from( 'users.json' )
        ->where( [ 'id' => $id
        ])
        ->get();

    if(!empty($users)) {
        $json_db->update( [ 'name' => $requestBody['name'], 'phone' => $requestBody['phone'] ] )
            ->from( 'users.json' )
            ->where( [ 'id' =>  $id] )
            ->trigger();

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
            "message" => "В базе не существует запись с ID=".$requestBody['id']
        ],JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    return $response
        ->withHeader('Content-Type', 'application/json');

});

//удалить запись по ID
$app->delete("/users/{id}", function (Request $request, Response $response, $args) use ($json_db) {
    $id = $request->getAttribute('id');
    $users = $json_db->select( '*'  )
        ->from( 'users.json' )
        ->where( [ 'id' => $id
        ])
        ->get();

    if(!empty($users)) {
        $json_db->delete()
            ->from( 'users.json' )
            ->where( [ 'id' => $id ] )
            ->trigger();

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