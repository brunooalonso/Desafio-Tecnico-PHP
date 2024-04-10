</div>

<script src="public/assets/js/vue/vue.js"></script>
<script src="public/assets/js/axios.min.js"></script>
<script src="public/assets/js/vue/vue-select.js"></script>

<!-- Vendor js -->
<script src="public/assets/js/vendor.min.js"></script>

<script src="public/assets/libs/sweetalert2/sweetalert2.min.js"></script>

<?php
//Verifica se alguma pagina tem js incluido
if (isset($pushScript) && !empty($pushScript)) {

    //Percorremos todos os link do js para inserir na pagina
    foreach ($pushScript as $script => $isModule) {

        // Adiciona o atributo type="module" se $isModule for verdadeiro
        $moduleAttribute = ($isModule === true) ? ' type="module"' : '';

        echo '<script' . $moduleAttribute . ' src="' . (is_string($isModule) ? $isModule : $script) . '"></script>';
    }
}
?>
<!-- App js -->
<script src="public/assets/js/app.min.js"></script>
</body>

</html>