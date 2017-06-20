<?php

/*
 * <script type="text/javascript">
    $(document).ready(function ( $ ) {
        $('#establecimiento_save').click(function(){
        var msg = '<div class="alert alert-danger" role="alert"><strong>Recordatorio!</strong> Antes de hacer una consulta debe marcar la casilla reCAPTCHA</div>';
        if($('#g-recaptcha-response').val() == 0){
          $('#msg').html(msg);
          document.getElementById('top-contact').scrollIntoView();
          return false;
        }
        if($('#g-recaptcha-response').val() == ''){
          $('#msg').html(msg);
          document.getElementById('top-contact').scrollIntoView();
          return false;
        }
        $('#msg').html('');
           $('#resultado').html('');
           $.ajax({
                type: "POST",
                url: "http://farmacia.salud.gob.sv/buscarajax?tipo=0&nombre=" + $('#basica_nombre').val()+"&max="+$('#limite').val(),
                success: function(data) {
                    // Remove current options
                    $('#resultado').html(data);
                    document.getElementById('comments').scrollIntoView();
                }
            });
            return false;
        });
        $('#establecimiento_type_save').click(function(){
        var msg = '<div class="alert alert-danger" role="alert"><strong>Recordatorio!</strong> Antes de hacer una consulta debe marcar la casilla reCAPTCHA</div>';
        if($('#g-recaptcha-response').val() == 0){
          $('#msg').html(msg);
          document.getElementById('top-contact').scrollIntoView();
          return false;
        }
        if($('#g-recaptcha-response').val() == ''){
          $('#msg').html(msg);
          document.getElementById('top-contact').scrollIntoView();
          return false;
        }
        $('#msg').html('');
           $('#resultado').html('');
           var val = $('#establecimiento_type_establecimiento').val(), nombre = $('#establecimiento_type_nombre').val();
           $.ajax({
                type: "POST",
                url: "http://farmacia.salud.gob.sv/buscarajax?tipo=1&nombre=" + $('#basica_nombre').val()+"&depto="+$('#basica_departamento').val()+"&munic="+$('#basica_municipio').val()+"&estab="+$('#basica_establecimiento').val()+"&max="+$('#limite').val(),
                success: function(data) {
                    $('#resultado').html(data);
                    document.getElementById('comments').scrollIntoView();
                }
            });
            return false;
        });
        $('input[type="checkbox"]').click(function(){
        if($(this).attr("value")=="red"){
            $(".red").toggle();
        }
    });
    });
</script>
 * */
 
?>
