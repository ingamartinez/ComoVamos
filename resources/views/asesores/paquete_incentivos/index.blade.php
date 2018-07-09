@extends('layouts.dashboard')

@section('titulo', 'Gestión de usuarios')

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card-box" id="hide">

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @include('flash::message')

                <h2 class="header-title m-t-0 m-b-30">Formulario Validación de Paquetes</h2>
                <hr>

                <form id="form-agregar-linea" method="POST" autocomplete="off" action="#">
                    {{csrf_field()}}
                    <div class="row">
                        <div class="col-lg-6">
                            <h4 class="header-title m-t-0 m-b-10">Punto de Venta</h4>

                            <div class="row">
                                <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                                    <div class="form-group">
                                        <label for="idpdv">ID PDV*</label>
                                        <input type="number" name="idpdv" required placeholder="" value="" class="form-control" id="idpdv">
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                                    <div class="form-group">
                                        <label for="circuito">Circuito</label>
                                        <input type="text" name="circuito" class="form-control" id="circuito" disabled>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                                    <div class="form-group">
                                        <label for="name_idpdv">Nombre Punto de Venta</label>
                                        <input type="text" name="name_idpdv" class="form-control" id="name_idpdv" disabled>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                                    <div class="form-group">
                                        <label for="numero_contacto">Celular de Contacto al Cliente*</label>
                                        <input type="number" name="numero_contacto" class="form-control" id="numero_contacto" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <h4 class="header-title m-t-0 m-b-10">Validar Línea</h4>

                            <div class="row">
                                <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                                    <div class="form-group">
                                        <label for="linea">Linea de Incentivo*</label>
                                        <input id="linea" type="number" class="form-control" required>
                                    </div>
                                </div>

                                <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                                    <div class="form-group">
                                        <label for="tipo_paquete">Tipo de Paquete</label>
                                        <input type="text" name="tipo_paquete" class="form-control" id="tipo_paquete" disabled>
                                    </div>
                                </div>

                                <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                                    <div class="form-group">
                                        <label for="fecha_paquete">Fecha de Paquete</label>
                                        <input type="text" name="fecha_paquete" class="form-control" id="fecha_paquete" disabled>
                                    </div>
                                </div>

                                <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                                    <div class="form-group">
                                        <label for="estado">Estado</label>
                                        <input type="text" name="estado" class="form-control" id="estado" disabled>
                                    </div>
                                </div>

                                <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                                    <div class="form-group">
                                        <label for="fecha_activación">Fecha de Activación</label>
                                        <input type="text" name="fecha_activación" class="form-control" id="fecha_activación" disabled>
                                    </div>
                                </div>

                                {{--<div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">--}}
                                    {{--<div class="form-group">--}}
                                        {{--<label for="plan_welcome">Simcard Nueva</label>--}}
                                        {{--<input type="text" name="plan_welcome" class="form-control" id="plan_welcome" disabled>--}}
                                    {{--</div>--}}
                                {{--</div>--}}

                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="form-group text-left m-b-0">
                        <button class="btn btn-success waves-effect waves-light" type="submit" id="validar" formaction="{{route('paq.validar')}}">
                            Validar
                        </button>
                        <button class="btn btn-primary waves-effect waves-light" type="submit" id="guardar" formaction="{{route('paq.store')}}">
                            Agregar
                        </button>
                        <button type="reset" class="btn btn-default waves-effect waves-light m-l-5">
                            Limpiar
                        </button>
                    </div>
                </form>
            </div>
        </div><!-- end col -->
    {{--</div><div class="row">--}}
        {{--<div class="col-sm-12">--}}
            {{--<div class="card-box table-responsive">--}}
                {{--@if ($errors->any())--}}
                    {{--<div class="alert alert-danger">--}}
                        {{--<ul>--}}
                            {{--@foreach ($errors->all() as $error)--}}
                                {{--<li>{{ $error }}</li>--}}
                            {{--@endforeach--}}
                        {{--</ul>--}}
                    {{--</div>--}}
                {{--@endif--}}

                {{--@include('flash::message')--}}

                {{--<h4 class="header-title m-t-0 m-b-30">Mis Lineas</h4>--}}

                {{--<table id="datatable" class="table table-striped table-bordered">--}}
                    {{--<thead>--}}
                    {{--<tr>--}}
                        {{--<th>Movil Incentivo</th>--}}
                        {{--<th>Paquete</th>--}}
                        {{--<th>Fecha Paquete</th>--}}
                        {{--<th>Movil Contacto</th>--}}
                        {{--<th>ID PDV</th>--}}
                        {{--<th>Nombre PDV</th>--}}
                        {{--<th>Validado Sistemas</th>--}}
                        {{--<th>Validado Call Center</th>--}}
                        {{--<th>Creación</th>--}}
                        {{--<th>Última Modificación</th>--}}
                    {{--</tr>--}}
                    {{--</thead>--}}

                    {{--<tbody>--}}
                    {{--@foreach($paquetes as $paquete)--}}
                        {{--<tr data-id="{{$paquete->id}}" class="{{($paquete->trashed() ? 'danger': false)}}">--}}
                            {{--<td>{{$paquete->movil}}</td>--}}
                            {{--<td>{{$paquete->paquete}}</td>--}}
                            {{--<td>{{$paquete->fecha_paquete}}</td>--}}
                            {{--<td>{{$paquete->movil_contacto}}</td>--}}
                            {{--<td>{{$paquete->dms->idpdv}}</td>--}}
                            {{--@if(strlen($paquete->dms->nombre_punto)>=30)--}}
                                {{--<td>{{substr($paquete->dms->nombre_punto,'0','30')}}...</td>--}}
                            {{--@else--}}
                                {{--<td>{{$paquete->dms->nombre_punto}}</td>--}}
                            {{--@endif--}}

                            {{--<td>{{$paquete->validado_sistema ? "Si": "NO"}}</td>--}}
                            {{--<td>{{$paquete->validado_callcenter ? "Si": "NO"}}</td>--}}

                            {{--<td>{{$paquete->created_at->format('Y-m-d h:i:sa')}}</td>--}}
                            {{--<td>{{$paquete->updated_at->format('Y-m-d h:i:sa')}}</td>--}}

                        {{--</tr>--}}
                    {{--@endforeach--}}
                    {{--</tbody>--}}
                {{--</table>--}}

            {{--</div>--}}
        {{--</div><!-- end col -->--}}
    {{--</div>--}}
@endsection

@section('modals')

@endsection

@push('script')
    <script src="{{asset('assets/js/parsley.min.js')}}"></script>

    <script>
        $(document).ready(function() {
            moment.locale("es");
            $('#datatable').DataTable({
                "language": {
                    "url": "{{asset('assets/js/Spanish.json')}}"
                },
                "order": [[ 8, "desc" ]]
            });
        });

        $('#form-agregar-linea').on('submit', function (e) {
            e.preventDefault();
            var submit = $(this.id).context.activeElement;

            var id = submit.id;
            var url = submit.formAction;
            var idpdv=$(this).find('#idpdv').val();
            var linea=$(this).find('#linea').val();
            var numero_contacto=$(this).find('#numero_contacto').val();

            if(id==="validar"){
                validar(url,idpdv,linea);
            }else if(id==="guardar"){
                guardar(url,idpdv,linea,numero_contacto);
            }

        });

        function validar(url,idpdv,linea) {
            $.ajax({
                type: 'GET',
                data:{'idpdv': idpdv,'numero':linea},
                // data:{'numero':linea},
                url: url,
                beforeSend: function(){
                    $('#hide').block({
                        message: 'Validando Simcard en el Servidor',
                        css: {
                            border: 'none',
                            padding: '15px',
                            backgroundColor: '#000',
                            '-webkit-border-radius': '10px',
                            '-moz-border-radius': '10px',
                            opacity: .5,
                            color: '#fff'
                        }
                    });
                },
                complete: function(){
                    $('#hide').unblock();
                },
                success: function (data) {
                    var pdv=data.pdv;
                    var simcard=data.simcard;

                    $('#circuito').val(pdv.circuito);
                    $('#name_idpdv').val(pdv.nombre_punto);
                    $('#tipo_paquete').val(simcard.paquete.paquete_id);
                    if (simcard.paquete.fecha){
                        $('#fecha_paquete').val(moment(simcard.paquete.fecha).format('LLL'));
                    }else{
                        $('#fecha_paquete').val(simcard.paquete.fecha)
                    }
                    $('#estado').val(simcard.estado);
                    if (simcard.fecha_activacion) {
                        $('#fecha_activación').val(moment(simcard.fecha_activacion).format('LLL'));
                    }else{
                        $('#fecha_activación').val(simcard.fecha_activacion);
                    }
                    // $('#plan_welcome').val( (simcard.first_call) ? 'SI' : 'NO');

                    swal({
                        title: 'Correcto!',
                        html: "Simcard Valida para Incentivo: <b>"+simcard.paquete.paquete_id+"</b><br>"
                        +"Fecha Activación Simcard: <b>" +moment(simcard.fecha_activacion).format('LLL')+"</b>",
                        type: 'success',
                        showCancelButton: false,
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'Ok'
                    });
                },
                error(jqXHR, ajaxOptions, thrownError){
                    var pdv=jqXHR.responseJSON.pdv;
                    var simcard=jqXHR.responseJSON.simcard;

                    console.log(jqXHR, ajaxOptions, thrownError);

                    if (jqXHR.status === 404) {
                        $('#circuito').val(pdv.circuito);
                        $('#name_idpdv').val(pdv.nombre_punto);
                        $('#tipo_paquete').val(simcard.paquete.paquete_id);
                        if (simcard.paquete.fecha){
                            $('#fecha_paquete').val(moment(simcard.paquete.fecha).format('LLL'));
                        }else{
                            $('#fecha_paquete').val(simcard.paquete.fecha)
                        }
                        $('#estado').val(simcard.estado);
                        if (simcard.fecha_activacion) {
                            $('#fecha_activación').val(moment(simcard.fecha_activacion).format('LLL'));
                        }else{
                            $('#fecha_activación').val(simcard.fecha_activacion);
                        }
                        $('#plan_welcome').val( (simcard.first_call) ? 'SI' : 'NO');
                        swal(
                            'Ha ocurrido un error',
                            jqXHR.responseJSON.mensaje,
                            'error'
                        );
                    }else{
                        swal(
                            'Ha ocurrido un error',
                            jqXHR.responseJSON.mensaje,
                            'error'
                        );
                    }
                }
            });
        }

        function guardar(url,idpdv,linea,numero_contacto) {
            $.ajax({
                type: 'POST',
                data:{'idpdv': idpdv,'numero':linea,'numero_contacto':numero_contacto},
                // data:{'numero':linea},
                url: url,
                beforeSend: function(){
                    $('#hide').block({
                        message: 'Validando Simcard en el Servidor',
                        css: {
                            border: 'none',
                            padding: '15px',
                            backgroundColor: '#000',
                            '-webkit-border-radius': '10px',
                            '-moz-border-radius': '10px',
                            opacity: .5,
                            color: '#fff'
                        }
                    });
                },
                complete: function(){
                    $('#hide').unblock();
                },
                success: function (data) {
                    var pdv=data.pdv;
                    var simcard=data.simcard;

                    $('#circuito').val(pdv.circuito);
                    $('#name_idpdv').val(pdv.nombre_punto);
                    $('#tipo_paquete').val(simcard.paquete.paquete_id);
                    if (simcard.paquete.fecha){
                        $('#fecha_paquete').val(moment(simcard.paquete.fecha).format('LLL'));
                    }else{
                        $('#fecha_paquete').val(simcard.paquete.fecha)
                    }
                    $('#estado').val(simcard.estado);
                    if (simcard.fecha_activacion) {
                        $('#fecha_activación').val(moment(simcard.fecha_activacion).format('LLL'));
                    }else{
                        $('#fecha_activación').val(simcard.fecha_activacion);
                    }
                    // $('#plan_welcome').val( (simcard.first_call) ? 'SI' : 'NO');

                    swal({
                        title: 'Correcto!',
                        html: "Simcard Valida para Incentivo: <b>"+simcard.paquete.paquete_id+"</b><br>"
                        +"Fecha Activación Simcard: <b>" +moment(simcard.fecha_activacion).format('LLL')+"</b>",
                        type: 'success',
                        showCancelButton: false,
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'Ok'
                    }).then(function () {
                        $('#linea').val("");
                        $('#tipo_paquete').val("");
                        $('#fecha_paquete').val("");
                        $('#estado').val("");
                        $('#fecha_activación').val("");
                        // location.reload();
                    });

                },
                error(jqXHR, ajaxOptions, thrownError){
                    var pdv=jqXHR.responseJSON.pdv;
                    var simcard=jqXHR.responseJSON.simcard;

                    console.log(jqXHR, ajaxOptions, thrownError);

                    if (jqXHR.status === 404) {
                        $('#circuito').val(pdv.circuito);
                        $('#name_idpdv').val(pdv.nombre_punto);
                        $('#tipo_paquete').val(simcard.paquete.paquete_id);
                        if (simcard.paquete.fecha){
                            $('#fecha_paquete').val(moment(simcard.paquete.fecha).format('LLL'));
                        }else{
                            $('#fecha_paquete').val(simcard.paquete.fecha)
                        }
                        $('#estado').val(simcard.estado);
                        if (simcard.fecha_activacion) {
                            $('#fecha_activación').val(moment(simcard.fecha_activacion).format('LLL'));
                        }else{
                            $('#fecha_activación').val(simcard.fecha_activacion);
                        }
                        // $('#plan_welcome').val( (simcard.first_call) ? 'SI' : 'NO');
                        swal(
                            'Ha ocurrido un error',
                            jqXHR.responseJSON.mensaje,
                            'error'
                        );
                    }else{
                        swal(
                            'Ha ocurrido un error',
                            jqXHR.responseJSON.mensaje,
                            'error'
                        );
                    }
                }

            });
        }

    </script>
@endpush