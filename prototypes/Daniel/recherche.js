function search() {

  //    document.location.reload(true);
  var input, parent, filter, i, texteAtester, indice;
  input = document.getElementById('exercise-research');
  filter = input.value.toLowerCase();
  var divExercices = document.getElementById('exercises-row-2');
  var nomExercices = divExercices.getElementsByTagName('p');


  //comparer un à un 
  for (i = 0; i < nomExercices.length; i++) {

    texteAtester = nomExercices[i].innerHTML.toLowerCase();

    indice = texteAtester.indexOf(filter);

    parent = nomExercices[i].parentNode;
    if (indice < 0) parent.classList.add("visuallyhidden");
    else parent.classList.remove("visuallyhidden");
  }
}