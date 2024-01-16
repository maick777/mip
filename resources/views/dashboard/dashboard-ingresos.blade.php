@extends('crudbooster::admin_template')
@section('content')

<!-- Prev -->
@if(g('return_url'))
<p><a title='Return' href='{{g("return_url")}}'><i class='fa fa-chevron-circle-left '></i>
        &nbsp; {{cbLang("form_back_to_list",['module'=>CRUDBooster::getCurrentModule()->name])}}</a></p>
@else
<p><a title='Main Module' href='{{CRUDBooster::mainpath()}}'><i class='fa fa-chevron-circle-left '></i>
        &nbsp; {{cbLang("form_back_to_list",['module'=>CRUDBooster::getCurrentModule()->name])}}</a></p>
@endif
<!-- End Prev-->

<div class='panel panel-default'>
    <form id="form1" method='post' action='{{ CRUDBooster::mainpath('dashboard-ingresos') }}' enctype="multipart/form-data">
        {{ csrf_field() }}
        <div class='panel-heading'><strong><i class="fa fa-bar-chart"></i> INGRESOS POR AÑO/MES</strong></div>

        <div class='panel-body'>

            <div class="row">

                <div class='form-group col-lg-2'>
                    <label>Año</label>
                    <input type="number" id="anio" name="anio" value="{{ $anio }}" required class="form-control">
                </div>

                <div class='form-group col-lg-2'>
                    <label>Desde [Mes] *</label>
                    <select id="mes_inicio_id" name="mes_inicio_id" class="form-control" required>
                        <option value="">Todo</option>
                        @foreach($meses as $index => $mes)
                        <option {{ ($mes_inicio_id == $index)?'selected':'' }} value="{{$index}}">{{$mes}}</option>
                        @endforeach
                    </select>
                </div>

                <div class='form-group col-lg-2'>
                    <label>Hasta [Mes] *</label>
                    <select id="mes_fin_id" name="mes_fin_id" class="form-control" required>
                        <option value="">Todo</option>
                        @foreach($meses as $index => $mes)
                        <option {{ ($mes_fin_id == $index)?'selected':'' }} value="{{$index}}">{{$mes}}</option>
                        @endforeach
                    </select>
                </div>

                <div class='form-group col-lg-2' hidden>
                    <label>Moneda</label>
                    <select id="id_tipo_moneda" name="id_tipo_moneda" class="form-control" disabled>
                        <option value="">Todo</option>
                        @foreach($monedas as $moneda)
                        <option {{ ($monedas_id == $moneda->id)?'selected':'' }} value="{{$moneda->id}}">{{$moneda->simbolo}}</option>
                        @endforeach
                    </select>
                </div>

                <div class='form-group col-lg-1' style="width: 100px">
                    <label for="buscar">&nbsp;</label>
                    <button type='submit' class='btn btn-info btn-sm'><i class="fa fa-search"></i> Buscar</button>
                </div>

                <div class='form-group col-lg-1' style="width: 100px">
                    <label for="buscar">&nbsp;</label>

                    @empty(json_decode($values_categoria) || json_decode($values_tipo_cliente) || json_decode($values_genero))
                    @else
                    <button type='button' class='btn btn-danger btn-sm' id="descargar_reporte_por_mes"><i class="fa fa-download"></i> Descargar</button>
                    @endempty

                </div>

            </div>
            <div class="row">

                <div class='form-group col-lg-2'>
                    <label>Tipo de Ingreso</label>
                    <select id="id_tipo_ingresos" name="id_tipo_ingresos" class="form-control">
                        <option value="">Todo</option>
                        @foreach($tipo_ingresos as $tipo_aporte)
                        <option {{ ($id_tipo_ingresos == $tipo_aporte->id)?'selected':'' }} value="{{$tipo_aporte->id}}">{{$tipo_aporte->nombre}}</option>
                        @endforeach
                    </select>
                </div>

                <div class='form-group col-lg-4'>
                    <label>Miembro</label>
                    <select id="id_cliente" name="id_cliente" class="form-control select2">
                        <option value="">Todo</option>
                        @foreach($clientes as $cliente)
                        <option {{ ($id_cliente == $cliente->id)?'selected':'' }} value="{{$cliente->id}}">{{$cliente->apellidos}} {{$cliente->nombres}}</option>
                        @endforeach
                    </select>
                </div>

            </div>
            <div class="row">
                <div class="col-md-4">
                    <h4 class="text-center">- Ingresos mes a mes -</h4>
                    <table class='table table-striped' style='width: 100%'>
                        <thead>
                            <tr>
                                <th style="width: 50px;">#</th>
                                <th>MES</th>
                                <th style="text-align: right; width: 90px;">S/. MONTO</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($registros as $key => $registro)
                            <tr>
                                <td>{{ sprintf('%02d', $key + 1); }}</td>
                                <td style="text-transform: capitalize;">{{Carbon\Carbon::createFromDate(null, $registro->mes, 01)->locale('es')->monthName}}</td>
                                <td style="text-align: right;">{{ number_format($registro->monto, 2, '.', ',') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th></th>
                                <th style="text-align: right;">TOTAL S/.</th>
                                <th style="text-align: right;"> {{ number_format($registros->sum('monto'),2,".",",") }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="col-md-8">
                    <h4 class="text-center">- Resumen gráfico de ingresos -</h4>
                    <canvas id="char_aportes"></canvas>
                </div>
            </div>
        </div>

    </form>
</div>

<script src="{{ asset('/vendor/crudbooster/assets/adminlte/plugins/jQuery/jquery-2.2.3.min.js') }}"></script>
<script type="text/javascript" charset="utf8" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.6.0/Chart.bundle.min.js"></script>


<script>
    var labels = {!!$labels!!}
    var values = {!!$values!!}
</script>

<script src="{{ asset('js/script_aportes.js') }}"></script>
<script src="{{ asset('js/chart_label.js') }}"></script>


@endsection