<?php

namespace App\Models;

use CodeIgniter\Model;

class InvoiceModel extends Model
{
    protected $table = "invoices";
    protected $primaryKey = "id";
    protected $allowedFields = ["total", "sub_total", "discount", "price", "client_id", "type"];
}