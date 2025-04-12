function bastim(){
$.isLoading({ text: "<img src='assets/plugins/loader_overlay/loader.gif'>" });
}

function bitti(){
$.isLoading( "hide" );
}

function AjaxFormS(FORMID,SONUCID){
$("#"+SONUCID).fadeOut(400);

var vls = $("#"+FORMID+" button.btn-purple").html();
var onc = $("#"+FORMID+" button.btn-purple").attr("onclick");

$("#"+FORMID+" button.btn-purple").html('Bekleyin...');
$("#"+FORMID+" button.btn-purple").attr("onclick","");
$("#"+FORMID+" button.btn-purple").attr("type","button");

/*bastim();*/

$("#"+FORMID).ajaxForm({
target: '#'+SONUCID,
complete:function(){
$("#"+SONUCID).fadeIn(400);

/*bitti();*/

$("#"+FORMID+" .btn-purple").html(vls);
$("#"+FORMID+" .btn-purple").attr("onclick",onc);
$("#"+FORMID+" .btn-purple").attr("type","submit");


}
}).submit();
	
}

function ajaxHere(nere,load_content){
/*bastim();*/
$("#"+load_content).html('');
$.get(nere, function(data, status){
$("#"+load_content).html(data);
/*bitti();*/
});
}


$("ul[role=menu] li a:contains('Sil')").click(function(event){
event.preventDefault();
var hrefi = $(this).attr("href");

if(hrefi == 'javascript:;'){
return false;
}else{

$(this).attr("href","javascript:void(0);");
if(sor_yonlendir(hrefi) == false){
$(this).attr("href",hrefi);
}

}


});

function sor_yonlendir(link) {
if(confirm("Bu işlemi gerçekten yapmak istiyor musunuz ?")){
window.location.href = link;
}else{
return false;
}
}



function sendFile(file, editor, welEditable) {
$("#resim_progress").fadeIn(500);
            data = new FormData();
            data.append("file", file);
            $.ajax({
                data: data,
                type: "POST",
                url: "ajax.php?p=editor_resim_yukle",
                cache: false,
                contentType: false,
				processData: false,
                success: function(url) {
				$("#resim_progress").fadeOut(500);
                 $('.summernote').summernote('editor.insertImage', url);
                }
            });
        }




$(document).ready(function(){
	var genislik = window.screen.width;
	var yukseklik = window.screen.height;
	
	if(genislik < 750){
	$(".button-menu-mobile").css("display","block");
	}else{

	}
});

$(function(){

$(".datatable th:contains('Seç')").html('<label>Seç <input type="checkbox" id="checkAll" style="display:none" /></label>');

 $("#checkAll").click(function () {
     $('#SelectForm input:checkbox').not(this).prop('checked', this.checked);
 });


});