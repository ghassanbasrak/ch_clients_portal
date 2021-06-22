<?php

namespace App\Filters;

use CodeIgniter\Config\Services;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;


class ManagerFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if($request->getHeaderLine('Client-Authentication')){
            return true;
        }
        if($request->getHeaderLine('Authentication'))
        {
            $db = db_connect();
            $builder = $db->table('users');
            $where = [
                'id' => $request->getHeaderLine('Authentication'),
            ];
            $builder->where($where);
            $user = $builder->get()->getRowArray();
            if(!empty($user))
                if($user['role'] == 'manager')
                {
                    return true;
                }
        }
        $response = Services::response();
        $response->setStatusCode(401);
        $response->setHeader("WWW-Authenticate", "Bearer Token");
        $response->setHeader("Content-Type", "application/json");
        $response->setBody("{\"error\": \"Method not allowed\"}");
        $response->send();
        die();
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do something here
    }
}