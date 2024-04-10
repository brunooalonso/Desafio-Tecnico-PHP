import { utilsMixin } from '../utilsMixin.js';

const urlRequisicao = "app/handlers/ProdutoHandlers.php";
const urlRequisicaoTipo = "app/handlers/TipoHandlers.php";

Vue.component('v-select', VueSelect.VueSelect);

const vm = new Vue({
    el: '#appProduto',
    mixins: [utilsMixin],
    data: {
        produto: {
            id_produto: "",
            id_tipo: "",
            nome_produto: "",
            valor_venda_produto: "",
        },
        listProdutos: [],
        listTipos: [],
        isEditing: false, // Flag para indicar se está editando 

    },
    methods: {
        abrirModal(acao, produto = null) {

            let self = this;

            if (acao === 'Editar') {
                self.isEditing = true;
                self.produto = { ...produto }; // Cria uma cópia do objeto para evitar mutações inesperadas
            } else {
                // Limpa os campos do formulário
                self.limparFormulario();
            }

            //Carregar o select ao abrir a modal
            self.carregarTipo();

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
            this.produto = {
                id_produto: "",
                id_tipo: "",
                nome_produto: "",
                valor_venda_produto: "",
            }; // Limpa os campos do formulário
        },
        salvar() {
            //Armazenar a referência ao 'this'
            let self = this;

            if (!self.produto.nome_produto) {
                Swal.fire("Atenção!", "Por favor, preencher o campo <b>nome do produto</b>.", "warning");
                return false;
            }

            if (!self.produto.valor_venda_produto) {
                Swal.fire("Atenção!", "Por favor, preencher o campo <b>preço de venda</b>.", "warning");
                return false;
            }

            if (!self.produto.id_tipo) {
                Swal.fire("Atenção!", "Por favor, selecionar o campo <b>tipo produto</b>.", "warning");
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
                    id_produto: self.produto.id_produto,
                    id_tipo: self.produto.id_tipo,
                    nome_produto: self.produto.nome_produto,
                    valor_venda_produto: self.produto.valor_venda_produto
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
                    return this.listProdutos = response.data;
                })
                .catch(error => {
                    // Lidar com erros aqui
                    console.error("Erro: " + error);
                    return [];
                });
        },
        carregarTipo() {
            //Cabeçalho 
            const headers = {
                'Content-Type': 'multipart/form-data'
            };

            // Crie um objeto com os dados do formulário
            let formData = {
                param: 'listar',
            };

            axios.post(urlRequisicaoTipo, formData, { headers })
                .then(response => {
                    // Ordenar os tipos pelo nome_tipo
                    return this.listTipos = response.data.sort((a, b) => a.nome_tipo - b.nome_tipo);
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
            return this.isEditing ? 'Editar Produto' : 'Adicionar Produto';
        },
    },
    mounted() {
        // Referência ao componente Vue
        let self = this;

        self.listar();
    }
});