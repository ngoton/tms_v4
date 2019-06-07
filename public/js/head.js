$(function($){

      var scrollbar = $('<div id="fixed-scrollbar"><div></div></div>').appendTo($(document.body));

      scrollbar.hide().css({

          overflowX:'auto',

          position:'fixed',

          width:'100%',

          bottom:0

      });

      var fakecontent = scrollbar.find('div');

      

      function top(e) {

          return e.offset().top;

      }



      function bottom(e) {

          return e.offset().top + e.height();

      }

      

      var active = $([]);

      function find_active() {

          scrollbar.show();

          var active = $([]);

          $('.fixed-scrollbar').each(function() {

              if (top($(this)) < top(scrollbar) && bottom($(this)) > bottom(scrollbar)) {

                  fakecontent.width($(this).get(0).scrollWidth);

                  fakecontent.height(1);

                  active = $(this);

              }

          });

          fit(active);

          return active;

      }

      

      function fit(active) {

          if (!active.length) return scrollbar.hide();

          scrollbar.css({left: active.offset().left, width:active.width()});

          fakecontent.width($(this).get(0).scrollWidth);

          fakecontent.height(1);

          delete lastScroll;

      }

      

      function onscroll(){

          var oldactive = active;

          active = find_active();

          if (oldactive.not(active).length) {

              oldactive.unbind('scroll', update);

          }

          if (active.not(oldactive).length) {

              active.scroll(update);

          }

          update();

      }

      

      var lastScroll;

      function scroll() {

          if (!active.length) return;

          if (scrollbar.scrollLeft() === lastScroll) return;

          lastScroll = scrollbar.scrollLeft();

          active.scrollLeft(lastScroll);

      }

      

      function update() {

          if (!active.length) return;

          if (active.scrollLeft() === lastScroll) return;

          lastScroll = active.scrollLeft();

          scrollbar.scrollLeft(lastScroll);

      }

      

      scrollbar.scroll(scroll);

      

      onscroll();

      $(window).scroll(onscroll);

      $(window).resize(onscroll);

  });



    $(function () {

      var w = $("#container").width();



      $(".container").width(w);

      $(".navbar-inner").width(w);

      $(".navbar").width(w);

      $("#container").width(w);

      $(".fix_img").width(w);

      $(".footer-nav").width(w);

      $("footer").width(w);



        setNavigation();

        $('.home').click(function(){



          var hrefval = $(this).attr("data");

          

              if(hrefval != "#") {

                closeSidepage('.slide-content');

                openSidepage(hrefval);



                $('.nav li').removeClass('active');



                $(this).closest('li').addClass('active');

                $(this).closest('li').parents('li').addClass('active');



                $("html, body").animate({ scrollTop: 0 }, 1);

              }



        });



        

        $('.png').click(function(){

          $('#support').animate({

            height: '300px'

          }, 300);

        });

        $('.close').click(function(){

          $('#support').animate({

            height: '22px'

          }, 300);

        });





      

    });





      function openSidepage(id) {

        $(id).css({display: "block"});

        $(id).animate({

          left: '0px'

        }, 500, 'easeOutBack'); 

      }

      

      function closeSidepage(id){

        

        $(id).animate({

          left: '-1500px'

        }, 100, 'easeOutQuint');  

        $(id).css({left: "-1500px",display: "none"});

      }



    function setNavigation() {

        var path = window.location.pathname;

        path = path.replace(/\/$/, "");

        //path = path.replace(/^\/([^\/]*).*$/, '/$1');

        path = decodeURIComponent(path);



        $(".nav a").each(function () {

            var href = "/"+$(this).attr('data');



            if (path === href) {

                $(this).closest('li').addClass('active');

                $(this).closest('li').parents('li').addClass('active');



            }

            if (window.location.hash == $(this).attr("data")) {

              var hrefval = $(this).attr("data");

          

              if(hrefval != "#") {

                closeSidepage('.slide-content');

                openSidepage(hrefval);



                $(this).closest('li').addClass('active');

                $(this).closest('li').parents('li').addClass('active');



                $("html, body").animate({ scrollTop: 0 }, 1);

              }

            }

            

        });



        $(".sidebar-menu a").each(function () {

            var href = "/"+$(this).attr('data');



            if (path === href) {

                $(this).closest('li').addClass('active');

                $(this).closest('li').parents('li').addClass('active');



            }

            if (window.location.hash == $(this).attr("data")) {

              var hrefval = $(this).attr("data");

          

              if(hrefval != "#") {

                closeSidepage('.slide-content');

                openSidepage(hrefval);



                $(this).closest('li').addClass('active');

                $(this).closest('li').parents('li').addClass('active');



                $("html, body").animate({ scrollTop: 0 }, 1);

              }

            }

            

        });



        if (path == "" && window.location.hash == "") {

            closeSidepage('.slide-content');

            openSidepage('#index');



            $('li').first().addClass('active');



        }

    }





    /*$(function(){

      $(".table_data").colResizable({

        liveDrag:true,

        gripInnerHtml:"<div class='grip'></div>", 

        draggingClass:"dragging"

      });

    });*/



    /*var refreshId = setInterval(function(){  

    $.ajax({

      type: "POST",

      url: "<?php echo BASE_URL ?>/admin/notification",

      data: 'data=1', // or any data you want to send

      cache: false,

      success: function(html){

       if (html != parseInt(html, 10)) {

          html = 0;

        }

      $('#nfc_count').text(html).fadeIn('fast'); // response from server side process

      }

    });

    },10000);*/



   



    $(document).ready(function () {

            $("#btnExport").click(function () {

                $("#tblExport").battatech_excelexport({

                    containerid: "tblExport"

                   , datatype: 'table'

                });

            });

        });



    /*function flashtext(ele, col) {

            var tmpColCheck = document.getElementById(ele).style.color;



            if (tmpColCheck === 'white') {

                document.getElementById(ele).style.color = col;

            } else {

                document.getElementById(ele).style.color = 'white';

            }

        }



        setInterval(function () {

            flashtext('nfc_count', 'red');

        }, 500); //set an interval timer up to repeat the function



    $(document).ready(function(){

          $(':input').live('focus',function(){

              $(this).attr('autocomplete', 'off');

          });

      });*/

    

    $(document).ready(function () {
      var h = $('.table_data').height();
      if (h > 150) {
        $("#sl_vehicle").chosen();
        $("#sl_nv").chosen();
        $("#sl_status").chosen();
      }

    });
