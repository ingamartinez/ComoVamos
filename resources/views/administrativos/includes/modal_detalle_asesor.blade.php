<div id="modal_detalle_asesor" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h2 class="modal-title" id="nombre_asesor">Detalle Asesor</h2>
            </div>

            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-responsive" id="detalle-asesor">
                            <thead>
                            <tr>
                                <th>Cant.</th>
                                <th>Paquete</th>
                                <th>Valor</th>
                            </tr>
                            </thead>
                            <tbody>
                                {{--contenido del body--}}
                            </tbody>
                            <tfoot style="border-top: 2px solid #8d8d8d;">
                            <tr>
                                {{--contenido del footer--}}
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div><!-- /.modal -->

@push('script')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#modal_asignar_supervisor').validator();
        });

        $('.detalle_asesor').on('click', function (e) {
            e.preventDefault();
            var fila = $(this).parents('tr');
            var id = fila.data('id');
            var fecha_inicial = $("#fechaInicial").val();
            var fecha_final = $("#fechaFinal").val();
            var asesor = $("#nombre_asesor");
            var nombre_asesor = $(this).text();

            // alert(nombre_asesor);

            $.ajax({
                type: 'GET',
                url: '{{url('detalle-paquetes-vendedor')}}',
                data: {user:id,fechaInicial:fecha_inicial,fechaFinal:fecha_final},
                success: function (data) {

                    var valor = _.reduce(data, function(contador,item){ return item.valor + contador; }, 0);
                    var cantidad = _.reduce(data, function(contador,item){ return item.cantidad + contador; }, 0);

                    var table_body = $('#detalle-asesor > tbody');
                    table_body.empty();
                    var table_foot = $('#detalle-asesor > tfoot');
                    table_foot.empty();

                    _.each(data,function (item) {
                        table_body.append(
                            "<tr>" +
                            "<th scope='row'>"+item.cantidad+"</th>"+
                                "<td>"+item.paquete+"</td>" +
                                "<td> $"+item.valor+"</td>" +
                            "</tr>"
                        );
                    });

                    table_foot.append(
                        "<tr>" +
                            "<th>"+cantidad+"</th>"+
                            "<td></td>" +
                            "<th> $"+valor+"</th>" +
                        "</tr>"
                    );

                    asesor.empty();
                    asesor.text("Detalle Asesor: "+nombre_asesor);

                    $("#modal_detalle_asesor").modal('toggle');
                }
            });
        });

    </script>
@endpush