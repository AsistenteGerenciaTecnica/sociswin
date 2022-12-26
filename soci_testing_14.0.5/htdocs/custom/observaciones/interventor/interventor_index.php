<?php

/**
 * INDEX
 * 
 * Página principal, dos botones, no tiene ninguna funcionalidad especial
 */

include_once "./interventor_header.php";

?>


<div class="container">
    <div class="title">
        INTERVENTOR
    </div>
    <div class="options">
        <a class="btn_option" href="./interventor_search.php?from=interventor_index.php">
            <img src="./img/lupa.png" alt="" style="max-height: 40px">
            <div style="margin-top: 5px">
                Buscar<br>Intervención
            </div> 
        </a>
        <a class="btn_option" href="./interventor_list.php?from=interventor_index.php">
            <img src="./img/lista.png" alt="" style="max-height: 40px">
            <div style="margin-top: 5px">
                Listar<br>Intervenciones
            </div>
        </a>
    </div>
</div>
   
<?php

include_once "./interventor_footer.php";

?>