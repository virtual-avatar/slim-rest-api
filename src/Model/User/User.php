<?php

namespace App\Model\User;


use App\Libs\DBConnection;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;


class User
{
    protected $json_db;

    public function __construct()
    {
        $this->json_db = DBConnection::getConnection();
    }

    public function userListAll() {
        $users = $this->json_db->select( '*' )
            ->from( 'users.json' )
            ->get();

        return $users;
    }

    public function findUserById(Request $request, Response $response) {
        $users = $this->json_db->select( '*'  )
            ->from( 'users.json' )
            ->where( [ 'id' => $request->getAttribute('id') ] )
            ->get();

        return $users;
    }

    public function addUser(Request $request, Response $response) {
        $requestBody = $request->getParsedBody();
        $users = $this->json_db->insert( 'users.json',
            [
                'id' => $requestBody['id'],
                "name" => $requestBody['name'],
                "phone" => $requestBody['phone']
            ]
        );

        return $users;
    }

    public function editUser(Request $request, Response $response) {
        $requestBody = $request->getParsedBody();
        $id = $request->getAttribute('id');

        $users = $this->json_db->update( [ 'name' => $requestBody['name'], 'phone' => $requestBody['phone'] ] )
            ->from( 'users.json' )
            ->where( [ 'id' =>  $id] )
            ->trigger();

        return $users;
    }

    public function delUser(Request $request, Response $response) {
        $id = $request->getAttribute('id');

        $users = $this->json_db->delete()
            ->from( 'users.json' )
            ->where( [ 'id' => $id ] )
            ->trigger();

        return $users;
    }
}