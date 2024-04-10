import { utilsMixin } from '../utilsMixin.js';

const urlRequisicao = "app/handlers/VendaHandlers.php";
const urlRequisicaoTipo = "app/handlers/TipoHandlers.php";
const urlRequisicaoProduto = "app/handlers/ProdutoHandlers.php";

Vue.component('v-select', VueSelect.VueSelect);

const vm = new Vue({
    el: '#appVenda',
    mixins: [utilsMixin],
    data: {
        venda: {
            id_venda: "",
            id_venda_produto: "",
            id_produto: "",
            id_tipo: "",
            quantidade_produto: 1,
            valor_produto: "",
            valor_percentual_imposto: "",
        },
        listVendas: [],
        listItensProdutos: [],
        listProdutos: [],
        listTipos: [],
        isEditing: false,

    },
    methods: {
        abrirModal(acao, id_venda = null) {
            let self = this;

            if (acao === 'Editar') {
                self.isEditing = true;
                self.venda.id_venda = id_venda;
            } else {
                // Limpa os campos do formulário
                self.limparFormulario();
            }

            //Carregar o select ao abrir a modal
            self.carregarTipo();
            self.carregarProduto();
            self.listarItemProduto();

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
            let self = this;
            self.isEditing = false;
            self.venda = {
                id_venda: "",
                id_venda_produto: "",
                id_tipo: null,
                id_produto: null,
                quantidade_produto: 1,
                valor_produto: "",
                valor_percentual_imposto: "",
            };
            self.listItensProdutos = [];
        },
        // Método para carregar detalhes do produto selecionado
        carregarDetalhesProdutoSelecionado() {
            let self = this;
            // Encontre o produto selecionado na lista de produtos
            const produtoSelecionado = self.listProdutos.find(produto => produto.id_produto === self.venda.id_produto);

            // Se o produto selecionado for encontrado
            if (produtoSelecionado) {
                // Atualize os campos da venda com os detalhes do produto selecionado
                self.venda.valor_produto = produtoSelecionado.valor_venda_produto;
                this.venda.valor_percentual_imposto = produtoSelecionado.valor_percentual_imposto;
            } else {
                // Se o produto selecionado não for encontrado, limpe os campos relacionados ao produto
                self.venda.valor_produto = "";
                self.venda.valor_percentual_imposto = "";
            }
        },
        // Adicionar um item à tabela
        adicionarItemProduto() {
            let self = this;

            // Verifique se há um produto selecionado
            if (!self.venda.id_produto) {
                Swal.fire("Atenção!", "Por favor, selecionar o campo produto.", "warning");
                return;
            }

            if (!self.venda.quantidade_produto) {
                Swal.fire("Atenção!", "Por favor, preencher o campo quantidade.", "warning");
                return;
            }

            // Verificar se a nova quantidade é menor que 1
            if (self.venda.quantidade_produto < 1) {
                Swal.fire("Atenção!", "A quantidade do produto deve ser maior ou igual a 1.", "warning");
                return;
            }

            // Verifique se o produto já está na lista de itens
            const produtoExistente = self.listItensProdutos.find(item => item.id_produto === self.venda.id_produto);

            if (produtoExistente) {
                Swal.fire("Atenção!", "O produto já foi adicionado à lista.", "warning");
                return;
            }

            // Encontre o produto selecionado na lista de produtos
            const produtoSelecionado = self.listProdutos.find(produto => produto.id_produto === self.venda.id_produto);

            // Verifique se o produto selecionado foi encontrado
            if (!produtoSelecionado) {
                Swal.fire("Atenção!", "O produto selecionado não existe.", "warning");
                return;
            }

            // Converta os valores para float
            const valorProduto = parseFloat(produtoSelecionado.valor_venda_produto.replace(/\./g, '').replace(',', '.'));
            const percentualImposto = parseFloat(produtoSelecionado.valor_percentual_imposto.replace(/\./g, '').replace(',', '.'));
            const quantidadeProduto = self.venda.quantidade_produto;

            // Calcule o valor total do produto, incluindo o imposto
            const valorTotalProduto = valorProduto * quantidadeProduto;
            const valorTotalImposto = (percentualImposto / 100) * valorTotalProduto;

            // Adicione o produto selecionado à lista de itens
            self.listItensProdutos.push({
                id_venda_produto: "",
                id_tipo: produtoSelecionado.id_tipo,
                id_produto: produtoSelecionado.id_produto,
                nome_produto: produtoSelecionado.nome_produto,
                quantidade_venda_produto: quantidadeProduto,
                valor_produto_venda_produto: valorProduto.toLocaleString('pt-BR', { minimumFractionDigits: 2 }),
                valor_total_produto_venda_produto: valorTotalProduto.toLocaleString('pt-BR', { minimumFractionDigits: 2 }),
                valor_imposto_venda_produto: percentualImposto.toLocaleString('pt-BR', { minimumFractionDigits: 2 }),
                valor_total_imposto_venda_produto: valorTotalImposto.toLocaleString('pt-BR', { minimumFractionDigits: 2 }),
            });

            // Limpe os campos após adicionar o item
            self.venda.id_tipo = null;
            self.venda.id_produto = null;
            self.venda.quantidade_produto = 1;
        },
        atualizarQuantidadeItemProduto(index, novaQuantidade) {
            let self = this;
            const item = self.listItensProdutos[index];

            const valorProduto = parseFloat(item.valor_produto_venda_produto.replace(/\./g, '').replace(',', '.'));
            const percentualImposto = parseFloat(item.valor_imposto_venda_produto.replace(/\./g, '').replace(',', '.'));
            const quantidadeProduto = Number(novaQuantidade);

            const valorTotalProduto = valorProduto * quantidadeProduto;
            const valorTotalImposto = (percentualImposto / 100) * valorTotalProduto;

            // Atualizar os valores totais do produto e os valores totais do imposto para o item específico
            item.valor_total_produto_venda_produto = valorTotalProduto.toLocaleString('pt-BR', { minimumFractionDigits: 2 });
            item.valor_total_imposto_venda_produto = valorTotalImposto.toLocaleString('pt-BR', { minimumFractionDigits: 2 });

            // Atualizar a quantidade para o item correspondente em listItensProdutos
            item.quantidade_venda_produto = quantidadeProduto;
        },
        removerItemProduto(index) {
            // Remova o produto da lista de produtos na tabela
            this.listItensProdutos.splice(index, 1);
        },
        listarItemProduto() {

            let self = this;

            //Cabeçalho 
            const headers = {
                'Content-Type': 'multipart/form-data'
            };

            // Crie um objeto com os dados do formulário
            let formData = {
                param: 'listarVendaProduto',
                id_venda: self.venda.id_venda
            };

            axios.post(urlRequisicao, formData, { headers })
                .then(response => {
                    return self.listItensProdutos = response.data;
                })
                .catch(error => {
                    // Lidar com erros aqui
                    console.error("Erro: " + error);
                    return [];
                });
        },
        salvar() {
            //Armazenar a referência ao 'this'
            let self = this;

            // Verificar se a lista de itens de produtos está vazia
            if (self.listItensProdutos.length === 0) {
                Swal.fire("Atenção!", "Por favor, insira pelo menos um produto antes de salvar.", "warning");
                return;
            }

            // Verifique se há itens com quantidade igual a zero
            const itensComQuantidadeZero = self.listItensProdutos.some(item => item.quantidade_venda_produto === 0);

            // Se houver itens com quantidade igual a zero, exiba uma mensagem e retorne sem salvar
            if (itensComQuantidadeZero) {
                Swal.fire("Atenção!", "Existem itens com quantidade igual a zero. <br>Por favor, ajuste antes de salvar.", "warning");
                return;
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

                // Crie um objeto com os dados do formulário, incluindo os totais
                let formData = {
                    param: self.isEditing ? 'alterar' : 'salvar',
                    id_venda: self.venda.id_venda,
                    listItensProdutos: self.listItensProdutos,
                    totalQuantidade: self.totalQuantidade,
                    totalValor: self.totalValor.replace(/\./g, '').replace(',', '.'),
                    totalImposto: self.totalImposto.replace(/\./g, '').replace(',', '.')
                };

                axios.post(urlRequisicao, formData, { headers })
                    .then(response => {
                        if (response.data.status === 200) {
                            Swal.fire({
                                title: response.data.title,
                                html: response.data.message,
                                type: response.data.type,
                                closeOnConfirm: false,
                                allowOutsideClick: false
                            }).then(function () {

                                self.listar();
                                self.limparFormulario();
                                self.fecharModal();

                            });
                        } else {
                            Swal.fire({
                                title: response.data.title,
                                html: response.data.message,
                                type: response.data.type,
                            })
                        }

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
                    return this.listVendas = response.data;
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
                    return this.listTipos = response.data.sort((a, b) => a.nome_tipo.localeCompare(b.nome_tipo));;
                })
                .catch(error => {
                    // Lidar com erros aqui
                    console.error("Erro: " + error);
                    return [];
                });
        },
        carregarProduto() {
            let self = this;

            // Redefinir o produto selecionado ao mudar o tipo de produto
            self.venda.id_produto = null;

            //Cabeçalho 
            const headers = {
                'Content-Type': 'multipart/form-data'
            };

            // Crie um objeto com os dados do formulário
            let formData = {
                param: 'carregarProduto',
                id_tipo: self.venda.id_tipo
            };

            axios.post(urlRequisicaoProduto, formData, { headers })
                .then(response => {
                    return self.listProdutos = response.data;
                })
                .catch(error => {
                    // Lidar com erros aqui
                    console.error("Erro: " + error);
                    return [];
                });
        },
        selecionarTipoPeloProduto() {
            let self = this;

            // Obtenha o tipo de produto associado ao produto selecionado
            const produtoSelecionado = self.listProdutos.find(produto => produto.id_produto === self.venda.id_produto);
            if (produtoSelecionado) {
                // Defina o tipo de produto associado como selecionado
                this.venda.id_tipo = produtoSelecionado.id_tipo;
            }
        },
       

    },
    computed: {
        modalTitle() {
            return this.isEditing ? 'Editar Venda' : 'Adicionar Venda';
        },
        totalQuantidade() {
            return this.listItensProdutos.reduce((total, item) => total + parseInt(item.quantidade_venda_produto), 0);
        },
        totalValor() {
            const total = this.listItensProdutos.reduce((total, item) => total + parseFloat(item.valor_total_produto_venda_produto.replace(/\./g, '').replace(',', '.')), 0);
            return total.toLocaleString('pt-BR', { minimumFractionDigits: 2 });
        },
        totalImposto() {
            const total = this.listItensProdutos.reduce((total, item) => total + parseFloat(item.valor_total_imposto_venda_produto.replace(',', '.')), 0);
            return total.toLocaleString('pt-BR', { minimumFractionDigits: 2 });
        }
    },
    watch: {
        // Assista a mudanças no ID do produto e carregue os detalhes do produto selecionado
        'venda.id_produto': {
            handler: 'carregarDetalhesProdutoSelecionado',
            immediate: true // Carregue imediatamente quando o componente for montado
        },
    },
    mounted() {

        // Referência ao componente Vue
        let self = this;

        self.listar();
    }
});