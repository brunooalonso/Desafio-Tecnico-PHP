<div class="content-page">
    <div class="content">

        <!-- Start Content-->
        <div class="container-fluid">

            <div class="page-title-box">
                <h4 class="page-title">Dados do imposto</h4>
            </div>

            <div class="card-box" id="appImpostos">
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
                                <th>Valor Percentual</th>
                                <th style="width: 20px;">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="listImpostos.length === 0">
                                <td colspan="3" class="text-center">Nenhum registro encontrado...</td>
                            </tr>
                            <tr v-else v-for="(imposto, index) in listImpostos" :key="imposto.id_imposto">
                                <td>{{imposto.id_imposto}}</td>
                                <td>R$ {{imposto.valor_percentual_imposto}}</td>
                                <td>
                                    <button class="btn btn-info" title="Visualizar/Editar" style="padding: 0.2rem 0.5rem;" @click="abrirModal('Editar', imposto)"><i class="fas fa-pencil-alt" style="font-size: 13px;"></i></button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>


                <!-- Modal -->

                <div class="modal fade bs-example-modal-center" id="modalCadastro" tabindex="-1" role="dialog" aria-labelledby="modalCadastroLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
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
                                        <label for="valor_percentual_imposto">Valor Percentual</label>
                                        <input type="text" class="form-control" id="valor_percentual_imposto" name="imposto[valor_percentual_imposto]" v-model="imposto.valor_percentual_imposto">
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
    'public/js/imposto/impostoModule.js' => true // passando true ativa o module
];
