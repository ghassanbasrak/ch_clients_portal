<?php

namespace App\Models;

use CodeIgniter\Model;

class ClientModel extends Model
{
    protected $table = "clients";
    protected $primaryKey = "id";
    protected $allowedFields = ["full_name", "phone_number", "address", "email", "password", 'type', "company_name"];
}