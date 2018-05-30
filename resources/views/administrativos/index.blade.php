@extends('layouts.dashboard')

@section('titulo', 'Gestión de usuarios')

@section('styles')
    <link href="{{url('assets/plugins/jquery-circliful/css/jquery.circliful.css')}}" rel="stylesheet" type="text/css"/>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="{{url('assets/css/bootstrap-material-datetimepicker.css')}}" rel="stylesheet" type="text/css"/>
@endsection

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card-box">
                @php(setlocale(LC_TIME, 'Spanish'))
                @php(\Carbon\Carbon::setUtf8(true))
                <h2>{{$fechaInicial->formatLocalized('%A %d %B %Y') }} -> {{$fechaFinal->formatLocalized('%A %d %B %Y') }}</h2>

                <form method="GET" action="{{url('resumen-incentivos')}}" id="formFiltrarFecha" role="form" data-toggle="validator" autocomplete="off" novalidate="true">
                    <div class="row">
                        <div class="col-lg-3 col-md-4">
                            <div class="form-group has-feedback has-success">
                                <label for="fechaInicial">Fecha Inicial</label>
                                <input type="text" class="form-control" id="fechaInicial" name="fechaInicial" placeholder="" required="" value="{{$fechaInicial->toDateString()}}" data-dtp="dtp_998o9">
                                <span class="glyphicon form-control-feedback glyphicon-ok" aria-hidden="true"></span>
                                <div class="help-block with-errors"></div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-4">
                            <div class="form-group has-feedback has-success">
                                <label for="fechaFinal">Fecha Final</label>
                                <input type="text" class="form-control" id="fechaFinal" name="fechaFinal" placeholder="" required="" value="{{$fechaFinal->toDateString()}}" data-dtp="dtp_acwE5">
                                <span class="glyphicon form-control-feedback glyphicon-ok" aria-hidden="true"></span>
                                <div class="help-block with-errors"></div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-4">
                            <div class="form-group">
                                <br>
                                <button type="submit" form="formFiltrarFecha" class="btn btn-success">Filtrar</button>
                            </div>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4 col-md-offset-1">
            <div class="card-box">

                <h3>Resumen Por Paquete</h3>

                <div class="table-responsive">
                    <table class="table m-0">
                        <thead>
                        <tr>
                            <th>Cant.</th>
                            <th>Paquete</th>
                            <th>Valor</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($resumenPorPaquete as $paquete)
                            <tr>
                                <th scope="row">{{$paquete->cantidad}}</th>
                                <td>{{$paquete->paquete}}</td>
                                <td>${{$paquete->valor}}</td>
                            </tr>
                        @endforeach

                        </tbody>
                        <tfoot style="border-top: 2px solid #8d8d8d;">
                        <tr>
                            <th>{{$resumenPorPaquete->sum('cantidad')}}</th>
                            <th></th>
                            <th>${{$resumenPorPaquete->sum('valor')}}</th>
                        </tr>
                        </tfoot>

                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-5 col-md-offset-0">
            <div class="card-box">

                <h3>Resumen Por Supervisor</h3>

                <div class="table-responsive">
                    <table class="table m-0">
                        <thead>
                        <tr>
                            <th>Cant.</th>
                            <th>Supervisor</th>
                            <th>Valor</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($resumenPorSupervisor as $supervisor)
                            <tr>
                                <th scope="row">{{$supervisor->cantidad}}</th>
                                <td>{{$supervisor->name}}</td>
                                <td>${{$supervisor->valor}}</td>
                            </tr>
                        @endforeach
                        </tbody>
                        <tfoot style="border-top: 2px solid #8d8d8d;">
                        <tr>
                            <th>{{$resumenPorSupervisor->sum('cantidad')}}</th>
                            <th></th>
                            <th>${{$resumenPorSupervisor->sum('valor')}}</th>
                        </tr>
                        </tfoot>

                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-9 col-lg-offset-1">
            <div class="card-box">

                <h3>Resumen Por Asesor</h3>

                <table class="table table-responsive">
                    <thead>
                    <tr>
                        <th>Cant.</th>
                        <th>Paquete</th>
                        <th>Valor</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($resumenPorAsesor as $asesor)
                        <tr>
                            <th scope="row">{{$asesor->cantidad}}</th>
                            <td>{{$asesor->name}}</td>
                            <td>${{$asesor->valor}}</td>
                        </tr>
                    @endforeach
                    </tbody>
                    <tfoot style="border-top: 2px solid #8d8d8d;">
                    <tr>
                        <th>{{$resumenPorAsesor->sum('cantidad')}}</th>
                        <th></th>
                        <th>${{$resumenPorAsesor->sum('valor')}}</th>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

@endsection

@push('script')
    <script src="{{asset('assets/js/bootstrap-material-datetimepicker.js')}}"></script>
    <script>
        $(document).ready(function() {
            moment.locale('es');
            $('#fechaFinal').bootstrapMaterialDatePicker({
                time: false,
                weekStart : 0,
                format: 'YYYY-MM-DD',
                setDate: moment()
            });
            $('#fechaInicial').bootstrapMaterialDatePicker({
                time: false,
                weekStart : 0,
                format: 'YYYY-MM-DD',
                setDate: moment()
            }).on('change', function(e, date) {
                $('#fechaFinal').bootstrapMaterialDatePicker('setMinDate', date);
            });
        });
    </script>
@endpush