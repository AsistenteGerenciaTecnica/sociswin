<?php

/**
 * BÚSQUEDA
 * 
 * Una vista sencilla con una barra de búsqueda,
 * redirige a la página del listado
 */

include_once "./interventor_header.php";

?>

<div class="container">
    <div style="height: 10px; margin-bottom: 15px">

    <?php
    $from = (isset($_GET["from"]) ? $_GET["from"] : "interventor_list.php");
    echo '<a class="btn_small" href="'. $from .'"><</a>';

    ?>

    </div>
    <div class="title">
        INTERVENTOR
    </div>
    <div class="subtitle">
        Buscar Intervención
    </div>
    <div class="content">
        <div class="inter_card">
            <form method="GET" action="./interventor_list.php">
                <div class="search_bar">
                    <input type="hidden" name="from" value="interventor_search.php">
                    
                    <input class="main_input" type="text" name="search_ref">
                    <input type="submit" value="">
                </div>
            </form>
        </div>
    </div>
</div>

<?php

include_once "./interventor_footer.php";

?>