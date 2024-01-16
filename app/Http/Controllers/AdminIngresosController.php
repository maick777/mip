<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Session;
use Request;
use Illuminate\Support\Facades\DB;
use PDF;
use crocodicstudio\crudbooster\helpers\CRUDBooster as CRUDBooster;
use Illuminate\Http\Request as HttpRequest;
use Excel;


class AdminIngresosController extends \crocodicstudio\crudbooster\controllers\CBController
{

	public function cbInit()
	{

		# START CONFIGURATION DO NOT REMOVE THIS LINE
		$this->title_field = "id";
		$this->limit = "10";
		$this->orderby =  array('fecha_pago' => 'desc');
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
		$this->table = "ingresos";
		# END CONFIGURATION DO NOT REMOVE THIS LINE
		setlocale(LC_ALL, 'es', 'ES');

		# START COLUMNS DO NOT REMOVE THIS LINE
		$this->col = [];
		$this->col = [];
		if (CRUDBooster::isUpdate() && $this->button_edit) {
			$this->col[] = ["label" => "", "name" => "id", "width" => "20", "callback" => function ($row) {
				return  '<a href="' . CRUDBooster::mainpath("edit/" . $row->id) . '" data-toggle="tooltip" title="' . trans("crudbooster.action_edit_data") . '" class="table-link"><i class="fa fa-pencil text-success"></i></a>';
			}];
		}
		$this->col[] = ["label" => "Referencia", "name" => "referencias", "callback" => function ($row) {
			return (CRUDBooster::isRead() && $this->button_detail) ? '<a href="' . CRUDBooster::mainpath("detail/" . $row->id) . '" data-toggle="tooltip" title="' . trans("crudbooster.action_detail_data") . '" class="table-link">' . $row->referencias . '</a>' :  $row->referencias;
		}];

		$this->col[] = [
			"label" => "Tipo", "name" => "id_tipo_ingreso", "join" => "tipo_ingresos,nombre"];
		$this->col[] = ["label" => "Tipo", "name" => "id_tipo_ingreso", "join" => "tipo_ingresos,nombre", "visible" => false];
		$this->col[] = ["label" => "Monto", "name" => "monto", "visible" => false];
		$this->col[] = [
			"label" => "Monto", "name" => "id_moneda", "join" => "tipo_monedas,nombre",
			"callback" => function ($row) {
				$rows = DB::table('tipo_monedas')
					->where('id', "=", $row->id_moneda)
					->select('simbolo', 'nombre', 'color')
					->first();
				return $row->monto ? "<span class='text-$rows->color' title='$rows->nombre'>$rows->simbolo</span> " . number_format($row->monto, 2, '.', ',') . " " : "";
			},
		];
		$this->col[] = [
			"label" => "Fecha Ingr.", "name" => "fecha_pago", "callback" => function ($row) {
				$fecha_pago =   ($row->fecha_pago) ?  strtoupper(Carbon::parse($row->fecha_pago)->formatLocalized('%d %b %Y')) : '';
				return  strtoupper($fecha_pago);
			}
		];
		$this->col[] = ["label" => "Tipo Pago", "name" => "id_tipo_pago", "join" => "tipo_pagos,nombre"];

		/*
		$this->col[] = [Carbon::now()->format("Y-m-d")
			"label" => "Cliente", "name" => "id_trabajador",
			"callback" => function ($row) {
				$rows = DB::table('clientes')
					->where('id', "=", $row->id_trabajador)
					->select('nombres', 'apellidos')
					->first();
				$nombre_completo = $rows->nombres . ' ' . $rows->apellidos;
				return $nombre_completo;
			}
		];*/

		# END COLUMNS DO NOT REMOVE THIS LINE

		# START FORM DO NOT REMOVE THIS LINE
		$this->form = [];
		$this->form[] = ['label' => 'Tipo Ingreso', 'name' => 'id_tipo_ingreso', 'type' => 'select', 'validation' => 'required|integer|min:0', 'width' => 'col-sm-4', 'datatable' => 'tipo_ingresos,nombre,id', 'value' => 1];
		$this->form[] = ['label' => 'Referencias', 'name' => 'referencias', 'type' => 'text', 'validation' => 'required|min:1|max:70', 'width' => 'col-sm-6'];

		$this->form[] = ['label' => 'Monto', 'name' => 'monto', 'type' => 'number', 'validation' => 'required|numeric|min:0', 'step' => 0.01, 'width' => 'col-sm-3'];

		$this->form[] = ['label' => 'Tipo Moneda', 'name' => 'id_moneda', 'type' => 'hidden', 'value' => 1];
		$this->form[] = ['label' => 'Tipo Pago', 'name' => 'id_tipo_pago', 'type' => 'select', 'validation' => 'required|integer|min:0', 'width' => 'col-sm-4', 'datatable' => 'tipo_pagos,nombre,id', 'value' => 1];
		$this->form[] = ['label' => 'Fecha Ing.', 'name' => 'fecha_pago', 'type' => 'date', 'validation' => 'required|date', 'width' => 'col-sm-4', 'value' => Carbon::now()->format("Y-m-d")];
		$this->form[] = ['label' => 'Responsable', 'name' => 'id_responsable', 'type' => 'select2', 'validation' => 'required', 'width' => 'col-sm-6', 'datatable' => 'trabajadors,nombre_completo',  'value' => CRUDBooster::myId()];

		$this->form[] = ['label' => 'Observacion', 'name' => 'observacion', 'type' => 'textarea', 'validation' => 'string|min:5|max:5000', 'width' => 'col-sm-6'];
		$this->form[] = ['label' => 'Estado', 'name' => 'id_estado', 'type' => 'hidden', 'value' => 1];
		$this->form[] = ['label' => 'Listable', 'name' => 'listable', 'type' => 'hidden', 'value' => 0];

		$this->form[] = ['label' => 'User Create', 'name' => 'id_user_create', 'type' => 'hidden', 'validation' => 'integer|min:0', 'value' => CRUDBooster::myId()];
		$this->form[] = ['label' => 'User Update', 'name' => 'id_user_update', 'type' => 'hidden', 'validation' => 'integer|min:0',  'value' => CRUDBooster::myId()];

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

		if (CRUDBooster::getCurrentMethod() == "getIndex") {
			$this->index_button[] = ['label' => 'Ver Reportes', 'url' => CRUDBooster::mainpath("dashboard-ingresos"), "icon" => "fa fa-bar-chart"];
			$this->index_button[] = ['label' => 'Descargar Resumen', 'url' => CRUDBooster::mainpath("download-pdf-ingresos"), "icon" => "fa fa-download", "color" => "danger"];
		}



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


		if ($modulo == "getIndex") {
		}


		if ($modulo == "getAdd" || $modulo == "getEdit") {

			$this->script_js = "

			$(function() {

				$('#fecha_pago').prop('type', 'date');
				let id_tipo_ingreso = $('#id_tipo_ingreso option:selected').val();
				$('#id_clientes').attr('required','required');

				if(id_tipo_ingreso == 1){
					$('#form-group-id_clientes').show();
				}else {
					$('#form-group-id_clientes').hide();
					$('#form-group-referencias').show();
					$('#id_clientes').removeAttr('required');
				}
			
				$('#id_tipo_ingreso').change(function(e){
				
					let tipo_aporte = $('#id_tipo_ingreso option:selected').val(); // GET ID 
					$('#id_clientes').attr('required','required'); //ADD attr REQUIRED TO SELECT 
					$('#id_clientes option:first').prop('selected',true).trigger('change'); 

					if(tipo_aporte == 1){
						$('#referencias').val('');
						$('#form-group-id_clientes').show();
				
					}else {

						//RESET SELECT
						$('#id_clientes').removeAttr('required'); //DISABLED SELECT CLIENTE
						$('#form-group-id_clientes').hide(); //HIDE SELECT 

						let ingreso_referencia = $('#id_tipo_ingreso option:selected').text(); //GET TEXT 
						$('#referencias').val(ingreso_referencia + ' ');
						$('#referencias').focus();

					}
					
				})

				$('#id_clientes').change(function(e){
					let cliente_nombre = $('#id_clientes option:selected').text();
					$('#referencias').val(cliente_nombre);
				})

				
			


			

			
			})
			
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
		//Your code here
		if ($postdata['id_tipo_ingreso'] != 1) {
			//$postdata['id_clientes'] = 0;
		}
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
		//Your code here

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
		if ($postdata['id_tipo_ingreso'] != 1) {
			//$postdata['id_clientes'] = 0;
		}
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
		//Your code here

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

	/* 
	    | ---------------------------------------------------------------------- 
	    | DASHBOAR [GRﾃ：ICO]
	    | ----------------------------------------------------------------------     
	    | 
	*/

	public function getDashboardIngresos()
	{

		$anio = Carbon::now()->format('Y');
		$monedas = DB::table('tipo_monedas')->get();
		$monedas_id = 1;

		$meses = [
			'1' => 'Enero',
			'2' => 'Febrero',
			'3' => 'Marzo',
			'4' => 'Abril',
			'5' => 'Mayo',
			'6' => 'Junio',
			'7' => 'Julio',
			'8' => 'Agosto',
			'9' => 'Setiembre',
			'10' => 'Octubre',
			'11' => 'Noviembre',
			'12' => 'Diciembre',
		];

		$trabajadores   	= DB::table('trabajadors')->where('listable', '=', 0)->orderBy('nombres', 'asc')->get();
		$tipo_ingresos 	= DB::table('tipo_ingresos')->orderBy('id', 'asc')->get();





		$registros = DB::select('SELECT MONTH(a.fecha_ingreso) as mes, MONTHNAME(a.fecha_ingreso) as nombre,
								COUNT(a.monto) as cantidad, ROUND(SUM(a.monto),2) as monto
								FROM ingresos as a
								WHERE  YEAR(a.fecha_ingreso) = ' . $anio . '
								GROUP BY MONTH(a.fecha_ingreso), MONTHNAME(a.fecha_ingreso)
								ORDER BY MONTH(a.fecha_ingreso) ASC; ', ["anio" => $anio,  "monedas_id" => $monedas_id]);

		$data_array = array();



		foreach ($registros as $registro) {
			array_push($data_array, $registro);
		}

		$mes_nombre = array_column($data_array, 'mes');
		$data_array_label = array();
		foreach ($mes_nombre as $mes) {
			array_push($data_array_label, ucfirst(Carbon::createFromDate(null, $mes, 01)->locale('es')->monthName));
		}

		$labels = json_encode(array_column($data_array_label, null));
		$values = json_encode(array_column($data_array, 'monto'));

		$registros = collect($registros);

		$data = compact(
			'tipo_ingresos',
			'monedas',
			'monedas_id',
			'anio',
			'labels',
			'values',
			'registros',
			'trabajadores',
			"meses"
		);

		return view('dashboard.dashboard-ingresos', $data);
	}

	public function postDashboardIngresos(HttpRequest $request)
	{

		$anio = $request->anio;
		$monedas_id = 1;
		$monedas = DB::table('tipo_monedas')->get();

		$mes_inicio_id = $request->mes_inicio_id;
		$mes_fin_id = $request->mes_fin_id;

		$meses = [
			'1' => 'Enero',
			'2' => 'Febrero',
			'3' => 'Marzo',
			'4' => 'Abril',
			'5' => 'Mayo',
			'6' => 'Junio',
			'7' => 'Julio',
			'8' => 'Agosto',
			'9' => 'Setiembre',
			'10' => 'Octubre',
			'11' => 'Noviembre',
			'12' => 'Diciembre'
		];


		$trabajador   	= DB::table('trabajadors')->where('listable', '=', 0)->orderBy('nombres', 'asc')->get();
		$id_trabajador = $request->id_trabajador;


		$tipo_ingresos 	= DB::table('tipo_ingresos')->orderBy('nombre', 'asc')->get();
		$id_tipo_ingreso = $request->id_tipo_ingreso;


		$parametros = ["anio" => $anio, "id_moneda" => $monedas_id];
		$registros = DB::select('SELECT MONTH(a.fecha_ingreso) as mes, MONTHNAME(a.fecha_ingreso) as nombre,
								COUNT(a.monto) as cantidad, ROUND(SUM(a.monto),2) as monto
								FROM ingresos as a
								WHERE  YEAR(a.fecha_ingreso) = ' . $anio . '
								GROUP BY MONTH(a.fecha_ingreso), MONTHNAME(a.fecha_ingreso)
								ORDER BY MONTH(a.fecha_ingreso) ASC; ', ["anio" => $anio,  "monedas_id" => $monedas_id]);



		$query = "SELECT MONTH(a.fecha_ingreso) as mes, MONTHNAME(a.fecha_ingreso) as nombre, ";
		$query .= "COUNT(a.monto) as cantidad, ROUND(SUM(a.monto),2) as monto ";
		$query .= "FROM ingresos as a ";
		$query .= 'WHERE YEAR(a.fecha_ingreso) = ' . $anio . ' and a.id_moneda = ' . $monedas_id;

		if ($mes_inicio_id != '') {
			$query .= " AND MONTH(a.fecha_ingreso) >= $mes_inicio_id ";
			$parametros["mes_inicio_id"] = $mes_inicio_id;
		}

		if ($mes_fin_id != '') {
			$query .= " AND MONTH(a.fecha_ingreso) <= $mes_fin_id ";
			$parametros["mes_fin_id"] = $mes_fin_id;
		}

		if ($id_tipo_ingreso != '') {
			$query .= ' AND a.id_tipo_ingreso =  ' . $id_tipo_ingreso;
			$parametros["id_tipo_ingreso"] = $id_tipo_ingreso;
		}


		if ($id_trabajador != '') {
			$query .= ' AND a.id_clientes =  ' . $id_trabajador;
			$parametros["id_clientes"] = $id_trabajador;
		}

		$query .= " GROUP BY MONTH(a.fecha_ingreso), MONTHNAME(a.fecha_ingreso) ";
		$query .= "ORDER BY MONTH(a.fecha_ingreso) ASC ";

		//dd($query);

		$registros = DB::select($query, $parametros);
		$data_array = array();


		foreach ($registros as $registro) {
			array_push($data_array, $registro);
		}

		$labels = json_encode(array_column($data_array, 'nombre'));
		$values = json_encode(array_column($data_array, 'monto'));

		$registros = collect($registros);

		$data = compact(
			'tipo_ingresos',
			'id_tipo_ingreso',
			'monedas',
			'monedas_id',
			'anio',
			'labels',
			'values',
			'registros',
			'trabajador',
			'id_trabajador',
			'meses',
			'mes_inicio_id',
			'mes_fin_id'
		);
		return view('dashboard.dashboard-ingresos', $data);
	}

	/* 
	    | ---------------------------------------------------------------------- 
	    | DESCARGA DE ARCHIVOS PDF, EXCEL
	    | ----------------------------------------------------------------------     
	    | 
	*/
	public function downloadIngresosMes(HttpRequest $request)
	{
		$data = [];

		$anio = $request->anio;
		$monedas_id = 1;
		$monedas = DB::table('tipo_monedas')->get();

		$mes_inicio_id = $request->mes_inicio_id;
		$mes_fin_id = $request->mes_fin_id;

		$meses = [
			'1' => 'Enero',
			'2' => 'Febrero',
			'3' => 'Marzo',
			'4' => 'Abril',
			'5' => 'Mayo',
			'6' => 'Junio',
			'7' => 'Julio',
			'8' => 'Agosto',
			'9' => 'Setiembre',
			'10' => 'Octubre',
			'11' => 'Noviembre',
			'12' => 'Diciembre',
		];


		$clientes   	= DB::table('clientes')->where('listable', '=', 0)->orderBy('nombres', 'asc')->get();
		$id_clientes = $request->id_clientes;


		$tipo_ingresos 	= DB::table('tipo_ingresos')->orderBy('nombre', 'asc')->get();
		$id_tipo_ingreso = $request->id_tipo_ingreso;


		$parametros = ["anio" => $anio, "id_moneda" => $monedas_id];
		$registros = DB::select('SELECT MONTH(a.fecha_pago) as mes, MONTHNAME(a.fecha_pago) as nombre,
								COUNT(a.monto) as cantidad, ROUND(SUM(a.monto),2) as monto
								FROM ingresos as a
								WHERE  YEAR(a.fecha_pago) = ' . $anio . '
								GROUP BY MONTH(a.fecha_pago), MONTHNAME(a.fecha_pago)
								ORDER BY MONTH(a.fecha_pago) ASC; ', ["anio" => $anio,  "monedas_id" => $monedas_id]);
		$query = "SELECT MONTH(a.fecha_pago) as mes, MONTHNAME(a.fecha_pago) as nombre, ";
		$query .= "COUNT(a.monto) as cantidad, ROUND(SUM(a.monto),2) as monto ";
		$query .= "FROM ingresos as a ";
		$query .= 'WHERE YEAR(a.fecha_pago) = ' . $anio . ' and a.id_moneda = ' . $monedas_id;

		$queryFiltros = '';

		if ($mes_inicio_id != '') {
			$queryFiltros .= " AND MONTH(a.fecha_pago) >= $mes_inicio_id ";
			$parametros["mes_inicio_id"] = $mes_inicio_id;
		}

		if ($mes_fin_id != '') {
			$queryFiltros .= " AND MONTH(a.fecha_pago) <= $mes_fin_id ";
			$parametros["mes_fin_id"] = $mes_fin_id;
		}

		if ($id_tipo_ingreso != '') {
			$queryFiltros .= ' AND a.id_tipo_ingreso =  ' . $id_tipo_ingreso;
			$parametros["id_tipo_ingreso"] = $id_tipo_ingreso;
		}

		if ($id_clientes != '') {
			$query .= ' AND a.id_clientes =  ' . $id_clientes;
			$parametros["id_clientes"] = $id_clientes;
		}

		$query .= $queryFiltros;
		$query .= " GROUP BY MONTH(a.fecha_pago), MONTHNAME(a.fecha_pago) ";
		$query .= "ORDER BY MONTH(a.fecha_pago) ASC ";

		$primasMesesGeneral = DB::select($query, $parametros);
		//dd($queryPrimasPorMesesGeneral);exit();


		#obtenemos las primas por cada mes
		$total_prima = 0;
		foreach ($primasMesesGeneral as $mes) {
			$total_prima += $mes->monto;
			$queryPorMes = "a.referencias,  c.nombres, c.apellidos, a.monto, a.fecha_pago, ta.nombre as ingreso, a.observacion ";
			$queryPorMes .= "FROM ingresos as a ";
			$queryPorMes .= "LEFT JOIN tipo_ingresos as ta ON a.id_tipo_ingreso = ta.id ";
			$queryPorMes .= "LEFT JOIN clientes as c ON a.id_clientes = c.id ";

			$queryPorMes .= 'WHERE  YEAR(a.fecha_pago) = ' . $id_clientes . ' and a.monedas_id = ' . $monedas_id . ' ';
			if ($queryFiltros != '') {
				$queryPorMes .= $queryFiltros;
			}
			$queryPorMes .= "AND MONTH(a.fecha_pago) = '. $mes .' ORDER BY MONTH(a.fecha_pago) ASC ";
			$parametros["mes"] = $mes->mes;
			#$parametros["mes"] = 4;
			$IngresosMes = DB::select($queryPorMes, $parametros);

			$mes->polizas = $IngresosMes;
		}

		$datos = compact("primasMesesGeneral", "moneda", "total_prima");

		$xls = Excel::create('reporte_por_mes', function ($excel) use ($datos) {
			$excel->sheet('reporte_por_mes', function ($sheet) use ($datos) {
				$sheet->setColumnFormat(array(
					'C' => '0',
				));
				$sheet->loadView('reportes.reporte_ingreso_por_mes', $datos);
			});
		})->export('xlsx');
	}

	public function reporteExcelIngresos(IngresosExport $ingresosExport)
	{
		return $ingresosExport->forYear('2023')->download('ingresos.xlsx');
	}



	/* 
	    | ---------------------------------------------------------------------- 
	    | DESCARGA DE ARCHIVOS PDF, EXCEL
	    | ----------------------------------------------------------------------     
	    | 
	*/

	public function getDownloadPdfIngresos()
	{

		setlocale(LC_ALL, 'es_ES');


		//QUERY CATEGORIA

		$query_ingreso_general = 'SELECT MONTH(a.fecha_pago) as mes, a.id_tipo_ingreso, MONTHNAME(a.fecha_pago) as nombre, 
								ROUND(SUM(a.monto),2) as monto
								FROM ingresos as a
								LEFT JOIN tipo_ingresos AS ti ON a.id_tipo_ingreso = ti.id
								WHERE  YEAR(a.fecha_pago) = 2023
								GROUP BY MONTH(a.fecha_pago), MONTHNAME(a.fecha_pago), a.id_tipo_ingreso
								ORDER BY a.id_tipo_ingreso ASC, MONTH(a.fecha_pago) ASC; ';

		$ingresos = collect(DB::select($query_ingreso_general));

		//-------GRAN TOTAL--------------

		$subset = $ingresos->map(function ($reg) {
			return collect($reg)->only(['monto',])->all();
		});

		$total_monto_por_mes = $subset->sum(function ($row) {
			$total = 0;
			$total = $row['monto'];
			return $total;
		});

		$ingreso_general = $ingresos->groupBy([
			'tipo_ingreso', 'id_tipo_ingreso'
		]);


		$fecha =  strtoupper(Carbon::parse(now())->isoFormat('DD MMM Y'));

		$data = compact(
			'ingreso_general',
			'ingreso_por_mes',
			'fecha',
			'total_monto_por_mes'
		);

		//CARGANDO VISTA PDF
		$pdf = PDF::loadView('download.download_ingresos_pdf', $data);
		$now = Carbon::now();
		$fecha_descarga = $now->format('Ymd-His');
		//return $pdf->stream();
		return $pdf->download('RESUMEN-MIEMBROS-' . $fecha_descarga . CRUDBooster::myId() . '.pdf');
	}

	public function postDownloadExcelIngresos(HttpRequest $request)
	{

		$id_estados = $request->id_estados;
		$id_estados_civil = $request->id_estados_civil;

		$query_miembros = 'SELECT c.*, g.nombre AS genero, tc.nombre AS tipo_cliente, e.nombre AS estado, tm.nombre AS tipo_miembro, ec.nombre AS estado_civil
							FROM clientes AS c
							LEFT JOIN td_generos AS g ON c.id_genero = g.id
							LEFT JOIN td_tipo_clientes AS tc ON c.id_tipo_cliente = tc.id
							LEFT JOIN td_estados AS e ON c.id_estados = e.id
							LEFT JOIN td_tipo_miembros AS tm  ON c.id_tipo_miembro = tm.id
							LEFT JOIN td_estado_civils AS ec  ON c.id_estados_civil = ec.id
							WHERE c.listable = 0 ';

		if ($id_estados != '') {
			$query_miembros = $query_miembros . 'AND c.id_estados = ' . $id_estados . ' ';
		}
		if ($id_estados_civil != '') {
			$query_miembros = $query_miembros . 'AND c.id_estados_civil = ' . $id_estados_civil . ' ';
		}

		$query_miembros = $query_miembros . ' ORDER BY c.apellidos ASC;';
		$miembros 	= DB::select($query_miembros);

		$now = Carbon::now();
		$nombre_descarga = $now->format('Ymd-His');
		return Excel::download(new MiembrosExport($miembros), $nombre_descarga . '.xlsx');
	}
}
