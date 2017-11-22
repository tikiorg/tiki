
$(document).ready(function(){/* affix the navbar after scroll below header */
$('#topbar').affix({
      offset: {
        top: $('#page-header').height()-$('#topbar').height()
      }
});	

/* highlight the top nav as scrolling occurs */
$('body').scrollspy({ target: '#topbar' })

/* smooth scrolling for scroll to top */
$('.scroll-top').click(function(){
  $('body,html').animate({scrollTop:0},1000);
})

/* smooth scrolling for nav sections */
$('#topbar .navbar-nav li>a').click(function(){
  var link = $(this).attr('href');
  var posi = $(link).offset().top+20;
  $('body,html').animate({scrollTop:posi},700);
})

    /* smooth scrolling for nav sections */
    $('#autotoc .nav li>a').click(function(){
        var link = $(this).attr('href');
        var posi = $(link).offset().top-50;
        $('body,html').animate({scrollTop:posi},700);
    })




});