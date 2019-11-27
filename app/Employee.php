<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $table = 'employees';
    
    public $timestamps = false;
    
    public function EmployeesCompanies() {
        return $this->hasOne('App\User','id','company_id');
    }
}
