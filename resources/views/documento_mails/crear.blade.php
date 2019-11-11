<p>
  Se ha creado un nuevo documento: 
  <a href="{{ action("DocumentosController@ver", $documento) }}">
    {{ $documento->folio }}
  </a>, 
  con título "{{ $documento->titulo }}" en el departamento
  {{ $documento->departamento->nombre }}.
</p>

<h5>Descripción:</h5>
<p>{{ $documento->descripcion }}</p>
