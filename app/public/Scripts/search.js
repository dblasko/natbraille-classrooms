let searchBars = document.getElementsByTagName('input');
for (searchBar of searchBars) {
    searchBar.addEventListener('input', search);
}

function search(event) {
    let parent, texteAtester, indice;
    let input = event.target;
    let filter = input.value.toLowerCase();
    let divExercices = input.parentElement.parentElement.getElementsByTagName('div')[1];
    let nomExercices = divExercices.getElementsByTagName('p');

    for (i = 0; i < nomExercices.length; i++) {
        texteAtester = nomExercices[i].innerHTML.toLowerCase();
        indice = texteAtester.indexOf(filter);
        parent = nomExercices[i].parentNode;
        if (indice < 0) parent.classList.add("visuallyhidden");
        else parent.classList.remove("visuallyhidden");
    }
}