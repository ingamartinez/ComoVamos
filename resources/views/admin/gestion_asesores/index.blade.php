@extends('layouts.dashboard')

@section('titulo', 'Gestión de usuarios')

@section('content')
    <div class="row">
        <div class="supervisor col-sm-12">
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

                <h2>Asesores sin asignar</h2>
                <ul>
                    @foreach($users as $user)
                        @if ($user->hasRole('Asesor') && $user->users_id==null)

                            <li style="display:inline-block;margin: 10px 10px 10px 0px">
                                <a data-id="{{$user->id}}" href="#" class="editar_supervisor">{{$user->name}}</a>
                                @foreach($user->roles()->get() as $roles)
                                    @if ($roles->name=='Asesor')
                                        @break
                                    @else
                                        <ul>
                                            <li >
                                                {{$roles->name}}
                                            </li>
                                        </ul>
                                    @endif
                                @endforeach
                            </li>

                            @continue
                        @endif
                    @endforeach
                </ul>

            </div>
        </div><!-- end col -->
    </div>

    <div class="grid row">
        @foreach($users as $supervisor)
            @if ($supervisor->hasRole('Supervisor'))
                <div class="supervisor col-sm-6">
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

                        <h3>Supervisor <span style="font-weight: 700">{{$supervisor->name}}</span></h3>

                        @foreach($users as $asesor)
                            @if ($asesor->users_id===$supervisor->id)
                                <ul>
                                    <li>
                                        <a data-id="{{$asesor->id}}" href="#" class="editar_supervisor">{{$asesor->name}}</a>


                                        @foreach($asesor->roles()->get() as $roles)
                                            @if ($roles->name=='Asesor')
                                                @break
                                            @else
                                                <ul>
                                                    <li>
                                                        {{$roles->name}}
                                                    </li>
                                                </ul>
                                            @endif
                                        @endforeach
                                    </li>
                                </ul>
                                @continue
                            @endif
                        @endforeach

                    </div>
                </div><!-- end col -->
                @continue
            @endif
        @endforeach
    </div>

@endsection

@section('modals')
    @include('admin.gestion_asesores.includes.modal_asginar_supervisor')
@endsection

@push('script')
<script>
    $(document).ready(function() {
        $('#datatable').DataTable({
            "language": {
                "url": "{{asset('assets/js/Spanish.json')}}"
            }
        });

        $('.grid').masonry({
            itemSelector: '.supervisor', // use a separate class for itemSelector, other than .col-
            percentPosition: true
        });
    });

    $('.editar_supervisor').on('click', function (e) {
        e.preventDefault();
        var id = $(this).data('id');
        $.ajax({
            type: 'GET',
            url: '{{url('gestion-usuarios')}}/' + id,
            success: function (data) {

                $('input:checkbox').each(function() {
                    $(this).prop("checked",false);
                });

                $('#modal_editar_usuario_id').val(data.id);
                $('#modal_editar_usuario_name').val(data.name);
                $('#modal_editar_supervisor').val(data.users_id);

                $.each(data.circuitos, function() {
                    $("input:checkbox[value=" + this.id + "]").prop("checked", "checked");
                });


                $("#modal_asignar_supervisor").modal('toggle');
            }
        });
    });


    $('#form_editar_usuario').on('submit', function (e) {
        if (!e.isDefaultPrevented()) {
            e.preventDefault();

            $.ajax({
                type: 'PUT',
                url: '{{url('asignar-supervisor')}}/',
                data: $('#form_editar_usuario').serialize(),
                success: function(){
                    swal({
                        title: 'Editado!',
                        text: "El usuario ha sido editado correctamente",
                        type: 'success',
                        showCancelButton: false,
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'Ok'
                    }).then(function () {
                        location.reload();
                    });
                },
                error:function (jqXHR, textStatus, errorThrown) {
                    console.log(jqXHR.responseText);
                    swal(
                        'Ha ocurrido un error',
                        jqXHR.responseText,
                        'error'
                    );

                }
            });
        }
    });

    $('.eliminar').on('click', function (e) {
        e.preventDefault();

        var fila = $(this).parents('tr');
        var id = fila.data('id');

        swal({
            title: 'Eliminar usuario',
            text: "¿Estas seguro de eliminar este usuario?",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#1ccc51',
            confirmButtonText: 'Si'
        }).then(function () {
            $.ajax({
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: '{{url('gestion-usuarios')}}/' + id,
                success: function (data) {
                    swal({
                        title: 'Eliminado!',
                        text: "El usuario ha sido eliminado.",
                        type: 'success',
                        showCancelButton: false,
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'Ok'
                    }).then(function () {
                        location.reload();
                    });
                },
                error:function (jqXHR, textStatus, errorThrown) {
                    console.log(jqXHR.responseText);
                    swal(
                        'Ha ocurrido un error',
                        jqXHR.responseText,
                        'error'
                    );

                }
            });
        });
    });

    $('.restaurar').on('click', function (e) {
        e.preventDefault();

        var fila = $(this).parents('tr');
        var id = fila.data('id');

        swal({
            title: 'Restaurar Usuario',
            text: "¿Esta seguro de restaurar este usuario?",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#1ccc51',
            confirmButtonText: 'Si'
        }).then(function () {
            $.ajax({
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: '{{url('restaurar-usuario')}}/' + id,
                success: function (data) {
                    swal({
                        title: 'Restaurado!',
                        text: "El usuario ha sido resaurado.",
                        type: 'success',
                        showCancelButton: false,
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'Ok'
                    }).then(function () {
                        location.reload();
                    });
                },
                error:function (jqXHR, textStatus, errorThrown) {
                    console.log(jqXHR.responseText);
                    swal(
                        'Ha ocurrido un error',
                        jqXHR.responseText,
                        'error'
                    );

                }
            });
        });
    });

</script>
@endpush