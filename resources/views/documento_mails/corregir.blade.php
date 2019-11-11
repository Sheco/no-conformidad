<p>
  El documento con folio
  <a href="{{ action("DocumentosController@ver", $documento) }}">
    {{ $documento->folio }}
  </a>  ha sido marcado como corregido, favor de verificar.
</p>
