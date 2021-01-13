$(function(){
  $('.selectpicker').selectpicker({
    style: '',
    styleBase: 'form-control'
  });
});

$.i18n.debug = true;
jQuery(function($) {
  $.i18n({
    locale: 'en'
  }).load( {
    'en': './assets/js/i18n/en.json',
    'de': './assets/js/i18n/de.json'
  } ).done(function() {
    $('html').i18n();

    $('.switch-locale').on('click', 'a', function(e) {
        console.log('here')
        e.preventDefault();
        $.i18n().locale = $(this).data('locale');
        $('html').i18n();
      });
    });
});