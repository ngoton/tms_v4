$(function () {
    setNavigation();
});
$( document ).ajaxStart(function() {

  $("#ajax_loader").fadeIn(200);

  $('input[type="submit"]').attr('disabled',true);
  $('button[type="submit"]').attr('disabled',true);

});

$( document ).ajaxComplete(function() {

  $("#ajax_loader").fadeOut(200);

  $('input[type="submit"]').attr('disabled',false);
  $('button[type="submit"]').attr('disabled',false);

});

function setNavigation() {
    var path = window.location.pathname;
    path = path.replace(/\/$/, "");
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
            $(this).closest('li').addClass('active');
            $(this).closest('li').parents('li').addClass('active');
          }
        }
        
    });
}
function exportExcel(button, table) {
  $("#"+button).click(function () {
      $("#"+table).battatech_excelexport({
          containerid: table
         , datatype: 'table'
      });
  });
}
function checkall(class_name, obj) {
    var items = document.getElementsByClassName(class_name);
    if(obj.checked == true) 
    {
      for(i=0; i < items.length ; i++)
        items[i].checked = true;
    }
    else { 
      for(i=0; i < items.length ; i++)
        items[i].checked = false;
    }
}
function searchall(page,cot,sapxep){
  var page = 1;
  var cot = cot;
  var sapxep = sapxep;
  
  var faq_search_input = "";  // Lấy giá trị search của người dùng
  var loc =    "";
  var ngaytao = "";
  var ngaytaobatdau = "";
  var batdau = "";
  var ketthuc = "";
  var trangthai = "";
  var nv = "";
  var tha = "";
  var na = "";
  var tu = "";
  var den = "";
  var xe = "";
  var vong = "";

  if($('#search-input') != null)
  {
    var faq_search_input = $('#search-input').val();
  }
  if($('#chonloc') != null)
  {
    var loc = $('#chonloc').val();
  }
  if($('#chonngaytao') != null)
  {
    var ngaytao = $('#chonngaytao').val();
  }
  if($('#chonngaytaobatdau') != null)
  {
    var ngaytaobatdau = $('#chonngaytaobatdau').val();
  }
  if($('#batdau') != null)
  {
    var batdau = $('#batdau').val();
  }
  if($('#ketthuc') != null)
  {
    var ketthuc = $('#ketthuc').val();
  }
  if($('#sl_status') != null)
  {
    var trangthai = $('#sl_status').val();
  }
  if($('#sl_nv') != null)
  {
    var nv = $('#sl_nv').val();
  }
  if($('#sl_tha') != null)
  {
    var tha = $('#sl_tha').val();
  }
  if($('#sl_na') != null)
  {
    var na = $('#sl_na').val();
  }
  if($('#tu') != null)
  {
    var tu = $('#tu').val();
  }
  if($('#den') != null)
  {
    var den = $('#den').val();
  }
  if($('#sl_vehicle') != null)
  {
    var xe = $('#sl_vehicle').val();
  }
  if($('#sl_round') != null)
  {
    var vong = $('#sl_round').val();
  }

    var dataString = 'keyword='+ faq_search_input+"&limit="+loc+"&page="+ page +"&order_by="+ cot +"&order="+ sapxep+"&ngaytao="+ ngaytao+"&ngaytaobatdau="+ ngaytaobatdau+"&batdau="+ batdau+"&ketthuc="+ ketthuc+"&trangthai="+ trangthai+"&nv="+nv+"&tha="+tha+"&na="+na+"&tu="+tu+"&den="+den+"&xe="+xe+"&vong="+vong; 
    console.log(dataString);//$('#loading').html("<img src='public/images/loading.gif'/>").fadeIn(500);
  $.ajax({
            type: "POST",                            // Phương thức gọi là GET
            url: "#",                 // File xử lý
            data: dataString,                       // Dữ liệu truyền vào
            beforeSend:  function() {               // add class "loading" cho khung nhập
                $('input#search-input').addClass('loading');
            },
            success: function(server_response)      // Khi xử lý thành công sẽ chạy hàm này
            {
                $('body').html(server_response);    // Hiển thị dữ liệu vào thẻ div #searchresultdata
                
                //Enable sidebar toggle
                $(document).on('click', "[data-toggle='offcanvas']", function (e) {
                  e.preventDefault();

                  //Enable sidebar push menu
                  if ($(window).width() > (768 - 1)) {
                    if ($("body").hasClass('sidebar-collapse')) {
                      $("body").removeClass('sidebar-collapse').trigger('expanded.pushMenu');
                    } else {
                      $("body").addClass('sidebar-collapse').trigger('collapsed.pushMenu');
                    }
                  }
                  //Handle sidebar push menu for small screens
                  else {
                    if ($("body").hasClass('sidebar-open')) {
                      $("body").removeClass('sidebar-open').removeClass('sidebar-collapse').trigger('collapsed.pushMenu');
                    } else {
                      $("body").addClass('sidebar-open').trigger('expanded.pushMenu');
                    }
                  }
                });
                
                 
                if ($('input#search-input').hasClass("loading")) {      // Kiểm tra class "loading"
                    $("input#search-input").removeClass("loading");     // Remove class "loading"
                }
        //$('#loading').fadeOut(500); 
            }
        });
  
}
function sapxep(page,cot,sapxep){
          var sapxep        = sapxep;
        if(page==""){
          if($('.sort').attr('class') == "sort DESC"){
            $('.sort').removeClass('DESC');
            $('.sort').addClass('ASC');
            sapxep = "ASC";
          }
          else if($('.sort').attr('class') == "sort ASC"){
            $('.sort').removeClass('ASC');
            $('.sort').addClass('DESC');
            sapxep = "DESC";
          }
          else if($('.sort').attr('class') == "sort"){
            
            $('.sort').addClass('DESC');
            sapxep = "DESC";
          }
        }
          //$('#loading').html("<img src='public/images/loading.gif'/>").fadeIn(500);
          var cot        = cot;
          var keyword = $('#search-input').val();
          var ngaytao = "";
          var loc =    $('#chonloc').val();

          if($('#chonngaytao') != null)
          {
            var ngaytao = $('#chonngaytao').val();
          }
          if($('#chonngaytaobatdau') != null)
          {
            var ngaytaobatdau = $('#chonngaytaobatdau').val();
          }
          if($('#batdau') != null)
          {
            var batdau = $('#batdau').val();
          }
          if($('#ketthuc') != null)
          {
            var ketthuc = $('#ketthuc').val();
          }
          if($('#sl_status') != null)
          {
            var trangthai = $('#sl_status').val();
          }
          if($('#sl_nv') != null)
          {
            var nv = $('#sl_nv').val();
          }
          if($('#sl_tha') != null)
          {
            var tha = $('#sl_tha').val();
          }
          if($('#sl_na') != null)
          {
            var na = $('#sl_na').val();
          }
          if($('#tu') != null)
          {
            var tu = $('#tu').val();
          }
          if($('#den') != null)
          {
            var den = $('#den').val();
          }
          if($('#sl_vehicle') != null)
          {
            var xe = $('#sl_vehicle').val();
          }
          if($('#sl_round') != null)
          {
            var vong = $('#sl_round').val();
          }
          
          
          $.ajax({
            type: "POST", // phương thức gởi đi
            url: "#", // nơi mà dữ liệu sẽ chuyển đến khi submit
            data: "page="+ page +"&order_by="+ cot +"&order="+ sapxep+"&limit="+ loc+"&keyword="+ keyword+"&ngaytao="+ ngaytao+"&ngaytaobatdau="+ ngaytaobatdau+"&batdau="+ batdau+"&ketthuc="+ ketthuc+"&trangthai="+ trangthai+"&nv="+nv+"&tha="+tha+"&na="+na+"&tu="+tu+"&den="+den+"&xe="+xe+"&vong="+vong,
            success: function(answer){ // if everything goes well
              
              $('body').html(answer); // đặt kết quả trả về từ test.php vào thẻ div success
              //$('#loading').fadeOut(500);

              //Enable sidebar toggle
                $(document).on('click', "[data-toggle='offcanvas']", function (e) {
                  e.preventDefault();

                  //Enable sidebar push menu
                  if ($(window).width() > (768 - 1)) {
                    if ($("body").hasClass('sidebar-collapse')) {
                      $("body").removeClass('sidebar-collapse').trigger('expanded.pushMenu');
                    } else {
                      $("body").addClass('sidebar-collapse').trigger('collapsed.pushMenu');
                    }
                  }
                  //Handle sidebar push menu for small screens
                  else {
                    if ($("body").hasClass('sidebar-open')) {
                      $("body").removeClass('sidebar-open').removeClass('sidebar-collapse').trigger('collapsed.pushMenu');
                    } else {
                      $("body").addClass('sidebar-open').trigger('expanded.pushMenu');
                    }
                  }
                });
                
            }
          });
}
function del(id)
{
  if($('.add-field') != null)
  {
    $('.add-field').slideUp();
  }
  var r = confirm("Bạn có chắc chắn muốn xóa không?");
  if (r == true){
    
    $.post(window.location.href+"/delete", {data: id},
       function(data){
        //alert(data);
        if (data.trim() != 'Bạn không có quyền thực hiện thao tác này') {
          $('tr#'+id).remove(); 
          
        };
        
        $("html, body").animate({ scrollTop: 0 }, 100);
       
       }); 
  }
}
window.addEventListener("keydown",function (e) {
    if (e.keyCode === 114 || (e.ctrlKey && e.keyCode === 70)) { 
        e.preventDefault();
        search_click();

    }
});
var preEl ;
var orgBColor;
var orgTColor;
function HighLightTR(el, backColor,textColor){
  //alert(el.id);
  if(typeof(preEl)!='undefined') {
     preEl.bgColor=orgBColor;
     try{ChangeTextColor(preEl,orgTColor);}catch(e){;}
  }
  orgBColor = el.bgColor;
  orgTColor = el.style.color;
  el.bgColor=backColor;

  try{ChangeTextColor(el,textColor);}catch(e){;}
  preEl = el;
}


function ChangeTextColor(a_obj,a_color){  ;
   for (i=0;i<a_obj.cells.length;i++)
    a_obj.cells(i).style.color=a_color;
}

/*Dùng cho bảng nhập liệu*/
function limit_change(limit){
  $.ajax({
      type: "POST",                            // Phương thức gọi là GET
      url: "#",                 // File xử lý
      data: 'page=1&limit='+limit,  
      success: function(server_response)      // Khi xử lý thành công sẽ chạy hàm này
      {
          $('body').html(server_response);    // Hiển thị dữ liệu vào thẻ div #searchresultdata
          
          //Enable sidebar toggle
          $(document).on('click', "[data-toggle='offcanvas']", function (e) {
            e.preventDefault();

            //Enable sidebar push menu
            if ($(window).width() > (768 - 1)) {
              if ($("body").hasClass('sidebar-collapse')) {
                $("body").removeClass('sidebar-collapse').trigger('expanded.pushMenu');
              } else {
                $("body").addClass('sidebar-collapse').trigger('collapsed.pushMenu');
              }
            }
            //Handle sidebar push menu for small screens
            else {
              if ($("body").hasClass('sidebar-open')) {
                $("body").removeClass('sidebar-open').removeClass('sidebar-collapse').trigger('collapsed.pushMenu');
              } else {
                $("body").addClass('sidebar-open').trigger('expanded.pushMenu');
              }
            }
          });
          
           
          if ($('input#search-input').hasClass("loading")) {      // Kiểm tra class "loading"
              $("input#search-input").removeClass("loading");     // Remove class "loading"
          }
  
      }
  });
}
function add_click(url,title){
  open_dialog(url,title);
}
function edit_click(url,title){
  open_dialog(url,title);
}
function info_click(url,title){
  open_dialog(url,title);
}

function search_click(){
  bootbox.prompt("Tìm kiếm", function(result) {
    if (result === null) {
      
    } else {
      $.ajax({
          type: "POST",                            // Phương thức gọi là GET
          url: "#",                 // File xử lý
          data: 'page=1&keyword='+result,  
          success: function(server_response)      // Khi xử lý thành công sẽ chạy hàm này
          {
              $('body').html(server_response);    // Hiển thị dữ liệu vào thẻ div #searchresultdata
              
              //Enable sidebar toggle
              $(document).on('click', "[data-toggle='offcanvas']", function (e) {
                e.preventDefault();

                //Enable sidebar push menu
                if ($(window).width() > (768 - 1)) {
                  if ($("body").hasClass('sidebar-collapse')) {
                    $("body").removeClass('sidebar-collapse').trigger('expanded.pushMenu');
                  } else {
                    $("body").addClass('sidebar-collapse').trigger('collapsed.pushMenu');
                  }
                }
                //Handle sidebar push menu for small screens
                else {
                  if ($("body").hasClass('sidebar-open')) {
                    $("body").removeClass('sidebar-open').removeClass('sidebar-collapse').trigger('collapsed.pushMenu');
                  } else {
                    $("body").addClass('sidebar-open').trigger('expanded.pushMenu');
                  }
                }
              });
              
               
              if ($('input#search-input').hasClass("loading")) {      // Kiểm tra class "loading"
                  $("input#search-input").removeClass("loading");     // Remove class "loading"
              }
      
          }
      });
    }
  });
}
function refresh_click(){
  location.reload();
}
function del_click(url){
  if (url == "" || url == undefined) {
    var url = window.location.href+"/delete";
  }

  bootbox.confirm("Bạn có chắc chắn muốn xóa không?", function(result) {
    if(result) {
      var del = [];
      ids = $('input:checkbox.checkbox:checked').map(function() { return del.push(this.value); });
      $.ajax({
        url: url,   
        type: 'POST',   
        data: "xoa="+del,   
        success:function(answer){ 
          for(var i=0; i<del.length; i++)
             $('tr#'+del[i]).remove();
        }
      });
    }
  });
}

function open_dialog(url, title){
  var dialog = $( "#dialog-message" ).removeClass('hide').dialog({
    autoOpen:false,
    resizable: false,
    modal: true,
    title: "<div class='widget-header widget-header-small blue'><h4 class='smaller'> "+title+"</h4></div>",
    title_html: true,
  });
  dialog.load(url, function(){
     dialog.dialog('open');
 });
}