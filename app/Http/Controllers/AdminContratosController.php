<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Session;
use Request;
use Illuminate\Support\Facades\DB;
use crocodicstudio\crudbooster\helpers\CRUDBooster as CRUDBooster;
use PhpParser\Node\Stmt\Else_;

class AdminContratosController extends \crocodicstudio\crudbooster\controllers\CBController
{

	public function cbInit()
	{

		# START CONFIGURATION DO NOT REMOVE THIS LINE
		$this->title_field = "id";
		$this->limit = "10";
		$this->orderby = "id,desc";
		$this->global_privilege = false;
		$this->button_table_action = true;
		$this->button_bulk_action = false;
		$this->button_action_style = "button_icon";
		$this->button_add = true;
		$this->button_edit = true;
		$this->button_delete = true;
		$this->button_detail = true;
		$this->button_show = false;
		$this->button_filter = false;
		$this->button_import = false;
		$this->button_export = false;
		$this->table = "contratos";
		# END CONFIGURATION DO NOT REMOVE THIS LINE

		# START COLUMNS DO NOT REMOVE THIS LINE
		$this->col = [];
		if (CRUDBooster::isUpdate() && $this->button_edit) {
			$this->col[] = ["label" => "", "name" => "id", "width" => "20", "callback" => function ($row) {
				return  '<a href="' . CRUDBooster::mainpath("edit/" . $row->id) . '" data-toggle="tooltip" title="'. trans("crudbooster.action_edit_data") .'" class="table-link"><i class="fa fa-pencil text-success"></i></a>';
			}];
		}
		$this->col[] = ["label" => "Referencia", "name" => "referencia", "width" => "200", "callback" => function ($row) {
			return (CRUDBooster::isRead() && $this->button_detail) ? '<a href="' . CRUDBooster::mainpath("detail/" . $row->id) . '" data-toggle="tooltip" title="'. trans("crudbooster.action_detail_data") .'" class="table-link">' . $row->referencia . '</a>' :  $row->referencia;
		}];
		$this->col[] = [
			"label" => "Trabajador", "name" => "id_trabajador",
			"callback" => function ($row) {
				$rows = DB::table('trabajadors')
					->where('id', "=", $row->id_trabajador)
					->select('nombre_completo')
					->first();
				return (CRUDBooster::isRead() && $this->button_detail) ? '<a href="' . CRUDBooster::mainpath("detail/" . $row->id) . '" data-toggle="tooltip" title="'. trans("crudbooster.action_detail_data") .'" class="table-link">' . $rows->nombre_completo . '</a>' :  $rows->nombre_completo;
			}
		];
		$this->col[] = [
			"label" => "Cargo", "name" => "id_tipo_cargo",
			"callback" => function ($row) {
				$rows = DB::table('tipo_cargos')
					->where('id', "=", $row->id_tipo_cargo)
					->select('nombre', 'color')
					->first();
				return $rows->nombre ? "$rows->nombre" : "";
			}
		];
		$this->col[] = [
			"label" => "Fec. Inicio", "name" => "fecha_inicio",
			"callback" => function ($row) {
				$fecha_inicio =   ($row->fecha_inicio) ?  strtoupper(Carbon::parse($row->fecha_inicio)->formatLocalized('%d %b %Y')) : '';
				return  strtoupper($fecha_inicio);
			}
		];
		$this->col[] = [
			"label" => "Progreso pago", "name" => "id_estado",
			"callback" => function ($row) {

				$pagosAll = DB::table('pagos')
					->where('id_contrato', "=", $row->id)
					->select('id')->get()->count();

				$pagosRealizados = DB::table('pagos')
					->where('id_contrato', "=", $row->id)
					->where('id_estado', "=", 1)
					->where('fecha_pago', "!=", NULL)
					->select('id')->get()->count();

				if ($pagosRealizados > 0) {
					$resultado = ($pagosRealizados / $pagosAll) * 100;
					$porcentaje = round($resultado, 0);
					$porcentaje = $porcentaje . '%';
					$progreso_descripcion = '';

					if ($porcentaje == 0) {
						$color = 'none';
						$porcentaje = '';
						$progreso_descripcion = '';
					} elseif ($porcentaje >= 1 && $porcentaje <= 99) {
						$color = 'warning';
						$progreso_descripcion = 'Progreso';
					} elseif ($porcentaje == 100) {
						$color = 'success';
						$progreso_descripcion = 'Completado';
					} else {
						$color = 'none';
					}
					return $row->id_estado ? "<div class='progress'>
						<div id='progress-import' class='progress-bar progress-bar-$color progress-bar-striped' role='progressbar' aria-valuenow='40'
							 aria-valuemin='0' aria-valuemax='$pagosAll' style='width: $porcentaje'>
							<span class='sr-only'>$porcentaje $progreso_descripcion </span>
						</div>
					</div>" : "";
				}
			}
		];
		$this->col[] = [
			"label" => "Fec. Fin", "name" => "fecha_fin",
			"callback" => function ($row) {
				$fecha_fin =   ($row->fecha_fin) ?  strtoupper(Carbon::parse($row->fecha_fin)->formatLocalized('%d %b %Y')) : '';
				return  strtoupper($fecha_fin);
			}
		];
		$this->col[] = [
			"label" => "Estado", "name" => "id_estado", "join" => "estados,estado_contrato",
			"callback" => function ($row) {
				$rows = DB::table('estados')
					->where('id', "=", $row->id_estado)
					->select('estado_contrato', 'color', 'icon')
					->first();
				return $row->id_estado ? "<i class='fa fa-$rows->icon text-$rows->color'></i> $rows->estado_contrato" : "";
			}
		];

		# END COLUMNS DO NOT REMOVE THIS LINE

		# START FORM DO NOT REMOVE THIS LINE
		$this->form = [];
		$this->form[] = ['label' => 'Referencia', 'name' => 'referencia', 'type' => 'text', 'validation' => 'required|min:1|max:70', 'width' => 'col-sm-6', 'placeholder' => '-Autogenerado-', 'disabled' => true];
		$this->form[] = ['label' => 'Trabajador', 'name' => 'id_trabajador', 'type' => 'select2', 'validation' => 'required|integer|min:0', 'width' => 'col-sm-6', 'datatable' => 'trabajadors,nombre_completo', 'datatable_where' => 'listable = ' . 1];
		$this->form[] = ['label' => 'Cargo', 'name' => 'id_tipo_cargo', 'type' => 'select', 'validation' => 'required', 'datatable' => 'tipo_cargos,nombre,nombre', 'width' => 'col-sm-4', 'value' => 4];

		if (CRUDBooster::getCurrentMethod() == "getEdit" || CRUDBooster::getCurrentMethod() == 'postEditSave'  || CRUDBooster::getCurrentMethod() == 'getDetail') {
			$this->form[] = ['label' => 'Estado', 'name' => 'id_estado', 'type' => 'select', 'validation' => 'required|integer|min:0', 'width' => 'col-sm-4', 'datatable' => 'estados,estado_contrato,id', 'value' => 1];
		}
		# PAGOS
		if (CRUDBooster::getCurrentMethod() == "getAdd" || CRUDBooster::getCurrentMethod() == 'postAddSave' || CRUDBooster::getCurrentMethod() == 'getDetail') {
			$this->form[] = ["label" => "Pagos", "type" => "header", "name" => "referencia", "collapsed" => true];
			$this->form[] = ['label' => 'Tipo Moneda', 'name' => 'id_moneda', 'type' => 'select',  'required|integer|min:0', 'width' => 'col-sm-3',  'datatable' => 'tipo_monedas,simbolo,id', 'value' => 1];
			$this->form[] = ['label' => 'Monto Pago', 'name' => 'monto', 'type' => 'number', 'validation' => 'required|numeric|min:0', 'step' => 0.01,  'width' => 'col-sm-3'];
			$this->form[] = ['label' => 'Periodo Pago', 'name' => 'id_periodo_pago', 'type' => 'select', 'validation' => 'required|integer|min:0', 'width' => 'col-sm-4', 'datatable' => 'periodo_pagos,nombre,id', 'value' => 2];
			$this->form[] = ['label' => 'Tiempo Contrato', 'name' => 'id_tiempo_contrato', 'type' => 'select', 'validation' => 'required|integer|min:0', 'width' => 'col-sm-4', 'datatable' => 'tiempo_contratos,nombre,id', 'value' => 1];
			$this->form[] = ['label' => 'Fec. Inicio', 'name' => 'fecha_inicio', 'type' => 'date', 'validation' => 'required|date', 'width' => 'col-sm-4'];
			//$this->form[] = ['label' => 'Fec. Fin', 'name' => 'fecha_fin', 'type' => 'date', 'validation' => 'required|date', 'width' => 'col-sm-4'];
			$this->form[] = ['label' => 'Listable', 'name' => 'listable', 'type' => 'hidden', 'value' => 0];
			$this->form[] = ['label' => 'User Create', 'name' => 'id_user_create', 'type' => 'hidden', 'validation' => 'integer|min:0', 'value' => CRUDBooster::myId()];
			$this->form[] = ['label' => 'User Update', 'name' => 'id_user_update', 'type' => 'hidden', 'validation' => 'integer|min:0',  'value' => CRUDBooster::myId()];
		}

		if (CRUDBooster::getCurrentMethod() == "getEdit" || CRUDBooster::getCurrentMethod() == 'postEditSave'  || CRUDBooster::getCurrentMethod() == 'getDetail') {
			$this->form[] = ['label' => 'Fec. Fin', 'name' => 'fecha_fin', 'type' => 'date', 'validation' => 'required|date', 'width' => 'col-sm-4'];
		}


		# END FORM DO NOT REMOVE THIS LINE

		/* 
	        | ---------------------------------------------------------------------- 
	        | Sub Module
	        | ----------------------------------------------------------------------     
			| @label          = Label of action 
			| @path           = Path of sub module
			| @foreign_key 	  = foreign key of sub table/module
			| @button_color   = Bootstrap Class (primary,success,warning,danger)
			| @button_icon    = Font Awesome Class  
			| @parent_columns = Sparate with comma, e.g : name,created_at
	        | 
	        */
		$this->sub_module = array();
		$this->sub_module[] = ['label' => '', 'title' => 'Pagos', 'path' => 'pagos_personal', 'parent_columns' => 'referencia', 'foreign_key' => 'id_contrato', 'button_color' => 'danger', 'button_icon' => 'fa fa-money', 'visible' => false];

		/* 
	        | ---------------------------------------------------------------------- 
	        | Add More Action Button / Menu
	        | ----------------------------------------------------------------------     
	        | @label       = Label of action 
	        | @url         = Target URL, you can use field alias. e.g : [id], [name], [title], etc
	        | @icon        = Font awesome class icon. e.g : fa fa-bars
	        | @color 	   = Default is primary. (primary, warning, succecss, info)     
	        | @showIf 	   = If condition when action show. Use field alias. e.g : [id] == 1
	        | 
	        */
		$this->addaction = array();


		/* 
	        | ---------------------------------------------------------------------- 
	        | Add More Button Selected
	        | ----------------------------------------------------------------------     
	        | @label       = Label of action 
	        | @icon 	   = Icon from fontawesome
	        | @name 	   = Name of button 
	        | Then about the action, you should code at actionButtonSelected method 
	        | 
	        */
		$this->button_selected = array();


		/* 
	        | ---------------------------------------------------------------------- 
	        | Add alert message to this module at overheader
	        | ----------------------------------------------------------------------     
	        | @message = Text of message 
	        | @type    = warning,success,danger,info        
	        | 
	        */
		$this->alert        = array();


		/* 
	        | ---------------------------------------------------------------------- 
	        | Add more button to header button 
	        | ----------------------------------------------------------------------     
	        | @label = Name of button 
	        | @url   = URL Target
	        | @icon  = Icon from Awesome.
	        | 
	        */
		$this->index_button = array();

		/* 
	        | ---------------------------------------------------------------------- 
	        | Customize Table Row Color
	        | ----------------------------------------------------------------------     
	        | @condition = If condition. You may use field alias. E.g : [id] == 1
	        | @color = Default is none. You can use bootstrap success,info,warning,danger,primary.        
	        | 
	        */
		$this->table_row_color = array();

		/*
	        | ---------------------------------------------------------------------- 
	        | You may use this bellow array to add statistic at dashboard 
	        | ---------------------------------------------------------------------- 
	        | @label, @count, @icon, @color 
	        |
	        */
		$this->index_statistic = array();

		/*
	        | ---------------------------------------------------------------------- 
	        | Add javascript at body 
	        | ---------------------------------------------------------------------- 
	        | javascript code in the variable 
	        | $this->script_js = "function() { ... }";
	        |
	        */

		$modulo = CRUDBooster::getCurrentMethod();



		if ($modulo == "getAdd" || $modulo == "getEdit") {
			$this->script_js = "

			$(function() {
	
				//INPUT TIPE DATE
				$('#fecha_inicio').prop(\"type\", \"date\");
				$('#fecha_fin').prop(\"type\", \"date\");

				let id_tiempo_contrato = $('#id_tiempo_contrato').val();

				$('#fecha_inicio').change(function () {

					$('#fecha_inicio').val()

					//var fechaArr = $('#fecha_inicio').val().split('-');
					//$('#fecha_fin').val([Number(fechaArr[0])+1, fechaArr[1], fechaArr[2]].join('-'));
				});

			})
			


			//var year = 2023; // Definir el año del contrato manualmente
			var year = moment().year();

			// Crear un objeto Moment.js para el 1 de enero del año del contrato
			var fechaInicio = moment().year(year).startOf('year');
			
			// Crear un objeto Moment.js para el 31 de diciembre del año del contrato
			var fechaFin = moment().year(year).endOf('year');
			
			// Mostrar las fechas en el formato deseado


			/*
			console.log('Fecha de inicio del contrato: ' + fechaInicio.format('DD/MM/YYYY'));
			console.log('Fecha de fin del contrato: ' + fechaFin.format('DD/MM/YYYY'));




						const fechaReferencia = moment('01-07', 'DD-MM');
						const fechaInicio = moment().subtract(1, 'year').set({month: 6, date: 1});
						const fechaFin = moment().set({month: 5, date: 30});
						const estaEnContrato = fechaReferencia.isAfter(fechaInicio) && fechaReferencia.isBefore(fechaFin);

						$('#contrato').html('Fecha de inicio: ' + fechaInicio.format('DD/MM/YYYY') + '<br>' +
                      'Fecha de fin: ' + fechaFin.format('DD/MM/YYYY') + '<br>' + 'La fecha de referencia ' + fechaReferencia.format('DD/MM/YYYY') + ' ' + (estaEnContrato ? 'está' : 'no está') + ' dentro del contrato.');

					  $('#fecha_fin').val(fechaFin.format('YYY-MM-DD'))

					  */


			


			
	
			";
		}


		/*
	        | ---------------------------------------------------------------------- 
	        | Include HTML Code before index table 
	        | ---------------------------------------------------------------------- 
	        | html code to display it before index table
	        | $this->pre_index_html = "<p>test</p>";
	        |
	        */
		$this->pre_index_html = null;



		/*
	        | ---------------------------------------------------------------------- 
	        | Include HTML Code after index table 
	        | ---------------------------------------------------------------------- 
	        | html code to display it after index table
	        | $this->post_index_html = "<p>test</p>";
	        |
	        */
		$this->post_index_html = null;



		/*
	        | ---------------------------------------------------------------------- 
	        | Include Javascript File 
	        | ---------------------------------------------------------------------- 
	        | URL of your javascript each array 
	        | $this->load_js[] = asset("myfile.js");
	        |
	        */
		$this->load_js = array();
		$this->load_js[] = "https://momentjs.com/downloads/moment-with-locales.min.js";


		/*
	        | ---------------------------------------------------------------------- 
	        | Add css style at body 
	        | ---------------------------------------------------------------------- 
	        | css code in the variable 
	        | $this->style_css = ".style{....}";
	        |
	        */
		$this->style_css = NULL;



		/*
	        | ---------------------------------------------------------------------- 
	        | Include css File 
	        | ---------------------------------------------------------------------- 
	        | URL of your css each array 
	        | $this->load_css[] = asset("myfile.css");
	        |
	        */
		$this->load_css = array();
	}


	/*
	    | ---------------------------------------------------------------------- 
	    | Hook for button selected
	    | ---------------------------------------------------------------------- 
	    | @id_selected = the id selected
	    | @button_name = the name of button
	    |
	    */
	public function actionButtonSelected($id_selected, $button_name)
	{
		//Your code here
	}


	/*
	    | ---------------------------------------------------------------------- 
	    | Hook for manipulate query of index result 
	    | ---------------------------------------------------------------------- 
	    | @query = current sql query 
	    |
	    */
	public function hook_query_index(&$query)
	{
		//Your code here
		$id_periodo_pago  = 4; // ANUAL, MENSUAL, QUINCENAL, SEMANAL, DIARIO
		$periodo_pago     = DB::table('periodo_pagos')->where('id',  $id_periodo_pago)->first();
		$id_tiempo_contrato = 3; // EN NÚMEROS segun ID
		$tiempo_contrato  = DB::table('tiempo_contratos')
			->where('tiempo_contratos.id',  $id_tiempo_contrato)
			->join('periodo_pagos', 'tiempo_contratos.id_periodo_pago', 'periodo_pagos.id')
			->select('tiempo_contratos.nombre', 'tiempo_contratos.cantidad', 'periodo_pagos.nombre as periodo_pago')
			->first();
		$index = 1;
		$fecha_inicio = Carbon::create(2023, 10, 7);
		$cantidad_sumar = $tiempo_contrato->cantidad;

		switch ($periodo_pago->nombre) {
			case 'ANUAL': //PAGO
				//TIEMPO CONTRATO
				$cantidad_sumar = $cantidad_sumar;
				$fecha_inicio->modify('+1 year');
				break;
			case 'MENSUAL': //PAGO
				//TIEMPO CONTRATO
				if ($tiempo_contrato->periodo_pago == 'ANUAL') {
					$cantidad_sumar = $cantidad_sumar * 12;
				} elseif ($tiempo_contrato->periodo_pago == 'MENSUAL') {
					$cantidad_sumar = $cantidad_sumar;
				}
				break;
			case 'QUINCENAL': // PAGO
				//TIEMPO CONTRATO
				if ($tiempo_contrato->periodo_pago == 'ANUAL') {
					$cantidad_sumar = $cantidad_sumar * 12;
				} elseif ($tiempo_contrato->periodo_pago == 'MENSUAL') {
					$cantidad_sumar = $cantidad_sumar;
				}
				break;
			case 'SEMANAL': //PAGO
				//TIEMPO CONTRATO
				if ($tiempo_contrato->periodo_pago == 'ANUAL') {
					$cantidad_sumar = $cantidad_sumar * 12;
				} elseif ($tiempo_contrato->periodo_pago == 'MENSUAL') {
					$cantidad_sumar = $cantidad_sumar;
				}
				break;
			case 'DIARIO': //PAGO
				//TIEMPO CONTRATO
				if ($tiempo_contrato->periodo_pago == 'ANUAL') {
					$cantidad_sumar = $cantidad_sumar * 12;
				} elseif ($tiempo_contrato->periodo_pago == 'MENSUAL') {
					$cantidad_sumar = $cantidad_sumar * 30;
				}
				break;
			case 'POR ENTREGA':
				$cantidad_sumar = $tiempo_contrato->cantidad;
				break;
		}
		$index = 1;

		//echo ('X' . $tiempo_contrato->nombre . ' PAGO: ' . $periodo_pago->nombre . 'CANTIDAD TIEMPO' . $cantidad_sumar);

		while ($index <= $cantidad_sumar) {

			if ($periodo_pago->nombre == 'ANUAL') {
				/*
				$fecha_fin_anio = $fecha_inicio->copy()->endOfYear();
				$fecha_correlativo[] =  "Fin año $index: " . $fecha_fin_anio->toDateString() . "\n";
				$fecha_inicio->addYear();
				*/
				$fecha_correlativo[] =  "Año " . $fecha_inicio->format('Y') . ": " . $fecha_inicio->format('Y-m-d') . "\n";
				$fecha_inicio->modify('+1 year'); // Suma un año
			} elseif ($periodo_pago->nombre == 'MENSUAL') {

				//$fecha_inicio->addMonth(); // Agrega un mes a la fecha de inicio
				$fecha_fin_mes = $fecha_inicio->copy()->endOfMonth();
				$fecha_correlativo[] =  "Fin de mes $index: " . $fecha_fin_mes->toDateString() . "\n";
				$fecha_inicio->addMonth();
			} elseif ($periodo_pago->nombre == 'QUINCENAL') {
				$fecha_quincenal_15 = $fecha_inicio->copy()->day(15);
				$fecha_quincenal_fin_mes = $fecha_inicio->copy()->endOfMonth();
				$fecha_correlativo[] = "Fecha quincenal $index " . $fecha_quincenal_15->toDateString() . "\n";
				$fecha_correlativo[] = "Fecha quincenal $index " . $fecha_quincenal_fin_mes->toDateString() . "\n";
				$fecha_inicio->addMonth();
			} elseif ($periodo_pago->nombre == 'SEMANAL') {

				$contador_sabados = 0;
				while ($contador_sabados < $cantidad_sumar * 4) { // Multiplicamos por 4 porque hay aproximadamente 4 sábados por mes
					if ($fecha_inicio->dayOfWeek === Carbon::SATURDAY) {
						$fecha_correlativo[] = "Sábado: " . $fecha_inicio->toDateString() . "\n";
						$contador_sabados++;
					}
					$fecha_inicio->addDay(); // Avanzar al siguiente día
				}
			} elseif ($periodo_pago->nombre == 'DIARIO') {
				// Obtiene el siguiente día a partir de la fecha de inicio
				$siguiente_dia = $fecha_inicio->copy()->addDay();
				// Verifica si el día es domingo (en versiones anteriores de Carbon)
				if ($siguiente_dia->dayOfWeek == 0) {
					// Si es domingo, suma 1 día para evitarlo
					$siguiente_dia->addDays(1);
				}
				$fecha_correlativo[] =  "Día $index: " . $siguiente_dia->toDateString() . "\n";
				// Avanza al siguiente día
				$fecha_inicio = $siguiente_dia->copy();
			}
			$index++;
		}

		//dd($fecha_correlativo);
		/*
		while ($correlativo->lte($fecha_fin)) {

			//PAGOS
			$id_pagos   = DB::table('pagos')->where('id_trabajador',  $contrato->id_trabajador)->where('id_contrato', $id)->max('id');

			if ($id_pagos == null) {
				$id_pagos = 1;
			} else {
				$id_pagos = $id_pagos + 1;
			}

			if ($contrato->id_periodo_pago == 1) { //ANUAL
				if ($correlativo->year != $fecha_inicio->year) {
					$anual[] = $correlativo->year . Carbon::parse($correlativo)->isoFormat('--- dddd DD, MMMM, Y');
				}
			} elseif ($contrato->id_periodo_pago == 2) { //MENSUAL

				//dd($peridodo_pagos->dia_pago);

				if ($peridodo_pagos->dia_pago > 0) {

					$diff = $peridodo_pagos->dia_pago - $correlativo->day;
					// Si la fecha actual es anterior al día DIA_PAGO, agregar la cantidad de días restantes
					if ($diff > 0) {
						$correlativo->addDays($diff);
					}
					// Hacer algo con la fecha DIA_PAGO de ese mes o posterior a ella
					$fecha = $correlativo->toDateString();
				} else {

					//$fecha = $correlativo->endOfMonth()->isoFormat('Y-MM-D');
					$ultimo_dia_del_mes = $correlativo->copy()->addMonthNoOverflow()->subDay();
					$fecha = $ultimo_dia_del_mes->isoFormat('Y-MM-D');
				}
			} elseif ($contrato->id_periodo_pago == 3) { //QUINCENAL
				if ($correlativo->format('d') == '15' || $correlativo->format('d') == $correlativo->copy()->endOfMonth()->format('d')) {
					$quincenal[] = $correlativo->toDateString();
				}
				//$quincenal[] =  $fecha_inicio->toDateString() .  Carbon::parse($fecha_inicio)->isoFormat('--- dddd DD, MMMM, Y'); // Imprime la fecha en formato texto

			} elseif ($contrato->id_periodo_pago == 4) { //SEMANAL
				// Mientras la fecha sea menor o igual a la fecha final
				if ($correlativo->dayOfWeek === Carbon::SATURDAY) { // Verifica si es domingo
					$domingo[] = $correlativo->toDateString() . Carbon::parse($correlativo)->isoFormat('--- dddd DD, MMMM, Y'); // Imprime la fecha en formato texto
				}
			} elseif ($contrato->id_periodo_pago == 5) { //DIARIO

			} elseif ($contrato->id_periodo_pago == 6) { //POR ENTREGABLE

			} else {
			}


			$referencia = 'PAG-' . $fecha_inicio->copy()->endOfMonth()->format('YmdHis') . '-' . str_pad($index++, 4, "0", STR_PAD_LEFT);

			DB::table('pagos')->insert([
				'id_trabajador'   => $contrato->id_trabajador,
				'id_contrato'  => $id,
				'referencia' 	=> $referencia,
				'monto' 		=> $contrato->monto,
				'fecha' 		=> $fecha,
				'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
			]);

			if ($contrato->id_periodo_pago == 1) { //ANUAL
				$correlativo->addYear();
			} elseif ($contrato->id_periodo_pago == 2) { //MENSUAL
				if ($peridodo_pagos->dia_pago > 0) {
					// Agregar un mes completo para obtener la siguiente fecha 25
					$correlativo->addMonth();
					// Colocar la fecha en el primer día del mes para evitar errores
					$correlativo->startOfMonth();
				} else {
					//$correlativo->addMonthNoOverflow();
					$correlativo = $ultimo_dia_del_mes->copy()->addDay();
				}
			} elseif ($contrato->id_periodo_pago == 3) { //QUINCENAL
				$correlativo->addDay();
			} elseif ($contrato->id_periodo_pago == 4) { //SEMANAL

			} elseif ($contrato->id_periodo_pago == 5) { //DIARIO

			} elseif ($contrato->id_periodo_pago == 6) { //POR ENTREGABLE

			} else {
			}
		}
		*/
		//dd($anual);
		//dd($mensual);
		//dd($quincenal);
		//dd($semanal);




	}

	/*
	    | ---------------------------------------------------------------------- 
	    | Hook for manipulate row of index table html 
	    | ---------------------------------------------------------------------- 
	    |
	    */
	public function hook_row_index($column_index, &$column_value)
	{
		//Your code here
	}

	/*
	    | ---------------------------------------------------------------------- 
	    | Hook for manipulate data input before add data is execute
	    | ---------------------------------------------------------------------- 
	    | @arr
	    |
	    */
	public function hook_before_add(&$postdata)
	{
		//$last_inserted_id = DB::table('contratos')->insertGetId($postdata);
		//dd($id_contrato);
		//dd( $id_contrato.$postdata);
		//unset($postdata['monto']);
		//$postdata->except(['monto', 'fecha_fin']);
		//$postdata->remove('monto');
		//dd($postdata['id_periodo_pago']);
	}

	/* 
	    | ---------------------------------------------------------------------- 
	    | Hook for execute command after add public static function called 
	    | ---------------------------------------------------------------------- 
	    | @id = last insert id
	    | 
	    */
	public function hook_after_add($id)
	{
		$contrato  = DB::table('contratos')->where('id',  $id)->first();
		$periodo_pago     = DB::table('periodo_pagos')->where('id',   $contrato->id_periodo_pago)->first();
		$tiempo_contrato  = DB::table('tiempo_contratos')
			->where('tiempo_contratos.id',   $contrato->id_tiempo_contrato)
			->join('periodo_pagos', 'tiempo_contratos.id_periodo_pago', 'periodo_pagos.id')
			->select('tiempo_contratos.nombre', 'tiempo_contratos.cantidad', 'periodo_pagos.nombre as periodo_pago')
			->first();
		if (isset($contrato->fecha_inicio) && isset($contrato->id_periodo_pago)) {
			$index 			= 1;
			$fecha_inicio 	= Carbon::parse($contrato->fecha_inicio);
			$cantidad_sumar = $tiempo_contrato->cantidad;
			switch ($periodo_pago->nombre) {
				case 'ANUAL': //PAGO
					//TIEMPO CONTRATO
					$cantidad_sumar = $cantidad_sumar;
					$fecha_inicio->modify('+1 year');
					break;
				case 'MENSUAL': //PAGO
					//TIEMPO CONTRATO
					if ($tiempo_contrato->periodo_pago == 'ANUAL') {
						$cantidad_sumar = $cantidad_sumar * 12;
					} elseif ($tiempo_contrato->periodo_pago == 'MENSUAL') {
						$cantidad_sumar = $cantidad_sumar;
					}
					break;
				case 'QUINCENAL': // PAGO
					//TIEMPO CONTRATO
					if ($tiempo_contrato->periodo_pago == 'ANUAL') {
						$cantidad_sumar = $cantidad_sumar * 12;
					} elseif ($tiempo_contrato->periodo_pago == 'MENSUAL') {
						$cantidad_sumar = $cantidad_sumar;
					}
					break;
				case 'SEMANAL': //PAGO
					//TIEMPO CONTRATO
					if ($tiempo_contrato->periodo_pago == 'ANUAL') {
						$cantidad_sumar = $cantidad_sumar * 12;
					} elseif ($tiempo_contrato->periodo_pago == 'MENSUAL') {
						$cantidad_sumar = $cantidad_sumar * 4;
					}
					break;
				case 'DIARIO': //PAGO
					//TIEMPO CONTRATO
					if ($tiempo_contrato->periodo_pago == 'ANUAL') {
						$cantidad_sumar = $cantidad_sumar * 12;
					} elseif ($tiempo_contrato->periodo_pago == 'MENSUAL') {
						$cantidad_sumar = $cantidad_sumar * 30;
					}
					break;
				case 'POR ENTREGA':
					$cantidad_sumar = $tiempo_contrato->cantidad;
					break;
			}
			while ($index <= $cantidad_sumar) {
				if ($periodo_pago->nombre == 'ANUAL') {
					/*
					$fecha_fin_anio = $fecha_inicio->copy()->endOfYear();
					$fecha_correlativo[] =  "Fin año $index: " . $fecha_fin_anio->toDateString() . "\n";
					$fecha_inicio->addYear();
					*/
					$fecha_correlativo[] = $fecha_inicio->format('Y-m-d');
					$fecha_inicio->modify('+1 year'); // Suma un año
				} elseif ($periodo_pago->nombre == 'MENSUAL') {
					//$fecha_inicio->addMonth(); // Agrega un mes a la fecha de inicio
					$fecha_fin_mes 		= $fecha_inicio->copy()->endOfMonth();
					$fecha_correlativo[]  = $fecha_fin_mes;
					$fecha_inicio->addMonth();
				} elseif ($periodo_pago->nombre == 'QUINCENAL') {
					$fecha_quincenal_15 = $fecha_inicio->copy()->day(15);
					$fecha_quincenal_fin_mes = $fecha_inicio->copy()->endOfMonth();

					$fecha_correlativo[] = $fecha_quincenal_15->toDateString();
					$fecha_correlativo[] = $fecha_quincenal_fin_mes->toDateString();
					$fecha_inicio->addMonth();
				} elseif ($periodo_pago->nombre == 'SEMANAL') {
					$contador_sabados = 0;
					while ($contador_sabados < $cantidad_sumar) { // Multiplicamos por 4 porque hay aproximadamente 4 sábados por mes
						if ($fecha_inicio->dayOfWeek === Carbon::SATURDAY) {
							$fecha_correlativo[] = $fecha_inicio->toDateString();
							$contador_sabados++;
						}
						$fecha_inicio->addDay(); // Avanzar al siguiente día
					}
				} elseif ($periodo_pago->nombre == 'DIARIO') {
					// Obtiene el siguiente día a partir de la fecha de inicio
					$siguiente_dia = $fecha_inicio->copy()->addDay();
					// Verifica si el día es domingo (en versiones anteriores de Carbon)
					if ($siguiente_dia->dayOfWeek == 0) {
						// Si es domingo, suma 1 día para evitarlo
						$siguiente_dia->addDays(1);
					}
					$fecha_correlativo =  "Día $index: " . $siguiente_dia->toDateString() . "\n";
					// Avanza al siguiente día
					$fecha_inicio = $siguiente_dia->copy();
				}
				$index++;
			}
			foreach ($fecha_correlativo as $key => $value) {

				$key = $key + 1;
				$referencia = 'PAG-' . $fecha_inicio->format('Ymd') . '-' . str_pad($key++, 4, "0", STR_PAD_LEFT);
				DB::table('pagos')->insert([
					'id_trabajador' => $contrato->id_trabajador,
					'id_contrato'  	=> $id,
					'referencia' 	=> $referencia,
					'id_moneda'     => $contrato->id_moneda,
					'monto' 		=> $contrato->monto,
					'fecha' 		=> $value,
					'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
				]);
			}
		}
		//ACTUALIZAR REFERENCIA
		$trabajador   				= DB::table('trabajadors')->where('id',  $contrato->id_trabajador)->first();
		$primera_letra_nombre   = (substr($trabajador->nombres, 0, 1));
		$primera_letra_apellido = (substr($trabajador->apellidos, 0, 2));
		if ($primera_letra_apellido === 'Ñ') {
			$primera_letra_apellido = (substr($trabajador->apellidos, 0, 2));
		} else {
			$primera_letra_apellido = (substr($trabajador->apellidos, 0, 1));
		}
		$ceros         			= str_pad($id, 4, "0", STR_PAD_LEFT);
		$fecha_actual 			= Carbon::now()->format('YmdHis');
		$referencia 			= 'CTO-' . $primera_letra_nombre . $primera_letra_apellido . '-' . $fecha_actual . '-' . $ceros;
		$postdata['referencia'] = $referencia;
		DB::table('contratos')
			->where('id', '=', $id)
			->update([
				'referencia' => $referencia,
			]);
	}

	/* 
	    | ---------------------------------------------------------------------- 
	    | Hook for manipulate data input before update data is execute
	    | ---------------------------------------------------------------------- 
	    | @postdata = input post data 
	    | @id       = current id 
	    | 
	    */
	public function hook_before_edit(&$postdata, $id)
	{
		//Your code here
	}

	/* 
	    | ---------------------------------------------------------------------- 
	    | Hook for execute command after edit public static function called
	    | ----------------------------------------------------------------------     
	    | @id       = current id 
	    | 
	    */
	public function hook_after_edit($id)
	{
		//Your code here 

	}

	/* 
	    | ---------------------------------------------------------------------- 
	    | Hook for execute command before delete public static function called
	    | ----------------------------------------------------------------------     
	    | @id       = current id 
	    | 
	    */
	public function hook_before_delete($id)
	{

		$contrato = DB::table('contratos')->where('id', $id)->first();
		$estado_contrato = DB::table('estados')->where('id', $contrato->id_estado)->first();

		if ($contrato) {
			if ($estado_contrato->estado_contrato === 'FINALIZADO') {
				CRUDBooster::redirect(
					$_SERVER['HTTP_REFERER'],
					"No se puede eliminar porque el contrato ya está finalizado.",
					"warning"
				);
			} else {
				DB::table('pagos')->where('id_contrato', $contrato->id)->delete();
			}
		}
	}

	/* 
	    | ---------------------------------------------------------------------- 
	    | Hook for execute command after delete public static function called
	    | ----------------------------------------------------------------------     
	    | @id       = current id 
	    | 
	    */
	public function hook_after_delete($id)
	{
		//Your code here
	}

	//By the way, you can still create your own method in here... :) 


}
