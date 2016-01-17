<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $primaryKey = 'task_id';
    protected $table = 'tasks';
    protected $guarded = array('id','remember_token','created_at','updated_at');
    public function user()
    {
        return $this->belongsTo('App\User','user_id','user');

    }
}
