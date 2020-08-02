function changeRole(event) {
    let select = event.target;
    let userCard = select.parentElement;
    let title = document.getElementById('promotion-space').getElementsByTagName('h1')[0];

    let firstAndLastName = userCard.getElementsByTagName('p')[0].innerText.split(" ", 2);
    let wantedRole = select.options[select.selectedIndex].value;
    let promotionId = title.id;

    // TODO : en cas de succès notify ?
    // TODO : aria???

    $.ajax({
        url: '/promotions/changeRole',
        type: 'POST',
        data: {firstName: firstAndLastName[0], lastName: firstAndLastName[1], wantedRole: wantedRole, promotionId: promotionId},
        //dataType: 'json',
        error: function() {
            alert('Le rôle n\'a pas pu être changé.');
            select.value = (wantedRole === 'APPRENANT')? 'ENSEIGNANT' : 'APPRENANT';
        },
        success: function(data) {
            //alert("Record added successfully");
            console.log('Rôle modifié !');
        }
    });
}

MicroModal.init();

let initialName = document.getElementById('promoName').value;
let initialIsClosedPromotion = ($("input[type='radio'][name='isClosedPromotion']:checked").val() == 1);

function validateModalChanges(event) {
    let promotionId = document.getElementsByTagName('h1')[0].id;
    let modifiedName = document.getElementById('promoName').value;
    let modifiedIsClosedPromotion = ($("input[type='radio'][name='isClosedPromotion']:checked").val() == 1);
    
    // If values haven't changed, no need to call the backend
    if (initialName === modifiedName && initialIsClosedPromotion === modifiedIsClosedPromotion) MicroModal.close('modal-promo');
    else {
        //  * appeler api pour update, req ajax => rep dit succès ou pas !
        // 	* si succès, fermer (avec .close('id');

        $.ajax({
            url: '/promotions/update',
            type: 'POST',
            data: {promotionId: promotionId, promotionName: modifiedName, modifiedIsClosedPromotion: (modifiedIsClosedPromotion)? 1 : 0 },
            //dataType: 'json',
            error: function() {
                alert('La mise à jour de la promotion a échoué. Veuillez annuler et réessayer plus tard.');
                // TODO : msg dans la modale ?
                document.getElementById('promoName').value = initialName;
                let radioButtonToCheckID = (initialIsClosedPromotion)? '1' : '0';
                $(radioButtonToCheckID).prop("checked", true);
            },
            success: function(data) {
                console.log('Modifications appliquées !');
                document.getElementsByTagName('h1')[0].innerText = "Espace de la promotion " + modifiedName;
                MicroModal.close('modal-promo');
            }
        });
    }
}