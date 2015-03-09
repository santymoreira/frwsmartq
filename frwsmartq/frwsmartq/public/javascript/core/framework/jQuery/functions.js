jQuery.validarForm=function(idForm){
    var $form=null;
    if(idForm=='' || idForm==undefined){
        $form=$("form").get(0);
        
    }else{
        $form=$('#'+idForm);
    }
    $($form).validate({
             invalidHandler: function(form, validator) {
                 var errors = validator.numberOfInvalids();
                  if (errors) {
                    var message = errors == 1
                      ? 'Hay error en 1 campo del formulario. Se ha puesto de relieve'
                      : 'Hay errores en '+errors+' campos del formulario. Se han puesto de relieve';
                    $("#dialog-message-validate").html(message);
                    $("#dialog-message-validate").dialog('open');
                  } else {
                    $("#dialog-message-validate").html('');
                  }
                /*this.defaultShowErrors();
                 $("#dialog-message-validate").html('Existe campos invalidos');
                $("#dialog-message-validate").dialog('open');*/
            }
    });    
}

$(function(){
    
    $("body").append("<div id='dialog-message-validate'></div>");
    $("#dialog-message-validate").dialog({
            modal: true,
            autoOpen: false,
            buttons: {
                Ok: function() {
                    $(this).dialog('close');
                }
            },
            title: 'Alerta'
     });
     $.validarForm();
});


