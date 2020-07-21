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
            console.log(data);
            console.log('Rôle modifié !');
        }
    });
}