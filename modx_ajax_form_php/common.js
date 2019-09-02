$(document).ready(function(){


  $("#navbarNav").on("click","a[href^='#']", function (event) {
    //отменяем стандартную обработку нажатия по ссылке
    event.preventDefault();
    //забираем идентификатор бока с атрибута href
    var id  = $(this).attr('href'),
    //узнаем высоту от начала страницы до блока на который ссылается якорь
      //top = $(id).offset().top - $('.slide__top').height();
      top = $(id).offset().top;
    //анимируем переход на расстояние - top за 1500 мс

    $('body,html').animate({scrollTop: top}, 1500);
    $('.navbar-toggler').click();


  });


  AOS.init({
    easing: 'ease-out-back',
    duration: 1000,
    disable: 'mobile'
  });
// $('.navbar-toggler').on('click', function({
//   console.log('hhghghg')
// }))

// $('.servis_col').click(function(){
//   $('.modal').removeClass('active');
// $(this).find('.servis_inner_text').next('.modal').addClass('active');
// })
$('.owl-carousel').owlCarousel({
  loop:true,
  margin:10,
  nav:true,
  items:1,
  dots:true,
  nav:true,
  autoplay:true,
  smartSpeed:1500,
  autoplayTimeout:5000

});


  function serializefiles(obj) {
      var formData = new FormData();

      var params = $(obj).serializeArray();
      $.each(params, function (i, val) {
          formData.append(val.name, val.value);
      });

      var file = obj.find('input[type=file]')[0];

      if (file && file.files.length) {
        formData.append('file', file.files[0]); 
      }

      return formData;
  }

  $('.ajax_form').submit(function(e){
    e.preventDefault();
    var form = $(this);
    //if (checkErrors(form)) return false;
    //var data = form.serialize();
    var data = serializefiles(form);
    var button = form.find('button');
    var oldtext = button.text();
    var action = form.attr('action');
    var response_div = form.find('.response');
    response_div.html('');
    $.ajax({
      type: 'POST', 
      contentType: false,
      processData: false,  
      url: action,
      dataType: 'json',
      data: data,   
      beforeSend: function(data) {
        button.attr('disabled', true);
        button.text('Отправляем..');
      },
      success: function(data){
        console.log(arguments);
        if (data.error.length) {
          response_div.html('<div class="alert alert-danger" role="alert">' + data.error.join('<br>') + '</div>');
        } else {
          form.fadeOut(500, function(){
            form.html('<div class="alert alert-success" role="alert">Заявка отправлена.</div>').fadeIn(500);
          });
        }
      },
      complete: function(data) {
        console.log(arguments);
        button.prop('disabled', false);
        button.text(oldtext);
      }
    });
  });


});