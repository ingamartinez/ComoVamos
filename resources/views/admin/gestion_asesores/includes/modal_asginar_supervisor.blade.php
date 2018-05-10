<style>
    .checkbox input,.checkbox label{
        display:inline-block;
        vertical-align:middle;
    }
    .checkbox label{margin-right:20px;}
</style>


<div id="modal_asignar_supervisor" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title">Asignar Supervisor</h4>
            </div>

            <form method="POST" autocomplete="off" id="form_editar_usuario">

                {{csrf_field()}}

                <input type="hidden" name="id" id="modal_editar_usuario_id" value="">

                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name" class="control-label">Asesor</label>
                                <input type="text" class="form-control" id="modal_editar_usuario_name" name="name" required disabled>
                                <div class="help-block with-errors"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="supervisor" class="control-label">Supervisor</label>
                                <select id="modal_editar_supervisor" name="supervisor" class="form-control" required>
                                    <option value="">Escoge un Supervisor</option>
                                    @foreach($users as $user)
                                        @if ($user->hasRole('Supervisor'))
                                            <option value="{{$user->id}}" >{{$user->name}}</option>
                                            @continue
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label">Escoja los circuitos</label>
                                <br>
                                @foreach($circuitos as $circuito)
                                    <div class="col-lg-2 col-md-3 col-sm-3 col-xs-4">

                                        <div class="checkbox checkbox-success checkbox-inline">

                                            <input type="checkbox" name="check_circuito[]" id="check_circuito_{{$circuito->id}}" value="{{$circuito->id}}">
                                            <label for="check_circuito_{{$circuito->id}}">
                                                {{$circuito->name}}
                                            </label>

                                        </div>
                                    </div>
                                @endforeach
                                <div class="help-block with-errors"></div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-info waves-effect waves-light">Editar Usuario</button>
                </div>
            </form>
        </div>
    </div>
</div><!-- /.modal -->

@push('script')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#modal_asignar_supervisor').validator();
        });
    </script>

@endpush