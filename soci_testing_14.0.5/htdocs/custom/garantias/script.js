addEventListener('load',inicializarEventos,false);

function inicializarEventos()
{
  //Primero obtenemos la referencia al elemento con el nombre "Busqueda"
  var vec=document.getElementsByName("busqueda");
  for(f=0;f<vec.length;f++)
  {
    //Definimos las funciones que se disparan para los eventos mouseenter(Poner Mouse encima), mouseout(quitar el mouse) y mousemove(Mover el mouse)
    vec[f].addEventListener('mouseenter',mostrarToolTip,false);
    vec[f].addEventListener('mouseout',ocultarToolTip,false);
    vec[f].addEventListener('mousemove',actualizarToolTip,false);
  }
  //Por otro lado la función inicializarEventos crea un div y lo añade al árbol de objetos, este nos servirá para mostrar el mensaje
  var ele=document.createElement('div');
  ele.setAttribute('id','divmensaje');
  vec=document.getElementsByTagName('body');
  vec[0].appendChild(ele);
}
/*
La función mostrarTooltip obtiene la referencia del div que muestra el mensaje 
(hasta este momento está oculto) y cambia la propiedad visibility a "visible", 
luego llama a la función recuperarServidorTooltip(ref.getAttribute('id')).
*/
function mostrarToolTip(e) 
{
  var d = document.getElementById("divmensaje");
  d.style.visibility = "visible";
  d.style.left = e.pageX + 10 + 'px';
  d.style.top = e.pageY + 10 + 'px';
  var ref;
  ref=e.target;
  recuperarServidorTooltip(ref.getAttribute('id'));
}

//Para actualizar la posicion del mouse mientras se mueve
function actualizarToolTip(e) 
{
  var d = document.getElementById("divmensaje");
  d.style.left = e.pageX + 10 + 'px';
  d.style.top = e.pageY + 10 + 'px';
}

//La función ocultarTooTip solo oculta el div del mensaje.
function ocultarToolTip(e) 
{
  var d = document.getElementById("divmensaje");
  d.style.visibility = "hidden";
}
/*
La función recuperarServidorTooltip recibe el valor del atributo id del div 
donde se encuentra la flecha del mouse. Se crea un objeto de la clase XMLHttpRequest 
y se envía el código del div respectivo.
*/
var conexion1;
function recuperarServidorTooltip(codigo) 
{
  conexion1=new XMLHttpRequest();
  conexion1.onreadystatechange = procesarEventos;
  conexion1.open('GET','busqueda.php?cod='+codigo, true);
  conexion1.send();
}
//Luego la función procesarEventos carga el div y procede hacerlo visible
function procesarEventos()
{
  //Por último el programa del servidor genera un trozo de HTML dependiendo del código de div enviado
  var d = document.getElementById("divmensaje");
  d.style.visibility = "visible";
  if(conexion1.readyState == 4)
  {
    d.innerHTML=conexion1.responseText;
  }
}