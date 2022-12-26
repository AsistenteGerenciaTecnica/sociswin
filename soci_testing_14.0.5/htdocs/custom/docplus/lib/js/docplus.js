/* Loop through all dropdown buttons to toggle between hiding and showing its dropdown content - 
This allows the user to have multiple dropdowns without any conflict */

function toggleDropdown(e) {
    let dropdown = e.target;
    
    var dropdownContent = dropdown.parentNode.nextElementSibling;
    
    if (dropdownContent.style.display === "block") {
        dropdownContent.style.display = "none";
        dropdown.classList.remove("dropdown_active");
    } else {
        dropdownContent.style.display = "block";
        dropdown.classList.add("dropdown_active");
    }
}

/**
 * MOSTRAR LAS OPCIONES DE RENOVACIÓN 
 */
function toggleRenovacion(e) {
    let renovacion = e.target;
    let options = renovacion.parentNode.nextElementSibling;

    if (renovacion.checked) {
        options.style.display = "block";
    } else {
        options.style.display = "none";
    }

}

/**
 * MOSTRAR INPUTS DE FECHA 
 */
function toggleFecha(fecha) {
    // radio < div < renovacion_type - type_cada - type_fecha

    let cadaOptions = fecha.parentNode.parentNode.nextElementSibling;
    let fechaOptions = cadaOptions.nextElementSibling;

    if (fecha.checked) {
        fechaOptions.style.display = "flex";
        cadaOptions.style.display = "none";
    }
}

/**
 * MOSTRAR INPUTS DE CADA CUANTO TIEMPO
 */
function toggleCada(cada) {
    // radio < div < renovacion_type - type_cada - type_fecha

    let cadaOptions = cada.parentNode.parentNode.nextElementSibling;
    let fechaOptions = cadaOptions.nextElementSibling;

    if (cada.checked) {
        cadaOptions.style.display = "flex";
        fechaOptions.style.display = "none";
    }

}


/** 
 * VERIFICAR QUE UN INPUT SOLO CONTENGA NÚMEROS
*/

function checkNumberInput(e) {
    let val = e.data;
    let text = e.target.value;

    if (text[0] == "0") {
        let newval = "";
        let all = false
        for (let j = 0; j < text.length; j++) {
            if (text[j] != 0) {
                all = true;
            }
            if (all) {
                newval += text[j];
            }
        }
        e.target.value = newval;
    } else {
        if (val != null) {
            let nums = ["0", "1", "2", "3", "4", "5", "6", "7", "8", "9"];

            let found = nums.includes(val);

            if (!found) {
                let newval = "";
                for (let j = 0; j < text.length; j++) {
                    if (text[j] != val) {
                        newval += text[j];
                    }
                }
                e.target.value = newval;
            }
        }
    }
}


/**
 * MOSTRAR Y OCULTAR MODAL
 */
function showModal(type) {
    let modal = document.getElementById(type);

    modal.style.display = "block";
}

function hideModal(idMain, idSub) {
    let modal = document.getElementById(idMain);
    let submodal = document.getElementById(idSub);

    modal.style.display = "none";
    submodal.style.display = "none";
}

/**
 * MOSTRAR MODAL DE ELIMINACIÓN DE ARCHIVO
 */
function showDeleteModalFile(e) {
    showModal("modalDeleteFile");

    let deleteModalFile = document.getElementById("deleteModalFile");

    deleteModalFile.style.display = "block";
}

/**
 * MOSTRAR MODAL DE HISTORIAL
 */
function showHistoryModal(e) {
    showModal("modalHistory");

    let historyModal = document.getElementById("historyModal");

    historyModal.style.display = "block";
}

/** 
 * MOSTRAR MODAL DE ELIMINACIÓN DE OBJETO
 */
 function showDeleteModal(e) {
    showModal("modalDelete");

    let deleteModal = document.getElementById("deleteModal");

    let obj = e.target.id.split("_")
    let objId = obj.pop();
    let objType = obj.pop();

    console.log(objId);
    console.log(objType);

    let deleteModalNombre = document.getElementById("eliminar_obj_nombre");
    let objetoNombre = document.getElementById(objType + "_nombre_" + objId).innerHTML;
    deleteModalNombre.innerHTML = objetoNombre;    

    let deleteModalId = document.getElementById("eliminar_obj_id");
    deleteModalId.name = "eliminar_" + objType + "_id";
    deleteModalId.value = objId;


    let deleteModalButton = document.getElementById("delete_obj");
    deleteModalButton.name = "delete_" + objType;

    deleteModal.style.display = "block";
}

/**
 * MODAL PARA ELIMINAR ARCHIVOS
 */
function showDeleteModalDoc(e) {
    showModal("modalDeleteDoc");

    let deleteModal = document.getElementById("deleteModalDoc");

    let obj = e.target.id.split("_")
    let objId = obj.pop();

    console.log(objId);

    let deleteModalNombre = document.getElementById("eliminar_file_nombre");
    let fileNombre = document.getElementById("file_id_" + objId).value;
    deleteModalNombre.innerHTML = fileNombre;    

    let deleteFileNombre = document.getElementById("eliminar_file");
    deleteFileNombre.value = fileNombre;

    let docId = document.getElementById("file_doc_id_" + objId).value;
    let deleteModalId = document.getElementById("eliminar_doc_id");
    deleteModalId.value = docId;

    deleteModal.style.display = "block";
}

/**
 * MOSTRAR MODAL PARA SUBIR DOCUMENTOS
 */

function showUploadModal(e)
{
    showModal("modalUpload");

    let uploadModal = document.getElementById("uploadModal");

    let obj = e.target.id.split("_")
    let objId = obj.pop();

    let uploadModalNombre = document.getElementById("upload_doc_nombre");
    let documentoNombre = document.getElementById("doc_nombre_" + objId).innerHTML;
    uploadModalNombre.innerHTML = documentoNombre;

    let uploadModalId = document.getElementById("upload_doc_id");
    uploadModalId.value = objId;

    uploadModal.style.display = "block";
}

/**
 * CAMBIAR TIPO DE MÓDULO
 */
function changeModule()
{
    let form = document.getElementById("form_modulo");

    form.submit();
}

/**
 * CAMBIAR ESTADO DE EDICIÓN DE UN OBJETO
 */
function changeEditing(id, type)
{
    let editing = document.getElementById("editing_"+ type + "_" + id);
    let regular = document.getElementById("regular_"+ type + "_" + id);

    if (editing.style.display == "none")
    {
        editing.style.display = "flex";
        regular.style.display = "none";
    }
    else
    {
        editing.style.display = "none";
        regular.style.display = "flex";
    }
}

/**
 * CAMBIAR ESTADO DE EDICIÓN DE LOS CAMPOS DE UN DOCUMENTO
 */
function changeCampos(button)
{
    console.log("XD");

    let obj = button.id.split("_");
    let objId = obj.pop();

    let campos = document.querySelectorAll(".campo_" + objId);

    for (let i = 0; i < campos.length; i++)
    {
        let campo = campos[i];

        
        if (campo.hasAttribute("disabled"))
        {            
            campo.removeAttribute("disabled");
            document.getElementById("editing_campos_" + objId).style.display = "flex";
            document.getElementById("regular_campos_" + objId).style.display = "none";
        }
        else
        {
            campo.setAttribute("disabled", "true");
            document.getElementById("editing_campos_" + objId).style.display = "none";
            document.getElementById("regular_campos_" + objId).style.display = "flex";
        }
    }
}


/**
 * ACTIVAR INPUT PARA VALORES
 */
function updateValuesInput(select)
{    
    let id = select.id.split("_").pop();

    let valores = document.getElementById("valores_campo_" + id);

    if (select.value == "selection")
    {
        valores.removeAttribute("disabled");
    }
    else
    {
        valores.setAttribute("disabled", "true");
    }
}

/**
 * ACTUALIZAR NOMBRE DEL ARCHIVO
 */
function updateFileName(){
    //console.log("file changed");
    let name = document.getElementById("file_name");
    console.log("name: " + name);
     
    let input = document.getElementById("upload_doc");
    let file = input.files[0];
 
    let filename;
    try {
        filename = file.name;
    } catch (e) {
        filename = "Ninguna imagen seleccionada"
    }
 
    name.innerHTML = filename;
}

/**
 * 
 * ASIGNAR LISTENERS
 * 
 */

// Dropdowns
var dropdown = document.getElementsByClassName("dropdown_button");

for (let i = 0; i < dropdown.length; i++) {
    dropdown[i].addEventListener("click", function (e) {
        toggleDropdown(e);
    });
}

// Checkbox ID Renovación
var renovacion = document.querySelectorAll("#renovacion");

for (let i = 0; i < renovacion.length; i++) {

    renovacion[i].addEventListener("click", function (e) {
        toggleRenovacion(e);
    })
}

// RadioButtons ID Fecha
var fechas = document.querySelectorAll("#fecha");

for (let i = 0; i < fechas.length; i++) {

    let radio = fechas[i];

    radio.addEventListener("click", function (e) {
        let fecha = e.target;
        toggleFecha(fecha);
    })

    toggleFecha(radio);
}

// RadioButtons ID Cada
var cadas = document.querySelectorAll("#cada");

for (let i = 0; i < cadas.length; i++) {
    let radio = cadas[i];

    radio.addEventListener("click", function (e) {
        let cada = e.target;
        toggleCada(cada);
    })

    toggleCada(radio);

}

// Inputs Clase number_input
var number_inputs = document.querySelectorAll(".number_input");

for (let i = 0; i < number_inputs.length; i++) {
    let input = number_inputs[i];

    input.addEventListener("input", function (e) {
        checkNumberInput(e);
    });
}

// Botones Clase delete
var delete_buttons = document.querySelectorAll(".delete");

for (let i = 0; i < delete_buttons.length; i++) {
    delete_buttons[i].addEventListener("click", function (e) {
        showDeleteModalDoc(e);
    })
}

// Botones Clase history
/* var history_buttons = document.querySelectorAll(".history");

for (let i = 0; i < history_buttons.length; i++) {
    history_buttons[i].addEventListener("click", function (e) {
        showHistoryModal(e);
    })
} */

// Botones Clase delete_obj
var delete_doc_buttons = document.querySelectorAll(".delete_obj");

for (let i = 0; i < delete_doc_buttons.length; i++) {
    delete_doc_buttons[i].addEventListener("click", function (e) {
        showDeleteModal(e);
    })
}

// Botones Clase upload_doc
var upload_doc_buttons = document.querySelectorAll(".upload");

for (let i = 0; i < upload_doc_buttons.length; i++) 
{
    upload_doc_buttons[i].addEventListener("click", function (e)
    {
        showUploadModal(e);
    });
}

var tipo_campo_selects = document.querySelectorAll(".campo_select");

for (let i = 0; i < tipo_campo_selects.length; i++) 
{
    let campo_select = tipo_campo_selects[i];

    campo_select.addEventListener("change", function (e) {
        let select = e.target;
        updateValuesInput(select);
    });

    updateValuesInput(campo_select);
    
}

var edit_campos_button = document.querySelectorAll(".edit_campos")

for (let i = 0; i < edit_campos_button.length; i++)
{
    let campo_button = edit_campos_button[i];

    campo_button.addEventListener("click", function (e) {
        let button = e.target;
        changeCampos(button)
    });
}

// CONTROL DE PÁGINAS
document.getElementById("max_rows").addEventListener("change", (e) => {

    let form = document.getElementById("pages_form");

    form.submit();

})