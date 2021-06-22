<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = "users";
    protected $primaryKey = "id";
    protected $allowedFields = ["full_name", "role", "phone_number", "address", "email", "password"];

    protected $beforeInsert = ['BeforeInsert'];
    protected $beforeUpdate = ['BeforeUpdate'];

    protected function BeforeInsert(array $data)
    {
        // $data = $this->passwordHash($data);
        return $data;
    }

    protected function BeforeUpdate(array $data)
    {
        // $data = $this->passwordHash($data);
        return $data;
    }

    protected function passwordHash(array $data)
    {
        if(isset($data['data']['password']))
        {
            $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
        }
        return $data;
    }
}