<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DocumentoArchivo extends Model
{
  protected $table = 'documento_archivos';

  function documento() {
    return $this->belongsTo('App\Documento');
  }

  function bajar() {
  }
}
