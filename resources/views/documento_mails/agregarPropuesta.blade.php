<p>
  El responsable del documento
  <a href="{{ action("DocumentosController@ver", $documento) }}">
    {{ $documento->folio }}
  </a> ha agregado una propuesta, favor de darle seguimiento.
</p>

<h5>Fecha de entrega propuesta:</h5>
{{ $propuesta->fecha_entrega->format('Y/M/d') }}

<h5>Descripción</h5>
<p>{{ $propuesta->descripcion }}</p>
