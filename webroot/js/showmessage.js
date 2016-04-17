/**
 * Created by Darkweizer on 06/02/2016.
 */

function showmessage($formulaire){
    $("#" + $formulaire).submit(function(event) {
        var data = $(this).serialize();
        $.ajax({
            type: $(this).attr("method"),
            url: $(this).attr("action"),
            data: data,
            success: function (retour) {
                if (retour.send == "ok") {
                    if(retour.redirection != "") {
                        window.location.href = retour.redirection;
                        if(retour.msg == "") {
                            return;
                        }
                    }
                    Materialize.toast(retour.msg, 2000, 'rounded teal accent-4');
                    document.getElementById($formulaire).reset();
                    $('#result').empty();
                }
                else {
                    Materialize.toast(retour.msg, 4000, 'rounded red');
                    document.getElementById($formulaire).reset();
                    $('#result').empty();
                    //$('#result').empty().append($('<span>').html(retour.msg));
                }

            }, // success()
            error: function (resultat, statut, erreur) {
                alert("Erreur dans l'encodage en javascript");
            }
        });
        return false;
    });
}