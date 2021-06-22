<?php

namespace App\Controllers;

use App\Models\ClientDatabaseModel;
use App\Models\InvoiceModel;
use CodeIgniter\RESTful\ResourceController;

class Client extends ResourceController
{
    protected $modelName = "App\Models\ClientModel";
    protected $format = "json";

    public function index()
    {
        $clients = $this->model->select([
            'id', 'email', 'full_name', 'phone_number', 'address'
        ])->findAll();
        return $this->respond($clients);
    }
    public function create()
    {
        helper(['form']);
        $rules = [
            'email' => 'required|valid_email|is_unique[clients.email]',
            'password' => 'required|min_length[8]',
            'full_name' => 'required|min_length[8]',
            'company_name' => 'required',
            'address' => 'required',
            'phone_number' => 'required',
        ];
        if(!$this->validate($rules))
        {
            return $this->fail($this->validator->getErrors());
        }else
        {
            $data = [
                'email' => $this->request->getVar('email'),
                'password' => $this->request->getVar('password'),
                'full_name' => $this->request->getVar('full_name'),
                'phone_number' => $this->request->getVar('phone_number'),
                'address' => $this->request->getVar('address'),
                'company_name' => $this->request->getVar('company_name')
            ];
            $data['id'] = $this->model->insert($data);
            $client_database = [
                'client_id' => $data['id'],
                'server_name' => $this->request->getVar('server_name'),
                'database_name' => $this->request->getVar('database_name'),
                'database_username' => $this->request->getVar('database_username'),
                'database_password' => $this->request->getVar('database_password')
            ];
            $this->link_database($data['id'], $client_database);

            return $this->respondCreated($data);
        }
    }
    public function show($id = null)
    {
        $data = $this->model->find($id);

        $database_model = new ClientDatabaseModel();
        $data['client_database'] = $database_model->where('client_id', $id)->get()->getRowArray();
        return $this->respond($data);
    }
    public function update($id = null)
    {
        helper(['form']);
        $rules = [
            'email' => 'required|valid_email|is_unique[clients.email,id,'.$id.']',
            'password' => 'required|min_length[8]',
            'full_name' => 'required|min_length[8]',
            'company_name' => 'required',
            'address' => 'required',
            'phone_number' => 'required',
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
                'password' => $request['password']
            ];
            $this->model->save($data);


            $client_database = [
                'client_id' => $id,
                'server_name' => isset($request['server_name']) ? $request['server_name'] : '',
                'database_name' => isset($request['database_name']) ? $request['database_name'] : '',
                'database_username' => isset($request['database_username']) ? $request['database_username'] : '',
                'database_password' => isset($request['database_password']) ? $request['database_password'] : '',
            ];
            $this->link_database($data['id'], $client_database);

            return $this->respond($data);
        }

    }

    public function update_database($client_id)
    {
        $request = $this->request->getRawInput();
        $client_database = [
            'client_id' => $client_id,
            'server_name' => isset($request['server_name']) ? $request['server_name'] : '',
            'database_name' => isset($request['database_name']) ? $request['database_name'] : '',
            'database_username' => isset($request['database_username']) ? $request['database_username'] : '',
            'database_password' => isset($request['database_password']) ? $request['database_password'] : '',
        ];
        $this->link_database($client_id, $client_database);
        return $this->respond($client_database);
    }

    public function link_database($client_id, $data)
    {
        $database_model = new ClientDatabaseModel();



        if($database_model->where('client_id', $client_id)->get()->getRowArray() != null)
        {
            $database_model->update($client_id, $data);
        }
        else{
            $database_model->insert($data);
        }


    }
    public function invoices($client_id)
    {
        $invoice_model = new InvoiceModel();
        $invoices = $invoice_model->where('client_id', $client_id)->findAll();
        return $this->respond($invoices);
    }
}