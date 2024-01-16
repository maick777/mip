@extends('crudbooster::admin_template')
@section('content')

<!-- Prev -->
@if(g('return_url'))
<p><a title='Return' href='{{g("return_url")}}'><i class='fa fa-chevron-circle-left '></i>
        &nbsp; {{trans("crudbooster.form_back_to_list",['module'=>CRUDBooster::getCurrentModule()->name])}} </a></p>
@else
<p><a title='Main Module' href='{{CRUDBooster::mainpath()}}'><i class='fa fa-chevron-circle-left '></i>
        &nbsp; {{trans("crudbooster.form_back_to_list",['module'=>CRUDBooster::getCurrentModule()->name])}} </a></p>


@endif
<!-- End Prev-->

<div class='panel panel-default'>

    <form method='post' action='{{ CRUDBooster::mainpath('dashboard') }}' enctype="multipart/form-data">

        {{ csrf_field() }}
        <input type="hidden" id="myId" value="{{ CRUDBooster::myId()  }}">

        <div class='panel-heading'><strong><i class="fa fa-pie-chart"></i> Reporte de Trabajadores</strong></div>


        <div class='panel-body'>

            <div class="row">

                <div class='form-group col-lg-2'>
                    <label>Género</label>
                    <select name="id_genero" id="id_genero" class="form-control">
                        <option value="">TODO</option>
                        @foreach($generos as $genero)
                        <option {{ ($id_genero == $genero->id)?'selected':'' }} value="{{$genero->id}}">{{$genero->nombre}}</option>
                        @endforeach
                    </select>
                </div>

                <div class='form-group col-lg-2'>
                    <label>Estado</label>
                    <select name="id_estado" id="id_estado" class="form-control">
                        <option value="">TODO</option>
                        @foreach($estados as $estado)
                        <option {{ ($id_estado == $estado->id)?'selected':'' }} value="{{$estado->id}}">{{$estado->estado_activacion}}</option>
                        @endforeach
                    </select>
                </div>

                <div class='form-group col-lg-1' style="width: 100px">
                    <label for="buscar">&nbsp;</label>
                    <button type='submit' class='btn btn-info btn-sm'><i class="fa fa-search"></i> Buscar</button>
                </div>

                <div class='form-group col-lg-1' style="width: 100px">
                    <label for="buscar">&nbsp;</label>

                    @empty(json_decode($value_genero) || json_decode($value_estado) )

                    @else
                    <button type='button' class='btn btn-success btn-sm' id="excel_trabajadores"><i class="fa fa-file-excel-o"></i> Descargar</button>
                    @endempty

                </div>

            </div>

            <div class="row">
                
                <div class="col-md-3">
                    <h5 class="text-center">POR ESTADO</h5>
                    <canvas id="chart_estado"></canvas>
                </div>

                <div class="col-md-3">
                    <h5 class="text-center">POR GÉNERO</h5>
                    <canvas id="chart_genero"></canvas>
                </div>

            </div>

        </div>

    </form>

</div>


<script src="{{ asset('/vendor/crudbooster/assets/adminlte/plugins/jQuery/jquery-2.2.3.min.js') }}"></script>
<script type="text/javascript" charset="utf8" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.6.0/Chart.bundle.min.js"></script>

<script>

    var label_genero = {!!$label_genero!!}
    var value_genero = {!!$value_genero!!}

    var label_estado = {!!$label_estado!!}
    var value_estado = {!!$value_estado!!}

</script>

<script src="{{ asset('js/script_clientes.js') }}"></script>
<script src="{{ asset('js/chart_label.js') }}"></script>

@endsection