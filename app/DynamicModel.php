<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

trait BindsDynamically
{
    protected $connection = null;
    protected $table = null;

    public function bind(string $connection, string $table)
    {
       $this->setConnection($connection);
       $this->setTable($table);
    }

    public function newInstance($attributes = [], $exists = false)
    {
       // Overridden in order to allow for late table binding.

       $model = parent::newInstance($attributes, $exists);
       $model->setTable($this->table);

       return $model;
    }

}

class DynamicModel extends Model
{
    use BindsDynamically;
    
}
