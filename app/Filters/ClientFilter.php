<?php

namespace App\Filters;

use CodeIgniter\Config\Services;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;


class ClientFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $userFilter = new UserFilter();
        if($userFilter->isStaffLoggedIn($request))
            return true;

        if($request->getHeaderLine('Client-Authentication'))
        {
            $db = db_connect();
            $builder = $db->table('clients');
            $where = ['id' => $request->getHeaderLine('Client-Authentication')];
            $builder->where($where);
            $user = $builder->get()->getRowArray();
            if(!empty($user)){
                return true;
            }
        }
        $response = Services::response();
        $response->setStatusCode(401);
        $response->setHeader("WWW-Authenticate", "Bearer Token");
        $response->setHeader("Content-Type", "application/json");
        $response->setBody("{\"error\": \"Unauthenticated\"}");
        return $response;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do something here
    }
}