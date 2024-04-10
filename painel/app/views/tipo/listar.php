<div class="content-page">
    <div class="content">

        <!-- Start Content-->
        <div class="container-fluid">

            <div class="page-title-box">
                <h4 class="page-title">Dados do tipo</h4>
            </div>

            <div class="card-box" id="appTipo">
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
                                <th>Tipo</th>
                                <th>Valor Percentual</th>
                                <th style="width: 20px;">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="listTipos.length === 0">
                                <td colspan="4" class="text-center">Nenhum registro encontrado...</td>
                            </tr>
                            <tr v-else v-for="(tipo, index) in listTipos" :key="tipo.id_tipo">
                                <td>{{tipo.id_tipo}}</td>
                                <td>{{tipo.nome_tipo}}</td>
                                <td>R$ {{tipo.valor_percentual_imposto}}</td>
                                <td>
                                    <button class="btn btn-info" title="Visualizar/Editar" style="padding: 0.2rem 0.5rem;" @click="abrirModal('Editar', tipo)"><i class="fas fa-pencil-alt" style="font-size: 13px;"></i></button>
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
                                        <label for="nome_tipo">Nome tipo *</label>
                                        <input type="text" class="form-control" id="nome_tipo" name="tipo[nome_tipo]" v-model="tipo.nome_tipo">
                                    </div>
                                    <div class="form-group">
                                        <label for="id_imposto">Valor Percentual *</label>
                                        <v-select :options="listImpostos" v-model="tipo.id_imposto" label="valor_percentual_imposto" :reduce="option => option.id_imposto" placeholder="Selecionar um imposto"></v-select>
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
    'public/js/tipo/tipoModule.js' => true // passando true ativa o module
];
