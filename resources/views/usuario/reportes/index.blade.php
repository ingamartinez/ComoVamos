@extends('layouts.dashboard')

@section('titulo', 'Gestión de usuarios')

@section('content')
    <div class="row">
        {{--<h2 class="m-t-0 m-b-30">Reportes</h2>--}}
        <div class="col-lg-12">
            <div class="card-box">
                <div class="dropdown pull-right">
                    <a href="#" class="dropdown-toggle card-drop" data-toggle="dropdown" aria-expanded="false">
                        <i class="zmdi zmdi-more-vert"></i>
                    </a>
                    <ul class="dropdown-menu" role="menu">
                        <li><a href="{{url('#')}}">Reportar</a></li>
                    </ul>
                </div>

                <h4 class="header-title m-t-0 m-b-30">Reportes</h4>

                <form action="{{url('#')}}" method="POST" autocomplete="off" id="form_generar_reporte">
                    <div class="form-group col-lg-4">
                        <label for="zona" class="control-label">Zona</label>
                        <select id="zona" name="zona" class="form-control" required>
                            <option value="">Escoge una Zona</option>
                            @foreach($zonas as $zona)
                                <option value="{{$zona->nombre}}" >{{$zona  ->nombre}}</option>
                            @endforeach
                        </select>
                        <div class="help-block with-errors"></div>
                    </div>

                    <div class="form-group col-lg-4">
                        <label for="tipo_rango" class="control-label">Paso de Tiempo</label>
                        <select id="tipo_rango" name="tipo_rango" class="form-control" required>
                            <option value="">Escoja una serie</option>
                            <option value="5min">5 Minutos</option>
                            <option value="1h">1 Hora</option>
                            <option value="2h">5 Horas</option>
                        </select>
                        <div class="help-block with-errors"></div>
                    </div>

                    <div class="form-group col-lg-4">
                        <label for="fecha" class="control-label">Fecha</label>
                        <div class="input-group input-daterange">
                            <input type="text" class="form-control" name="inicio" data-date="" required>
                            <div class="input-group-addon">hasta</div>
                            <input type="text" class="form-control" name="fin" data-date="" required>
                        </div>
                        <div class="help-block with-errors"></div>
                    </div>

                    <button type="submit" class="btn btn-info waves-effect waves-light">Generar Reporte</button>
                </form>
            </div>
        </div><!-- end col -->
    </div>
    <div class="row">
        <h2 class="m-t-0 m-b-30">Reportes</h2>
        <div class="col-lg-12">
            <div class="card-box">
                <div class="dropdown pull-right">
                    <a href="#" class="dropdown-toggle card-drop" data-toggle="dropdown" aria-expanded="false">
                        <i class="zmdi zmdi-more-vert"></i>
                    </a>
                    <ul class="dropdown-menu" role="menu">
                        <li><a href="{{url('#')}}">Reportar</a></li>
                    </ul>
                </div>

                <h4 class="header-title m-t-0 m-b-30">Luz</h4>

                <canvas id="chart-luz" width="400" height="80"></canvas>

            </div>
        </div>
        <div class="col-lg-12">
            <div class="card-box">
                <div class="dropdown pull-right">
                    <a href="#" class="dropdown-toggle card-drop" data-toggle="dropdown" aria-expanded="false">
                        <i class="zmdi zmdi-more-vert"></i>
                    </a>
                    <ul class="dropdown-menu" role="menu">
                        <li><a href="{{url('#')}}">Reportar</a></li>
                    </ul>
                </div>

                <h4 class="header-title m-t-0 m-b-30">Temperatura</h4>

                <canvas id="chart-temperatura" width="400" height="80"></canvas>

            </div>
        </div>
        <div class="col-lg-12">
            <div class="card-box">
                <div class="dropdown pull-right">
                    <a href="#" class="dropdown-toggle card-drop" data-toggle="dropdown" aria-expanded="false">
                        <i class="zmdi zmdi-more-vert"></i>
                    </a>
                    <ul class="dropdown-menu" role="menu">
                        <li><a href="{{url('#')}}">Reportar</a></li>
                    </ul>
                </div>

                <h4 class="header-title m-t-0 m-b-30">Sonido</h4>

                <canvas id="chart-sonido" width="400" height="80"></canvas>

            </div>
        </div>
        <div class="col-lg-12">
            <div class="card-box">
                <div class="dropdown pull-right">
                    <a href="#" class="dropdown-toggle card-drop" data-toggle="dropdown" aria-expanded="false">
                        <i class="zmdi zmdi-more-vert"></i>
                    </a>
                    <ul class="dropdown-menu" role="menu">
                        <li><a href="{{url('#')}}">Reportar</a></li>
                    </ul>
                </div>

                <h4 class="header-title m-t-0 m-b-30">Movimiento</h4>

                <canvas id="chart-movimiento" width="400" height="80"></canvas>

            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        var luz;
        var temperatura;
        var sonido;
        var movimiento;
        $(document).ready(function() {
            $('.input-daterange').datepicker({
                language: 'es',
                todayHighlight: true,
                endDate:'now'
            });
            $('#form_generar_reporte').validator();

            let data1 = {
                labels: [],
                datasets: [
                    {
                        label: "",
                        fill: false,
                        lineTension: 0.1,
                        backgroundColor: "rgba(75,192,192,0.4)",
                        borderColor: "rgba(75,192,192,1)",
                        borderCapStyle: 'butt',
                        borderDash: [],
                        borderDashOffset: 0.0,
                        borderJoinStyle: 'miter',
                        pointBorderColor: "rgba(75,192,192,1)",
                        pointBackgroundColor: "#fff",
                        pointBorderWidth: 1,
                        pointHoverRadius: 5,
                        pointHoverBackgroundColor: "rgba(75,192,192,1)",
                        pointHoverBorderColor: "rgba(220,220,220,1)",
                        pointHoverBorderWidth: 2,
                        pointRadius: 5,
                        pointHitRadius: 10,
                        data: [],
                    }
                ]
            };
            let data2 = {
                labels: [],
                datasets: [
                    {
                        label: "",
                        fill: false,
                        lineTension: 0.1,
                        backgroundColor: "rgba(75,192,192,0.4)",
                        borderColor: "rgba(75,192,192,1)",
                        borderCapStyle: 'butt',
                        borderDash: [],
                        borderDashOffset: 0.0,
                        borderJoinStyle: 'miter',
                        pointBorderColor: "rgba(75,192,192,1)",
                        pointBackgroundColor: "#fff",
                        pointBorderWidth: 1,
                        pointHoverRadius: 5,
                        pointHoverBackgroundColor: "rgba(75,192,192,1)",
                        pointHoverBorderColor: "rgba(220,220,220,1)",
                        pointHoverBorderWidth: 2,
                        pointRadius: 5,
                        pointHitRadius: 10,
                        data: [],
                    }
                ]
            };
            let data3 = {
                labels: [],
                datasets: [
                    {
                        label: "",
                        fill: false,
                        lineTension: 0.1,
                        backgroundColor: "rgba(75,192,192,0.4)",
                        borderColor: "rgba(75,192,192,1)",
                        borderCapStyle: 'butt',
                        borderDash: [],
                        borderDashOffset: 0.0,
                        borderJoinStyle: 'miter',
                        pointBorderColor: "rgba(75,192,192,1)",
                        pointBackgroundColor: "#fff",
                        pointBorderWidth: 1,
                        pointHoverRadius: 5,
                        pointHoverBackgroundColor: "rgba(75,192,192,1)",
                        pointHoverBorderColor: "rgba(220,220,220,1)",
                        pointHoverBorderWidth: 2,
                        pointRadius: 5,
                        pointHitRadius: 10,
                        data: [],
                    }
                ]
            };
            let data4 = {
                labels: [],
                datasets: [
                    {
                        label: "",
                        fill: false,
                        lineTension: 0.1,
                        backgroundColor: "rgba(75,192,192,0.4)",
                        borderColor: "rgba(75,192,192,1)",
                        borderCapStyle: 'butt',
                        borderDash: [],
                        borderDashOffset: 0.0,
                        borderJoinStyle: 'miter',
                        pointBorderColor: "rgba(75,192,192,1)",
                        pointBackgroundColor: "#fff",
                        pointBorderWidth: 1,
                        pointHoverRadius: 5,
                        pointHoverBackgroundColor: "rgba(75,192,192,1)",
                        pointHoverBorderColor: "rgba(220,220,220,1)",
                        pointHoverBorderWidth: 2,
                        pointRadius: 5,
                        pointHitRadius: 10,
                        data: [],
                    }
                ]
            };

            let option = {
                showLines: true
            };
            luz = Chart.Line(document.getElementById('chart-luz'),{
                data:data1,
                options:option
            });
            temperatura = Chart.Line(document.getElementById('chart-temperatura'),{
                data:data2,
                options:option
            });
            sonido = Chart.Line(document.getElementById('chart-sonido'),{
                data:data3,
                options:option
            });
            movimiento = Chart.Line(document.getElementById('chart-movimiento'),{
                data:data4,
                options:option
            });
        });


        $('#form_generar_reporte').on('submit', function (e) {
            if (!e.isDefaultPrevented()) {
                e.preventDefault();

                $.ajax({
                    type: 'GET',
                    url: '{{url('reporte-zona')}}',
                    data: $(this).serialize(),
                    success: function (registros) {
                        luz.data.datasets[0].data=[];
                        luz.data.labels=[];
                        luz.data.datasets[0].label="Sensor Luz";

                        temperatura.data.datasets[0].data=[];
                        temperatura.data.labels=[];
                        temperatura.data.datasets[0].label="Sensor Temperatura";

                        sonido.data.datasets[0].data=[];
                        sonido.data.labels=[];
                        sonido.data.datasets[0].label="Sensor Sonido";

                        movimiento.data.datasets[0].data=[];
                        movimiento.data.labels=[];
                        movimiento.data.datasets[0].label="Sensor Movimiento";

                        _.each(registros, function(registro){
                            luz.data.datasets[0].data.push(registro.luz);
                            luz.data.labels.push(registro.created_at);

                            temperatura.data.datasets[0].data.push(registro.temperatura);
                            temperatura.data.labels.push(registro.created_at);

                            sonido.data.datasets[0].data.push(registro.sonido);
                            sonido.data.labels.push(registro.created_at);

                            movimiento.data.datasets[0].data.push(registro.movimiento);
                            movimiento.data.labels.push(registro.created_at);
                        });
                        luz.update();
                        temperatura.update();
                        sonido.update();
                        movimiento.update();
                    }
                });
            }
        });

    </script>

@endpush