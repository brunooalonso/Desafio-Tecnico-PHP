<div class="content-page">
    <div class="content">

        <!-- Start Content-->
        <div class="container-fluid">

            <div class="page-title-box">
                <h4 class="page-title">Dados do produto</h4>
            </div>

            <div class="card-box" id="appProduto">
                <div class="row">
                    <div class="col-12">
                        <button class="btn btn-primary waves-effect width-md waves-light float-right" @click="abrirModal('Cadastrar')"><i class="fa fa-plus" aria-hidden="true"></i> Novo</button>
                    </div>
                </div>
                <hr>

                <div class="table-responsive">

                    <table class="table table-bordered table-striped mb-0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Produto</th>
                                <th>Preço Venda</th>
                                <th>Tipo</th>
                                <th style="width: 20px;">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="listProdutos.length === 0">
                                <td colspan="5" class="text-center">Nenhum registro encontrado...</td>
                            </tr>
                            <tr v-else v-for="(produto, index) in listProdutos" :key="produto.id_produto">
                                <td>{{produto.id_produto}}</td>
                                <td>{{produto.nome_produto}}</td>
                                <td>R$ {{produto.valor_venda_produto}}</td>
                                <td>{{produto.nome_tipo}}</td>
                                <td>
                                    <button class="btn btn-info" title="Visualizar/Editar" style="padding: 0.2rem 0.5rem;" @click="abrirModal('Editar', produto)"><i class="fas fa-pencil-alt" style="font-size: 13px;"></i></button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Modal -->
                <div class="modal fade bs-example-modal-center" id="modalCadastro" role="dialog" aria-labelledby="modalCadastroLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="modalCadastroLabel">{{ modalTitle }}</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form @submit.prevent="salvar">
                                <div class="form-group">
                                        <label for="id_tipo">Tipo produto *</label>
                                        <v-select :options="listTipos" v-model="produto.id_tipo" label="nome_tipo" :reduce="option => option.id_tipo" placeholder="Selecionar um tipo"></v-select>
                                    </div>
                                    <div class="form-group">
                                        <label for="nome_produto">Nome produto *</label>
                                        <input type="text" class="form-control" id="nome_produto" name="produto[nome_produto]" v-model="produto.nome_produto">
                                    </div>
                                    <div class="form-group">
                                        <label for="valor_venda_produto">Valor da venda *</label>
                                        <input type="text" class="form-control" id="valor_venda_produto" name="produto[valor_venda_produto]" v-model="produto.valor_venda_produto">
                                    </div>
                                    <hr>
                                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Salvar</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>

<?php
//Variavel para inserir o js no footer
$pushScript = [
    'public/js/produto/produtoModule.js' => true // passando true ativa o module
];
