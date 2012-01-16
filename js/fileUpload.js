function ajaxFileUpload(cid, formName, elementName)
{
        $("#loadingFile")
        .ajaxStart(function(){
                $(this).show();
        })
        .ajaxComplete(function(){
                $(this).hide();
        });

        $.ajaxFileUpload
        (
                {
                        url:'admin/content/ajax-file-upload/'+cid+'?element='+elementName,
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
                                                $('#frmCtrAjaxFilesWrapper').load("admin/content/edit/"+cid+" #AjaxFilesWrapper");
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
    
    $('.file-delete').live('click',
        function(){
            if(confirm('Naozaj vymazať??')) {
                
                $("#loadingFile")
                .ajaxStart(function(){
                        $(this).show();
                })
                .ajaxComplete(function(){
                        $(this).hide();
                });
                
                var name = $(this).attr('name');
                var id = name.split("__", 1);
                var file = $('#file'+id).text();
                var cid = $('#formContentEdit-id').val();
                
                $.ajax({
                    url: "admin/content/ajaxFileDelete/"+id+"?file="+file,
                    dataType: "json",
                    success: function(data){
                        if(typeof(data.status) != 'undefined')
                        {
                            if(data.status == 'notok')
                            {
                                    alert("Výmaz súboru sa nepodaril");
                            }else
                            {
                                    $('#frmCtrAjaxFilesWrapper').load("admin/content/edit/"+cid+" #AjaxFilesWrapper");
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