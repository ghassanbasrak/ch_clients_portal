<?php

namespace App\Controllers;
use \App\Libraries\CustomOauth2;
use App\Models\UserModel;
use \OAuth2\Request;
use CodeIgniter\API\ResponseTrait;

class User extends BaseController
{
    use ResponseTrait;

    public function index()
    {
        if($this->request->getMethod() != 'get')
        {
            return $this->fail('Only get request is allowed');
        }
        $model = new UserModel();
        $users = $model->select([
            'id', 'email', 'full_name', 'phone_number', 'address', 'role'
        ])->findAll();
        return $this->respond($users);
    }

    public function login()
    {
        if($this->request->getMethod() != 'post')
        {
            return $this->fail('Only post request is allowed');
        }

        helper(['form']);
        $rules = [
            'email' => 'required|valid_email',
            'password' => 'required|min_length[8]'
        ];


        if(!$this->validate($rules))
        {
            return $this->fail($this->validator->getErrors());
        }else
        {
            $db = db_connect();
            $builder = $db->table('users');
            $where = ['email' => $this->request->getVar('email')];
            $builder->where($where);
            $user = $builder->get()->getRowArray();
            if(empty($user)){
                return $this->fail(['message' => 'The given data was invalid']);
            }
            $user = array_merge(array(
                'user_id' => $user['id']
            ), $user);
            if($user['password'] == $this->request->getVar('password'))
                return $this->respond($user);
            return $this->fail(['message' => 'The given data was invalid']);
        }

    }

    public function updateUser($id = null)
    {
        if($id == null)
        {
            return $this->fail('User ID not found!');
        }
        helper(['form']);
        $rules = [
            'email' => 'required|valid_email|is_unique[users.email,id,'.$id.'}]',
            'password' => 'required|min_length[8]',
            'full_name' => 'required|min_length[8]',
            'role' => 'required'
        ];
        if(!$this->validate($rules))
        {
            return $this->fail($this->validator->getErrors());
        }else
        {
            //["full_name", "phone_number", "address", "email", "password"];
            $request = $this->request->getRawInput();
            $data = [
                'id' => $id,
                'email' => $request['email'],
                'full_name' => $request['full_name'],
                'address' => isset($request['address']) ? $request['address'] : null,
                'phone_number' => isset($request['phone_number']) ? $request['phone_number'] : null,
                'role' => $request['role'],
                'password' => $request['password']
            ];
            $model = new UserModel();
            $model->save($data);
            unset($data['password']);
            return $this->respond($data);
        }

    }


    public function show($id = null)
    {
        $userModel = new UserModel();
        $data = $userModel->find($id);
        return $this->respond($data);
    }

    public function clientLogin()
   {
        if($this->request->getMethod() != 'post')
        {
            return $this->fail('Only post request is allowed');
        }

        helper(['form']);
        $rules = [
            'email' => 'required|valid_email',
            'password' => 'required|min_length[8]'
        ];


        if(!$this->validate($rules))
        {
            return $this->fail($this->validator->getErrors());
        }else
        {
            $db = db_connect();
            $builder = $db->table('clients');
            $where = ['email' => $this->request->getVar('email')];
            $builder->where($where);
            $user = $builder->get()->getRowArray();
            if(empty($user)){
                return $this->fail(['message' => 'The given data was invalid']);
            }
            $user = array_merge(array(
                'user_id' => $user['id']
            ), $user);
            if($user['password'] == $this->request->getVar('password'))
                return $this->respond($user);
            return $this->fail(['message' => 'The given data was invalid']);
        }

    }

    public function generateToken()
    {
        $oauth2 = new CustomOauth2();
        $request = new Request();
        $respond = $oauth2->server->handleTokenRequest($request->createFromGlobals());
        $code = $respond->getStatusCode();
        $body = $respond->getResponseBody();
        return $this->respond(json_decode($body), $code);

    }

    public function register()
    {

        helper(['form']);

        if($this->request->getMethod() != 'post')
        {
            return $this->fail('Only post request is allowed');
        }

        $rules = [
            'email' => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[8]',
            'full_name' => 'required|min_length[8]',
            'role' => 'required'
        ];


        if(!$this->validate($rules))
        {
            return $this->fail($this->validator->getErrors());
        }else
        {
            $model = new UserModel();
            $data = [
                'email' => $this->request->getVar('email'),
                'password' => $this->request->getVar('password'),
                'full_name' => $this->request->getVar('full_name'),
                'phone_number' => $this->request->getVar('phone_number'),
                'role' => $this->request->getVar('role'),
                'address' => $this->request->getVar('address')
            ];
            $data['id'] = $model->insert($data);
            unset($data['password']);
            return $this->respondCreated($data);
        }
    }
}