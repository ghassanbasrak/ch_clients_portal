<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;

class Service extends ResourceController
{
    protected $modelName = "App\Models\ServiceModel";
    protected $format = "json";

    public function index()
    {
        $services = $this->model->findAll();
        return $this->respond($services);
    }
    public function create()
    {
        helper(['form']);
        $rules = [
            'name' => 'required|min_length[4]',
            'price' => 'required|decimal'
        ];
        if(!$this->validate($rules))
        {
            return $this->fail($this->validator->getErrors());
        }else
        {
            $data = [
                'name' => $this->request->getVar('name'),
                'price' => $this->request->getVar('price'),
                'description' => $this->request->getVar('description'),
            ];
            $data['id'] = $this->model->insert($data);
            return $this->respondCreated($data);
        }
    }
    public function show($id = null)
    {
        $data = $this->model->find($id);
        return $this->respond($data);
    }
    public function update($id = null)
    {
        helper(['form']);
        $rules = [
            'name' => 'required|min_length[4]',
            'price' => 'required|decimal'
        ];
        if(!$this->validate($rules))
        {
            return $this->fail($this->validator->getErrors());
        }
        else
        {
            $request = $this->request->getRawInput();
            $data = [
                'id' => $id,
                'name' => $request['name'],
                'price' => $request['price'],
                'description' => isset($request['description']) ? $request['description'] : ''
            ];
            $this->model->save($data);
            return $this->respond($data);
        }

    }

}