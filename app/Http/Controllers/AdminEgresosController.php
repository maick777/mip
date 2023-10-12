<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Session;
use Request;
use DB;
use CRUDBooster;
use Illuminate\Support\Facades\App;

class AdminEgresosController extends \crocodicstudio\crudbooster\controllers\CBController
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
		$this->table = "egresos";
		# END CONFIGURATION DO NOT REMOVE THIS LINE

		setlocale(LC_TIME, App::getLocale());

		# START COLUMNS DO NOT REMOVE THIS LINE
		$this->col = [];
		$this->col = [];
		if (CRUDBooster::isUpdate() && $this->button_edit) {
			$this->col[] = ["label" => "", "name" => "id", "width" => "20", "callback" => function ($row) {
				return  '<a href="' . CRUDBooster::mainpath("edit/" . $row->id) . '" class="table-link"><i class="fa fa-pencil text-success"></i></a>';
			}];
		}
		$this->col[] = ["label" => "Descripción", "name" => "nombre"];
		$this->col[] = ["label" => "Tipo", "name" => "id_tipo_egreso", "join" => "tipo_egresos,nombre"];
		$this->col[] = ["label" => "Cant.", "name" => "cantidad"];
		$this->col[] = ["label" => "Precio", "name" => "precio_total", "visible" => false];
		$this->col[] = [
			"label" => "Monto", "name" => "id_moneda", "join" => "tipo_monedas,nombre",
			"callback" => function ($row) {
				$rows = DB::table('tipo_monedas')
					->where('id', "=", $row->id_moneda)
					->select('simbolo', 'nombre', 'color')
					->first();
				return $row->precio_total ? "<span class='text-$rows->color' title='$rows->nombre'>$rows->simbolo</span> " . number_format($row->precio_total, 2, '.', ',') . " " : "";
			},
		];
		$this->col[] = [
			"label" => "Fec. Pago", "name" => "fecha_egreso", "callback" => function ($row) {
				$fecha_egreso =   ($row->fecha_egreso) ?  strtoupper(Carbon::parse($row->fecha_egreso)->formatLocalized('%d %b %Y')) : '';
				return  strtoupper($fecha_egreso);
			}
		];
		$this->col[] = ["label" => "Comprobante", "name" => "comprobante", "callback" => function ($row) {
			return ($row->comprobante) ? "<a href=" . asset($row->comprobante) . " target='_BLANK' title='Abrir' class='label label-primary'><i class=\"fa fa-external-link-square\"></i> Abrir</a> | 
			<a href=" . asset($row->comprobante) . " download title='Descargar' class='label label-danger'><i class=\"fa fa-download\"></i> Descargar</a>" : "";
		}];
		# END COLUMNS DO NOT REMOVE THIS LINE

		# START FORM DO NOT REMOVE THIS LINE
		$this->form = [];
		$this->form[] = ['label' => 'Tipo Gasto', 'name' => 'id_tipo_egreso', 'type' => 'select', 'validation' => 'required|integer|min:0', 'width' => 'col-sm-6', 'datatable' => 'tipo_egresos,nombre,ID'];
		$this->form[] = ['label' => 'Descripción', 'name' => 'nombre', 'type' => 'text', 'validation' => 'required|min:5|max:70', 'width' => 'col-sm-6'];
		$this->form[] = ['label' => 'Cantidad', 'name' => 'cantidad', 'type' => 'number', 'validation' => 'required|integer|min:0', 'width' => 'col-sm-3'];
		$this->form[] = ['label' => 'Precio Por Unidad', 'name' => 'precio_unidad', 'type' => 'number', 'validation' => 'required|numeric|min:0', 'step' => 0.01, 'width' => 'col-sm-3'];
		$this->form[] = ['label' => 'Precio Total', 'name' => 'precio_total', 'type' => 'number', 'validation' => 'required|numeric|min:0', 'step' => 0.01, 'width' => 'col-sm-3', 'readonly' => true];
		$this->form[] = ['label' => 'Tipo Moneda', 'name' => 'id_moneda', 'type' => 'hidden', 'value' => 1];
		$this->form[] = ['label' => 'Pagado con', 'name' => 'id_tipo_ingreso', 'type' => 'select', 'validation' => 'integer|min:0', 'width' => 'col-sm-4', 'datatable' => 'tipo_ingresos,nombre,id'];
		$this->form[] = ['label' => 'Fecha', 'name' => 'fecha_egreso', 'type' => 'date', 'validation' => 'required|date', 'width' => 'col-sm-4', 'value' => Carbon::now()->format("Y-m-d")];
		$this->form[] = ['label' => 'Estado', 'name' => 'id_estado', 'type' => 'hidden', 'value' => 1];
		$this->form[] = ['label' => 'Comprobante', 'name' => 'comprobante', 'type' => 'upload', "help" => "Recomendado: PDF, JPG, JPEG, PNG", 'validation' => 'max:25000', 'width' => 'col-sm-6'];
		$this->form[] = ['label' => 'Encargado', 'name' => 'id_responsable', 'type' => 'select2', 'validation' => 'required|integer|min:0', 'width' => 'col-sm-6', 'datatable' => 'trabajadors,nombres', 'datatable_format' => "nombres,' ',apellidos"];

		$this->form[] = ["label" => "Más detalles", "type" => "header", "name" => "celular", "collapsed" => false];
		$this->form[] = ['label' => 'Celular', 'name' => 'celular', 'type' => 'text', 'validation' => 'min:11|max:11', 'width' => 'col-sm-4'];
		$this->form[] = ['label' => 'Correo', 'name' => 'correo', 'type' => 'text', 'validation' => 'min:5|max:70', 'width' => 'col-sm-6'];
		$this->form[] = ['label' => 'Observacion', 'name' => 'observacion', 'type' => 'textarea', 'validation' => 'string|min:5|max:5000', 'width' => 'col-sm-6'];

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

			function calcularTotal() {
				let cantidad = $('#cantidad').val();
				let precio_unidad = $('#precio_unidad').val();
				let total = cantidad*precio_unidad;
				$('#precio_total').val(total);
				console.log(total);
			}

			function obtener_egreso(){

				$('#id_tipo_egreso').change(function(e) {
					e.preventDefault();
					let egreso_nombre = $('#id_tipo_egreso option:selected').text();
					$('#nombre').val(egreso_nombre + ' ');
					$('#nombre').focus();
				});
				
			}

			$(function() {

				$('#celular').inputmask('999-999-999');


				$('#fecha_egreso').prop(\"type\", \"date\");
				$('#precio_unidad').on('input', calcularTotal);
				$('#cantidad').on('input', calcularTotal);

				obtener_egreso();

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



}
