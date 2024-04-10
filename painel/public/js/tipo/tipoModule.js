import { utilsMixin } from '../utilsMixin.js';

const urlRequisicao = "app/handlers/TipoHandlers.php";
const urlRequisicaoImposto = "app/handlers/ImpostoHandlers.php";

Vue.component('v-select', VueSelect.VueSelect);

const vm = new Vue({
    el: '#appTipo',
    mixins: [utilsMixin],
    data: {
        tipo: {
            id_tipo: "",
            id_imposto: "",
            nome_tipo: "",
        },
        listTipos: [],
        listImpostos: [],
        isEditing: false, // Flag para indicar se está editando 

    },
    methods: {
        abrirModal(acao, tipo = null) {
            let self = this;

            if (acao === 'Editar') {
                self.isEditing = true;
                self.tipo = { ...tipo }; // Cria uma cópia do objeto para evitar mutações inesperadas
            } else {
                // Limpa os campos do formulário
                self.limparFormulario();
            }

            //Carregar o select ao abrir a modal
            self.carregarImpostos();

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
            this.tipo = { id_tipo: "", id_imposto: "", nome_tipo: "" }; // Limpa os campos do formulário
        },
        salvar() {
            //Armazenar a referência ao 'this'
            let self = this;

            if (!self.tipo.nome_tipo) {
                Swal.fire("Atenção!", "Por favor, preencher o campo <b>nome</b>.", "warning");
                return false;
            }

            if (!self.tipo.id_imposto) {
                Swal.fire("Atenção!", "Por favor, selecionar o campo <b>valor percentual</b>.", "warning");
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
                    id_tipo: self.tipo.id_tipo,
                    id_imposto: self.tipo.id_imposto,
                    nome_tipo: self.tipo.nome_tipo
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
                    return this.listTipos = response.data;
                })
                .catch(error => {
                    // Lidar com erros aqui
                    console.error("Erro: " + error);
                    return [];
                });
        },
        carregarImpostos() {
            //Cabeçalho 
            const headers = {
                'Content-Type': 'multipart/form-data'
            };

            // Crie um objeto com os dados do formulário
            let formData = {
                param: 'listar',
            };

            axios.post(urlRequisicaoImposto, formData, { headers })
                .then(response => {
                    // Ordenar os impostos pelo valor_percentual_imposto
                    return this.listImpostos = response.data.sort((a, b) => parseFloat(a.valor_percentual_imposto) - parseFloat(b.valor_percentual_imposto));
                })
                .catch(error => {
                    // Lidar com erros aqui
                    console.error("Erro: " + error);
                    return [];
                });
        },
    },
    computed: {
        modalTitle() {
            return this.isEditing ? 'Editar Tipo' : 'Adicionar Tipo';
        },
    },
    mounted() {
        // Referência ao componente Vue
        let self = this;

        self.listar();
      
    }
});