import { utilsMixin } from '../utilsMixin.js';

const urlRequisicao = "app/handlers/ImpostoHandlers.php";

const vm = new Vue({
    el: '#appImpostos',
    mixins: [utilsMixin],
    data: {
        imposto: {
            id_imposto: "",
            valor_percentual_imposto: "",
        },
        listImpostos: [],
        isEditing: false, // Flag para indicar se está editando um imposto

    },
    methods: {
        abrirModal(acao, imposto = null) {
            if (acao === 'Editar') {
                this.isEditing = true;
                this.imposto = { ...imposto }; // Cria uma cópia do objeto para evitar mutações inesperadas
            } else {
                // Limpa os campos do formulário
                this.limparFormulario();
            }

            // Abre a modal ao clicar no botão
            $('#modalCadastro').modal('show');
        },
        fecharModal() {
            // Limpa os campos do formulário
            this.limparFormulario();

            // Fecha a modal após o cadastro
            $('#modalCadastro').modal('hide');
        },
        limparFormulario() {
            this.isEditing = false;
            this.imposto = { id_imposto: "", valor_percentual_imposto: "" }; // Limpa os campos do formulário
        },
        salvar() {
            //Armazenar a referência ao 'this'
            let self = this;

            if (!self.imposto.valor_percentual_imposto) {
                Swal.fire("Atenção!", "Por favor, preencher o campo <b>valor percentual</b>.", "warning");
                return false;
            }

            //Alterar o label do botão 
            self.changeButtonLabel({ buttonSelector: 'button[type="submit"]', typeButton: 'saving' });

            //Obtêm o token do CSRF
            self.getCsrfToken(function (csrfToken) {

                //Cabeçalho 
                const headers = {
                    'X-CSRF-Token': csrfToken,
                    'Content-Type': 'multipart/form-data'
                };

                // Crie um objeto com os dados do formulário
                let formData = {
                    param: self.isEditing ? 'alterar' : 'salvar',
                    id_imposto: self.imposto.id_imposto,
                    valor_percentual_imposto: self.imposto.valor_percentual_imposto.replace(/\./g, '').replace(',', '.'),
                };

                axios.post(urlRequisicao, formData, { headers })
                    .then(response => {
                        Swal.fire({
                            title: response.data.title,
                            html: response.data.message,
                            type: response.data.type,
                            closeOnConfirm: false,
                            allowOutsideClick: false
                        }).then(function () {
                            if (response.data.status === 200) {
                                self.listar();
                                self.limparFormulario();
                                self.fecharModal();
                            }
                        });

                        //Alterar o label do botão 
                        self.changeButtonLabel({ buttonSelector: 'button[type="submit"]', typeButton: 'save' });
                    })
                    .catch(error => {
                        // Lidar com erros aqui
                        console.error("Erro: " + error);
                    });
            });
        },
        listar() {

            //Cabeçalho 
            const headers = {
                'Content-Type': 'multipart/form-data'
            };

            // Crie um objeto com os dados do formulário
            let formData = {
                param: 'listar',
            };

            axios.post(urlRequisicao, formData, { headers })
                .then(response => {
                    return this.listImpostos = response.data;
                })
                .catch(error => {
                    // Lidar com erros aqui
                    console.error("Erro: " + error);
                    return [];
                });
        }
    },
    computed: {
        modalTitle() {
            return this.isEditing ? 'Editar Imposto' : 'Adicionar Imposto';
        },
    },
    mounted() {
        this.listar();
    }
});