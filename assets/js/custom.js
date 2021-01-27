const lang = localStorage.getItem('lang') || 'en';

if (!localStorage.getItem('accept-cookies')) {
  console.log('here')
  setTimeout(function(){
    $('.cookie-consent-banner').removeClass('d-none');
  }, 1000);

  $('.cookie-consent-banner-ack').on('click', function(){
    localStorage.setItem('accept-cookies', true);
    $('.cookie-consent-banner').addClass('d-none');
  });
}

$(function(){
  $('.selectpicker').selectpicker({
    style: '',
    styleBase: 'form-control'
  });
});

$('.selectpicker').val(lang);

$.i18n.debug = true;
$(function($) {
  $.i18n({
    locale: lang
  }).load( {
    'en': './assets/js/i18n/en.json',
    'de': './assets/js/i18n/de.json'
  } ).done(function() {
    $('html').i18n();
    $('.selectpicker').on('change', function(e){
      $('.selectpicker').selectpicker('refresh');
      $.i18n().locale = this.value;
      localStorage.setItem('lang', this.value);
      $('html').i18n();
    });
  });
});