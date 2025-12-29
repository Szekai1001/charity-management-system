<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $fillable = ['application_id','type', 'file_name', 'file_type', 'file_path'];

    public function application()
    {
        return $this->belongsTo(Application::class);
    }
}
