<?php

namespace App\Models;

use CodeIgniter\Model;

class ClientDatabaseModel extends Model
{
    protected $table = "client_database";
    protected $primaryKey = "client_id";
    protected $allowedFields = ["id", "client_id", "server_name", "database_name", "database_username", "database_password"];
}