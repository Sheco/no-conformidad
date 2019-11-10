<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DocumentoLog extends Model
{
    protected $table = "documento_logs";

    public function documento() {
      return $this->belongsTo('App\Documento');
    }
}
