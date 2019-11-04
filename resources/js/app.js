/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.guardarResponsable = function(field) {
  let form = $(field.form);
  let status = form.find('.status');
  if(status) status.html('Guardando...');
  $.post(form.attr('action'), form.serialize(), function(response) {
    if(status) status.html(response);
  }).fail(function() {
    if(status) status.html('Error');
  });;
}
