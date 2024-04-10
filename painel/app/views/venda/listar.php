<!-- inclua primeiro o VueJS -->
<!-- <script src="https://unpkg.com/vue@latest"></script> -->

<!-- use a última versão do vue-select -->
<!-- <script src="https://unpkg.com/vue-select@3.20.3/dist/vue-select.js"></script> -->
<!-- <link rel="stylesheet" href="https://unpkg.com/vue-select@3.20.3/dist/vue-select.css"> -->


<div class="content-page">
    <div class="content">

        <!-- Start Content-->
        <div class="container-fluid">

            <div class="page-title-box">
                <h4 class="page-title">Dados da venda</h4>
            </div>

            <div class="card-box" id="appVenda">
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
                                <th>Data da venda</th>
                                <th>Quantidade produto</th>
                                <th>Valor total venda</th>
                                <th>Valor total imposto</th>
                                <th style="width: 20px;">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="listVendas.length === 0">
                                <td colspan="7" class="text-center">Nenhum registro encontrado...</td>
                            </tr>
                            <tr v-else v-for="(venda, index) in listVendas" :key="venda.id_venda">
                                <td>{{venda.id_venda}}</td>
                                <td>{{venda.data_venda}}</td>
                                <td>{{venda.quantidade_total_venda}}</td>
                                <td>R$ {{venda.valor_total_venda }}</td>
                                <td>R$ {{venda.valor_total_imposto_venda }}</td>
                                <td>
                                    <button class="btn btn-info" title="Visualizar/Editar" style="padding: 0.30rem 0.7rem;" @click="abrirModal('Editar', venda.id_venda)"><i class="fas fa-pencil-alt" style="font-size: 13px;"></i></button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Modal -->
                <div class="modal fade bs-example-modal-center bs-example-modal-lg" id="modalCadastro" role="dialog" aria-labelledby="modalCadastroLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="modalCadastroLabel">{{ modalTitle }}</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <!-- <form @submit.prevent="salvar"> -->

                                    <div class="row">
                                        <div class="col-lg-3">
                                            <div class="form-group">
                                                <label for="id_tipo">Tipo produto *</label>
                                                <v-select :options="listTipos" v-model="venda.id_tipo" label="nome_tipo" :reduce="option => option.id_tipo" placeholder="Selecionar tipo" @input="carregarProduto"></v-select>
                                            </div>
                                        </div>
                                        <div class="col-lg-9">
                                            <div class="form-group">
                                                <label for="id_produto">Produto *</label>
                                                <v-select :options="listProdutos" v-model="venda.id_produto" label="nome_produto" :reduce="option => option.id_produto" placeholder="Selecionar produto" @input="selecionarTipoPeloProduto"></v-select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-3">
                                            <div class="form-group">
                                                <label for="quantidade_produto">Quantidade *</label>
                                                <input type="number" class="form-control" id="quantidade_produto" name="venda[quantidade_produto]" v-model="venda
                                                .quantidade_produto" min="1" @input="validarNumeroInteiro($event)">
                                            </div>
                                        </div>
                                        <div class="col-lg-3">
                                            <div class="form-group">
                                                <label for="valor_produto">Valor do produto </label>
                                                <input type="text" class="form-control" id="valor_produto" name="venda[valor_produto]" v-model="venda
                                                .valor_produto" disabled>
                                            </div>
                                        </div>
                                        <div class="col-lg-3">
                                            <div class="form-group">
                                                <label for="valor_percentual_imposto">Valor do imposto </label>
                                                <input type="text" class="form-control" id="valor_percentual_imposto" name="venda[valor_percentual_imposto]" v-model="venda
                                                .valor_percentual_imposto" disabled>
                                            </div>
                                        </div>
                                        <div class="col-lg-3" style="margin-top: 28px;">
                                            <button title="Adicionar Produto" class="btn btn-primary" style="padding: 0.4rem 0.7rem;" @click.prevent="adicionarItemProduto"><i class="fa fa-plus" style="font-size: 13px;"></i> Adicionar produto</button>
                                        </div>
                                    </div>

                                    <div class="table-responsive">

                                        <table class="table table-bordered table-striped mb-0">
                                            <thead>
                                                <tr>
                                                    <th>Produto</th>
                                                    <th>Valor</th>
                                                    <th>Imposto</th>
                                                    <th>Qtd.</th>
                                                    <th>Subtotal Valor</th>
                                                    <th>Subtotal Imposto</th>
                                                    <th style="width: 10%;">Açoes</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-if="listItensProdutos.length === 0">
                                                    <td colspan="7" class="text-center">Nenhum registro encontrado...</td>
                                                </tr>
                                                <tr v-else v-for="(item, index) in listItensProdutos" :key="index">
                                                    <td>{{ item.nome_produto }}</td>
                                                    <td>R$ {{ item.valor_produto_venda_produto }}</td>
                                                    <td>R$ {{ item.valor_imposto_venda_produto }}</td>
                                                    <td><input type="number" class="form-control" v-model="item.quantidade_venda_produto" min="1" style="width: 95px; height: 30px;" @input="atualizarQuantidadeItemProduto(index, $event.target.value); validarNumeroInteiro($event)"></td>
                                                    <td>R$ {{ item.valor_total_produto_venda_produto }}</td>
                                                    <td>R$ {{ item.valor_total_imposto_venda_produto }}</td>
                                                    <td>
                                                        <button class="btn btn-danger" title="Excluir" style="padding: 0.2rem 0.5rem;" @click.prevent="removerItemProduto(index)"><i class="far fa-trash-alt" style="font-size: 13px;"></i></button>
                                                    </td>
                                                </tr>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td colspan="3" class="text-right"><b>Total:</b></td>
                                                    <td>{{totalQuantidade}}</td>
                                                    <td>R$ {{totalValor}}</td>
                                                    <td>R$ {{totalImposto}}</td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                    <hr>
                                    <button type="submit" class="btn btn-primary" @click.prevent="salvar"><i class="fas fa-save"></i> Salvar</button>
                                <!-- </form> -->
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
    'public/js/venda/vendaModule.js' => true // passando true ativa o module
];
