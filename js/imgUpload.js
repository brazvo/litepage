function ajaxImageUpload(cid, formName, elementName)
{
        $("#loading")
        .ajaxStart(function(){
                $(this).show();
        })
        .ajaxComplete(function(){
                $(this).hide();
        });

        $.ajaxFileUpload
        (
                {
                        url:'admin/content/ajax-image-upload/'+cid+'?element='+elementName,
                        //url:'doajaxfileupload.php',
                        secureuri:false,
                        fileElementId: formName+'-'+elementName,
                        dataType: 'json',
                        success: function (data, status)
                        {
                                if(typeof(data.error) != 'undefined')
                                {
                                        if(data.error != '')
                                        {
                                                alert(data.error);
                                                $('#'+formName+'-'+elementName).val('');
                                        }else
                                        {
                                                $('#frmCtrAjaxImagesWrapper').load("admin/content/edit/"+cid+" #AjaxImagesWrapper");
                                                $('#'+formName+'-'+elementName).val('');
                                        }
                                }
                            
                        },
                        error: function (data, status, e)
                        {
                                alert(e);
                                $('#'+formName+'-'+elementName).val('');
                        }
                }
        )

        return false;

}

jQuery(document).ready(function($){
    
    $('.image-delete').live('click',
        function(){
            if(confirm('Naozaj vymazať??')) {
                
                $("#loading")
                .ajaxStart(function(){
                        $(this).show();
                })
                .ajaxComplete(function(){
                        $(this).hide();
                });
                
                var name = $(this).attr('name');
                var id = name.split("__", 1);
                var image = $('#img'+id).attr('alt');
                var cid = $('#formContentEdit-id').val();
                
                $.ajax({
                    url: "admin/content/ajaxImageDelete/"+id+"?image="+image,
                    dataType: "json",
                    success: function(data){
                        if(typeof(data.status) != 'undefined')
                        {
                            if(data.status == 'notok')
                            {
                                    alert("Výmaz súboru sa nepodaril");
                            }else
                            {
                                    $('#frmCtrAjaxImagesWrapper').load("admin/content/edit/"+cid+" #AjaxImagesWrapper");
                            }
                        }
                    },
                    error: function(){
                        alert("Výmaz súboru sa nepodaril...");
                    }
                });
            }
            return false;
        }
    );
    
});