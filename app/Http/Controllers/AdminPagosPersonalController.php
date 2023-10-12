<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Session;
use Request;
use Illuminate\Support\Facades\DB;
use crocodicstudio\crudbooster\helpers\CRUDBooster as CRUDBooster;
use Illuminate\Support\Facades\App;


setlocale(LC_TIME, App::getLocale());

class AdminPagosPersonalController extends \crocodicstudio\crudbooster\controllers\CBController
{

	public function cbInit()
	{

		# START CONFIGURATION DO NOT REMOVE THIS LINE
		$this->title_field = "id";
		$this->limit = "10";
		$this->orderby = ['id_estado' => 'desc', 'fecha' => 'asc'];
		$this->global_privilege = false;
		$this->button_table_action = true;
		$this->button_bulk_action = true;
		$this->button_action_style = "button_icon";
		$this->button_add = true;
		$this->button_edit = true;
		$this->button_delete = true;
		$this->button_detail = true;
		$this->button_show = false;
		$this->button_filter = false;
		$this->button_import = false;
		$this->button_export = false;
		$this->table = "pagos";
		# END CONFIGURATION DO NOT REMOVE THIS LINE

		# START COLUMNS DO NOT REMOVE THIS LINE
		$this->col = [];

		if (CRUDBooster::isUpdate() && $this->button_edit) {
			$this->col[] = ["label" => "", "name" => "id", "width" => "20", "callback" => function ($row) {
				return  '<a href="' . CRUDBooster::mainpath("edit/" . $row->id) . '" class="table-link"><i class="fa fa-pencil text-success"></i></a>';
			}];
		}
		$this->col[] = ["label" => "Referencia", "name" => "referencia", "width" => "200", "callback" => function ($row) {
			return (CRUDBooster::isRead() && $this->button_detail) ? '<a href="' . CRUDBooster::mainpath("detail/" . $row->id) . '" class="table-link">' . $row->referencia . '</a>' :  $row->referencia;
		}];
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
			"label" => "Fec. Pago", "name" => "fecha",
			"callback" => function ($row) {
				$fecha =   ($row->fecha) ?  strtoupper(Carbon::parse($row->fecha)->formatLocalized('%d %b %Y ')) : '';
				return  strtoupper($fecha);
			}
		];



		$this->col[] = ["label" => "Archivo", "name" => "archivo", "visible" => false];
		$this->col[] = [
			"label" => "Estado de Pago", "name" => "id_estado", "join" => "estados,estado_pago",
			"callback" => function ($row) {
				$rows = DB::table('estados')
					->where('id', "=", $row->id_estado)
					->select('estado_pago', 'color', 'icon')
					->first();

				$archivo = $row->archivo ?  "<a href=' " . asset($row->archivo) . "' target='_blank' class='table-link' >&nbsp;&nbsp;<i class='fa fa-external-link text-primary'></i></a>" : "";

				if ($rows->estado_pago === 'PAGADO') {
					return $row->id_estado ? "<i class='fa fa-$rows->icon text-$rows->color'></i> $rows->estado_pago" . ' &nbsp;&nbsp; | &nbsp;&nbsp;<i class="fa fa-calendar-check-o text-success"></i> ' . strtoupper(Carbon::parse($row->fecha_pago)->formatLocalized('%d %b %Y')) . $archivo : "";
				} else {
					return $row->id_estado ? "<i class='fa fa-$rows->icon text-$rows->color'></i> $rows->estado_pago" : "";
				}
			}
		];
		# END COLUMNS DO NOT REMOVE THIS LINE

		# START FORM DO NOT REMOVE THIS LINE
		$this->form = [];
		$this->form[] = ['label' => 'Contrato', 'name' => 'id_contrato', 'type' => 'select2', 'validation' => 'required|integer|min:0', 'width' => 'col-sm-6', 'datatable' => 'contratos,referencia'];
		$this->form[] = ['label' => 'Referencia', 'name' => 'referencia', 'type' => 'text', 'validation' => 'required|min:1|max:255', 'width' => 'col-sm-6'];
		$this->form[] = ['label' => 'Tipo Moneda', 'name' => 'id_moneda', 'type' => 'select',  'required|integer|min:0', 'width' => 'col-sm-3',  'datatable' => 'tipo_monedas,simbolo,id', 'value' => 1];
		$this->form[] = ['label' => 'Monto', 'name' => 'monto', 'type' => 'money', 'validation' => 'required|integer|min:0', 'width' => 'col-sm-3'];
		$this->form[] = ['label' => 'Fecha', 'name' => 'fecha', 'type' => 'date', 'validation' => 'required|date', 'width' => 'col-sm-3'];
		$this->form[] = ["label" => "Estado de Pago", "type" => "header", "name" => "referencia", "collapsed" => true];
		$this->form[] = ['label' => 'Estado', 'name' => 'id_estado', 'type' => 'select2', 'validation' => 'required|integer|min:0', 'width' => 'col-sm-4', 'datatable' => 'estados,estado_pago', 'value' => 3];
		$this->form[] = ['label' => 'Fecha Pago', 'name' => 'fecha_pago', 'type' => 'date', 'validation' => 'date', 'width' => 'col-sm-3'];
		$this->form[] = ["label" => "Comprobante", "name" => "archivo", "type" => "upload", "help" => "Formato permitido Pdf, jpg, png o jpeg", 'validation' => 'max:2000', 'width' => 'col-sm-6'];
		$this->form[] = ['label' => 'Detalle/Observacion', 'name' => 'observacion', 'type' => 'textarea', 'validation' => 'string|min:5|max:5000', 'width' => 'col-sm-6'];
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
			
				$(function() {

					//INPUT TIPE DATE
					$('#fecha').prop(\"type\", \"date\");
					$('#fecha_pago').prop(\"type\", \"date\");

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
		$id_trabajador = DB::table('pagos')->join('contratos', 'contratos.id', 'pagos.id_contrato')->where('pagos.id', "=", $id)->select('contratos.id_trabajador')->first();
		if ($id_trabajador) {
			DB::table('pagos')->where('id', '=', $id)->update(['id_trabajador' => $id_trabajador->id_trabajador]);
		}
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
		$id_trabajador = DB::table('pagos')->join('contratos', 'contratos.id', 'pagos.id_contrato')->where('pagos.id', "=", $id)->select('contratos.id_trabajador')->first();
		if ($id_trabajador) {
			DB::table('pagos')->where('id', '=', $id)->update(['id_trabajador' => $id_trabajador->id_trabajador]);
		}
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
