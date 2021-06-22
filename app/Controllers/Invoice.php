<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;

class Invoice extends ResourceController
{
    protected $modelName = "App\Models\InvoiceModel";
    protected $format = "json";

    public function index()
    {
        $where = [];
        if($this->request->getHeaderLine('Client-Authentication'))
        {
            $where = ['invoices.client_id' => $this->request->getHeaderLine('Client-Authentication')];
        }
        $services = $this->model
            ->select(['client_id', 'invoices.id as invoice_id', 'full_name', 'email', 'address', 'sub_total', 'discount', 'type', 'total', 'invoices.created_at'])
            ->where($where)
            ->join('clients', 'invoices.client_id = clients.id')
            ->findAll();
        return $this->respond($services);
    }
    public function create()
    {

        helper(['form']);
        $rules = [
            'client_id' => 'required|decimal',
            'total' => 'required|decimal',
            'sub_total' => 'required|decimal',
            'discount' => 'required|decimal',
            'items.*.id' => 'required|decimal',
            'items.*.price' => 'required|decimal'
        ];
        if(!$this->validate($rules))
        {
            return $this->fail($this->validator->getErrors());
        }else
        {
            $data = [
                'client_id' => $this->request->getVar('client_id'),
                'total' => $this->request->getVar('total'),
                'sub_total' => $this->request->getVar('sub_total'),
                'discount' => $this->request->getVar('discount'),
                'type' => $this->request->getVar('type'),
            ];
            $data['id'] = $this->model->insert($data);
            $data['items'] = [];
            foreach ($this->request->getVar('items') as $item)
            {
                $db = db_connect();
                $item_data = [
                    'invoice_id' => $data['id'],
                    'service_id' => $item['id'],
                    'price' => $item['price'],
                ];
                $db->table('invoice_item')->insert($item_data);
                $data['items'][] = $item_data;
            }
            return $this->respondCreated($data);
        }
    }
    public function show($id = null)
    {

        $where = [];
        if($this->request->getHeaderLine('Client-Authentication'))
        {
            $where = ['invoices.client_id' => $this->request->getHeaderLine('Client-Authentication')];
        }
        $data = $this->model
            ->select(['client_id', 'invoices.id as invoice_id', 'full_name', 'email', 'address', 'phone_number', 'sub_total', 'discount', 'type', 'total', 'invoices.created_at'])
            ->where($where)
            ->join('clients', 'invoices.client_id = clients.id')
            ->find($id);
        if(empty($data)){
            return $this->failNotFound('Invoice not found');
        }
        $db = db_connect();
        $builder = $db->table('invoice_item');
        $where = ['invoice_id' => $id];
        $select = ['services.id as service_id', 'services.name', 'invoice_item.price', 'services.description'];
        $builder->select($select)->where($where)->join('services', 'invoice_item.service_id = services.id');
        $invoice_items = $builder->get()->getResultObject();
        $data['invoice_items'] = $invoice_items;

        return $this->respond($data);
    }

}