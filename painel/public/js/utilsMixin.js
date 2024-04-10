export const utilsMixin = {
    methods: {
        changeButtonLabel({ buttonSelector, typeButton }) {
            if (typeButton === "saving") {
                $(buttonSelector).html('<i class="fa fa-spinner fa-pulse" aria-hidden="true"></i> Salvando...').prop("disabled", true);
            } else if (typeButton === "save") {
                $(buttonSelector).html('<i class="fas fa-save"></i> Salvar').prop("disabled", false);
            }
        },
        validarNumeroInteiro(event) {
            const valor = event.target.value;
            const numeros = /[^\d]+/g;
    
            if (!numeros.test(valor)) {
                event.target.value = '';
            }
        },
        validarPreco(event) {
            const valor = event.target.value;
            const numeros = /[^\d.,]+/g;
    
            if (!numeros.test(valor)) {
                event.target.value = '';
            }
        },
        getCsrfToken(callback) {
            $.ajax({
                url: 'app/library/LibraryHandler.php',
                type: 'POST',
                dataType: 'json',
                data: { param: 'getCsrfToken' },
                success: function (response) {
                    callback(response.csrf_token);
                }
            });
        }
    },
};