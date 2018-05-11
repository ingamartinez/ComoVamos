@extends('layouts.dashboard')

@section('titulo', 'Gestión de usuarios')

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card-box table-responsive">
                <div class="dropdown pull-right">
                    <a href="#" class="dropdown-toggle card-drop" data-toggle="dropdown" aria-expanded="false">
                        <i class="zmdi zmdi-more-vert"></i>
                    </a>
                    <ul class="dropdown-menu" role="menu">
                        <li><a href="#">Action</a></li>
                        <li><a href="#">Another action</a></li>
                        <li><a href="#">Something else here</a></li>
                        <li class="divider"></li>
                        <li><a href="#">Separated link</a></li>
                    </ul>
                </div>

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

                <form role="form" action="{{route('paq.generarExcel')}}" method="POST">
                    {{csrf_field()}}
                    {{method_field('POST')}}

                    <div class="col-sm-4">
                        <div class="form-group">
                            <label for="asesor" class="control-label">Asesor</label>
                            <select id="asesor" name="asesor" class="form-control" required>
                                <option value="">Escoge un Asesor</option>
                                @foreach($asesores as $asesor)
                                    <option value="{{$asesor->id}}" >{{$asesor->name}}</option>
                                @endforeach
                            </select>

                            <div class="help-block with-errors"></div>
                        </div>
                        <div class="form-group text-left m-b-0">
                            <button class="btn btn-success waves-effect waves-light" type="submit" id="generar_excel">
                                Validar
                            </button>
                            <button type="reset" class="btn btn-default waves-effect waves-light m-l-5">
                                Limpiar
                            </button>
                        </div>
                    </div>
                </form>

            </div>
        </div><!-- end col -->
    </div>
@endsection

@section('modals')

@endsection

@push('script')

@endpush