<?php

namespace App\Http\Controllers;

use App\Exports\trabajadorsExport;
use App\Models\Cliente;
use PDF;
use Carbon\Carbon;
use Session;
use Request;
use Illuminate\Support\Facades\DB;
use crocodicstudio\crudbooster\helpers\CRUDBooster as CRUDBooster;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Facades\App;
use Maatwebsite\Excel\Facades\Excel;
use PHPExcel_Style_Alignment;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

setlocale(LC_TIME, App::getLocale());

class AdminTrabajadoresController extends \crocodicstudio\crudbooster\controllers\CBController
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
		$this->button_action_style = "dropdown"; //button_icon
		$this->button_add = true;
		$this->button_edit = true;
		$this->button_delete = true;
		$this->button_detail = true;
		$this->button_show = false;
		$this->button_filter = false;
		$this->button_import = false;
		$this->button_export = false;
		$this->table = "trabajadors";
		# END CONFIGURATION DO NOT REMOVE THIS LINE
		//setlocale(LC_ALL, 'es_ES.UTF-8');

		# START COLUMNS DO NOT REMOVE THIS LINE
		$this->col = [];
		$this->col[] = ["label" => "Foto", "name" => "foto", "width" => "55", "image" => 1];
		if (CRUDBooster::isUpdate() && $this->button_edit) {
			$this->col[] = ["label" => "", "name" => "id", "width" => "20", "callback" => function ($row) {
				return  '<a href="' . CRUDBooster::mainpath("edit/" . $row->id) . '" data-toggle="tooltip" title="' . trans("crudbooster.action_edit_data") . '" class="table-link"><i class="fa fa-pencil text-success"></i></a>';
			}];
		}
		$this->col[] = ["label" => "Apellidos & Nombres", "name" => "nombre_completo", "callback" => function ($row) {
			return (CRUDBooster::isRead() && $this->button_detail) ? '<a href="' . CRUDBooster::mainpath("detail/" . $row->id) . '" data-toggle="tooltip" title="' . trans("crudbooster.action_detail_data") . '" class="table-link">' . $row->nombre_completo . '</a>' :  $row->nombre_completo;
		}];
		$this->col[] = ["label" => "Cargo", "name" => "id_tipo_cargo", "JOIN" => "tipo_cargos,nombre", 'visible' => false];
		$this->col[] = [
			"label" => "Cargo actual", "name" => "id_tipo_cargo",
			"callback" => function ($row) {
				$rows = DB::table('contratos')
					->JOIN('tipo_cargos', 'tipo_cargos.id', 'contratos.id_tipo_cargo')
					->where('contratos.id_trabajador', "=", $row->id)
					->select('tipo_cargos.nombre', 'tipo_cargos.color')
					->first();
				return $rows->nombre ? $rows->nombre : "";
			}
		];
		$this->col[] = ["label" => "N° Documento", "name" => "nro_documento", 'visible' => false];
		$this->col[] = [
			"label" => "Documento", "name" => "id_tipo_documento",
			"callback" => function ($row) {
				$rows = DB::table('tipo_documentos')
					->where('id', "=", $row->id_tipo_documento)
					->select('nombre')
					->first();

				return  $rows->nombre . ': ' . $row->nro_documento;
			}
		];
		$this->col[] = ["label" => "Género", "name" => "id_genero", "JOIN" => "generos,nombre", "visible" => false];
		$this->col[] = [
			"label" => "Género", "name" => "id_genero",
			"callback" => function ($row) {
				$rows = DB::table('generos')
					->where('id', "=", $row->id_genero)
					->select('nombre', 'icon', 'color')
					->first();
				return $row->id_genero ? "<i class='$rows->icon text-$rows->color'></i> $rows->nombre" : "";
			}
		];
		$this->col[] = ["label" => "Celular", "name" => "celular", "callback" => function ($row) {
			$celular = $row->celular;
			$celular = str_replace("-", "", $celular);
			return ($row->celular) ? "<a href='https://wa.me/+51$celular' target='_blank' data-toggle='tooltip' title='Enviar mensage' class='table-link'><i class=\"fa fa-whatsapp text-success\"></i>&nbsp;$row->celular</a>" : "&nbsp;";
		}];
		$this->col[] = [
			"label" => "Estado", "name" => "id_estado", "JOIN" => "estados,estado_activacion",
			"callback" => function ($row) {
				$rows = DB::table('estados')
					->where('id', "=", $row->id_estado)
					->select('estado_activacion', 'color', 'icon')
					->first();
				return $row->id_estado ? "<i class='fa fa-$rows->icon text-$rows->color'></i> $rows->estado_activacion" : "";
			}
		];
		# END COLUMNS DO NOT REMOVE THIS LINE

		# START FORM DO NOT REMOVE THIS LINE
		$this->form = [];
		$this->form[] = ['label' => 'Tipo Documento', 'name' => 'id_tipo_documento', 'type' => 'select', 'validation' => 'integer|min:0', 'width' => 'col-sm-4', 'datatable' => 'tipo_documentos,nombre,id', 'datatable_where' => 'id = ' . 1, 'value' => 1];
		$this->form[] = ['label' => 'Nro Documento', 'name' => 'nro_documento', 'type' => 'text', "help" => "Presione enter para buscar", 'validation' => 'sometimes|min:8|max:8|unique:trabajadors,nro_documento,', 'width' => 'col-sm-4'];
		$this->form[] = ['label' => 'Apellidos', 'name' => 'apellidos', 'type' => 'text', 'validation' => 'required|string|min:5|max:50', 'width' => 'col-sm-6'];
		$this->form[] = ['label' => 'Nombres', 'name' => 'nombres', 'type' => 'text', 'validation' => 'required|string|min:3|max:50', 'width' => 'col-sm-6'];
		$this->form[] = ['label' => 'Género', 'name' => 'id_genero', 'type' => 'select', 'validation' => 'required|integer|min:0', 'width' => 'col-sm-4', 'datatable' => 'generos,nombre,id'];
		$this->form[] = ['label' => 'Fecha Nacimiento', 'name' => 'fecha_nacimiento', 'type' => 'date', 'validation' => 'date|max:2023-01-01', 'width' => 'col-sm-4'];
		$this->form[] = ["label" => "Foto", "name"    => "foto", "type" => "upload", "help" => "Resolución recomendada 500x500px", 'validation' => 'file|max:2000', 'resize_width' => 500, 'resize_height' => 500, 'width' => 'col-sm-6'];

		# CONTACTO Y DIRECCIÓN
		$this->form[] = ["label" => "Contacto & Ubicación", "type" => "header", "name" => "correo", "collapsed" => false];
		$this->form[] = ['label' => 'Correo', 'name' => 'correo', 'type' => 'email', 'validation' => 'min:5|max:30', 'width' => 'col-sm-6'];
		$this->form[] = ['label' => 'Celular', 'name' => 'celular', 'type' => 'text', 'validation' => 'min:11|max:11',  'width' => 'col-sm-4'];
		$this->form[] = ['label' => 'Teléfono', 'name' => 'telefono', 'type' => 'text', 'validation' => 'min:2|max:20', 'width' => 'col-sm-4'];
		$this->form[] = ['label' => 'Pais', 'name' => 'id_pais', 'type' => 'select2', 'validation' => 'integer|min:0', 'width' => 'col-sm-4',  'datatable' => 'pais,pais', 'value' => 1];
		$this->form[] = ['label' => 'Departamento', 'name' => 'id_departamento', 'type' => 'select2', 'validation' => 'integer|min:0', 'width' => 'col-sm-4', 'datatable' => 'departamentos,departamento'];
		$this->form[] = ['label' => 'Provincia', 'name' => 'id_provincia', 'type' => 'select2', 'validation' => 'integer', 'width' => 'col-sm-4', 'datatable' => 'provincias,provincia', 'parent_select' => 'id_departamento'];
		$this->form[] = ['label' => 'Distrito', 'name' => 'id_distrito', 'type' => 'select2', 'validation' => 'integer', 'width' => 'col-sm-4', 'datatable' => 'distritos,distrito', 'parent_select' => 'id_provincia'];
		$this->form[] = ['label' => 'Dirección', 'name' => 'direccion', 'type' => 'text', 'validation' => 'min:5|max:70', 'width' => 'col-sm-6'];
		$this->form[] = ['label' => 'Detalle', 'name' => 'detalle', 'type' => 'textarea', 'validation' => 'string|min:5|max:1000', 'width' => 'col-sm-6'];

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
		$this->sub_module[] = ['label' => '', 'title' => 'Contratos', 'path' => 'contratos', 'parent_columns' => 'nombre_completo', 'foreign_key' => 'id_trabajador', 'button_color' => 'danger', 'button_icon' => 'fa fa-pencil-square-o', 'visible' => false];

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
		$this->addaction[] = ['label' => '',  'title' => 'Desactivar', 'icon' => 'fa fa-toggle-on', 'color' => 'success', 'url' => CRUDBooster::mainpath('desactivar/[id]'), 'showIf' => "[id_estado] == 1", 'confirmation' => true];
		$this->addaction[] = ['label' => '',  'title' => 'Activar', 'icon' => 'fa fa-toggle-off', 'color' => 'secondary', 'url' => CRUDBooster::mainpath('activar/[id]'), 'showIf' => "[id_estado] == 2", 'confirmation' => true];

		/* 
		    | ---------------------------------------------------------------------- 
		    | Add More Button Selected
		    | ----------------------------------------------------------------------     
		    | @label       = Label of action 
		    | @icon 	   = Icon FROM fontawesome
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
		    | @icon  = Icon FROM Awesome.
		    | 
		*/
		$this->index_button = array();
		if (CRUDBooster::getCurrentMethod() == "getIndex") {
			$this->index_button[] = ['label' => 'Dashboard', 'url' => CRUDBooster::mainpath("dashboard"), "icon" => "fa fa-bar-chart"];
			$this->index_button[] = ['label' => 'Resumen', 'url' => CRUDBooster::mainpath('pdf/[id]'), 'icon' => 'fa fa-file-pdf-o', 'color' => 'danger'];
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

		if ($modulo == "getAdd" || $modulo == "getEdit") {
			$this->script_js = "
			
				function ubigeos(){
						
					$('#id_departamento').change(function(e) {
						e.preventDefault();
						let departamento_id = $(this).val();
					
						$('#id_provincia').val(''); //devuelve a ** selecccione...
						$('#id_provincia').each(function () {
							$(this).select2();
						}); 

						//$('#id_distrito').select2('destroy');
						$('#id_distrito').val('');  //devuelve a ** selecccione... princiapl
						$('#id_distrito').each(function () {
							$(this).select2();
							$('#id_distrito').html('');
							$('#id_distrito').append(`<option value=''>** Seleccione un distrito </option>`); //Devuelve a ** Seleccione por segunda vez
						});
						getApiProvincias('#id_provincia',departamento_id);

					});
	
					$('#id_provincia').change(function(e) {
						e.preventDefault();
						let distrito_id = $(this).val();

						$('#id_distrito').val(''); //devuelve a ** selecccione...
						$('#id_distrito').each(function () {
							$(this).select2();
						}); 

						getApiDistritos('#id_distrito',distrito_id);

					});

				}

				function nro_documento(){

					let id_tipo_documento_get = $('#id_tipo_documento').val();

					if(id_tipo_documento_get == 1){
						$('#nro_documento').mask('99999999');
						console.log('dni');
					}else if(id_tipo_documento_get == 2){
						$('#nro_documento').mask('999999999999');
						console.log('Pasaporte');
					}else if(id_tipo_documento_get == 3){
						$('#nro_documento').mask('999999999999');
						console.log('carnet Ext');
					}else if(id_tipo_documento_get == 4){
						$('#nro_documento').mask('99999999999');
						console.log('RUC');
					}else {
						$('#nro_documento').mask('999999999999999');
						console.log('Otro');
					}

					$('#id_tipo_documento').change(function(e) {

						let id_tipo_documento = $(this).val();

						if(id_tipo_documento == 1){
							$('#nro_documento').mask('99999999');
							console.log('dni');
						}else if(id_tipo_documento == 2){
							$('#nro_documento').mask('999999999999');
							console.log('Pasaporte');
						}else if(id_tipo_documento == 3){
							$('#nro_documento').mask('999999999999');
							console.log('carnet Ext');
						}else if(id_tipo_documento == 4){
							$('#nro_documento').mask('99999999999');
							console.log('RUC');
						}else {
							$('#nro_documento').mask('999999999999999');
							console.log('Otro');
						}

						$('#nro_documento').focus();



					});

				}

				function buscar_por_dni() {
					const auth = 'Bearer 898fa86bb32b06e034f96985af9f192fbd080d5fc5b8bd5436e6fd39695c9df4';

					var dni = $('#nro_documento').val();
				
					if (dni == '') {
						alerta('error', 'Ingrese N° documento.');
					} else {
				
						if (dni.length < 8 || dni.length > 8) {
							alerta('error', 'Ingrese N° documento válido.');
						} else {
							$.ajax({
								url: 'https://apisperu.net/api/dni/' + dni,
								headers: {
									Authorization: auth
								},
								async: true,
								type: 'GET',
								dataType: 'json',
								beforeSend: function () {
									$('.loading').show();
								},
								success: function ({ data }) {
				
									if (!data || data === '') {
										alerta('error', 'N° documento no válido.');
										$('#nombres').val('');
										$('.loading').hide();
										$('#apellidos').val('');
										$('#nombres').val('');

									} else {
										alerta('success', 'Datos encontrados y cargados.');
										$('#apellidos').val(data.apellido_paterno + ' ' + data.apellido_materno);
										$('#nombres').val(data.nombres);
										$('.loading').hide();

									}
				
								},
								error: function (e) {
									alerta('error', 'Datos no encontrados.');
									$('.loading').hide();
									$('#apellidos').val('');
									$('#nombres').val('');
									console.log(e);

								},
							});
						}
				
				
				
				
				
					}
				
				}

				function validar_edad(){

					var fechaNacimiento = $('#fecha_nacimiento').val();
					var fechaActual = new Date();
					var fechaNacimientoDate = new Date(fechaNacimiento);
					var edad = fechaActual.getFullYear() - fechaNacimientoDate.getFullYear();
					if (edad < 18) {
						alert('Alerta: Menos de 18 años de edad.');
					} else {
						alert('OK');
					}
				
				};


				$(function() {

					//INPUT TIPE DATE
					$('#fecha_nacimiento').prop(\"type\", \"date\");
					$('#fecha_inicio').prop(\"type\", \"date\");
					$('#fecha_fin').prop(\"type\", \"date\");

					$('#fecha_inicio').change(function () {
						var fechaArr = $('#fecha_inicio').val().split('-');
						$('#fecha_fin').val([Number(fechaArr[0])+1, fechaArr[1], fechaArr[2]].JOIN('-'));
					});

				
					//MAX DATE
				
					//MASK
					$('#celular').inputmask('999-999-999');
					$('#nombres').mask('AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA',{
						translation: {
						  A: {pattern: /[a-zA-ZñÑáéíóúüÁÉÍÓÚÜ ]/} //todas las letras inglesas más la ñ y vocales acentuadas 
						}
					});
					$('#apellidos').mask('AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA',{
						translation: {
						  A: {pattern: /[a-zA-ZñÑáéíóúüÁÉÍÓÚÜ ]/} //todas las letras inglesas más la ñ y vocales acentuadas 
						}
					});
					$('#direccion').mask('AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA',{
						translation: {
							A: {pattern: /[0-9a-zA-ZñÑáéíóúüÁÉÍÓÚÜ #°._-]/} //todas las letras inglesas más la ñ y vocales acentuadas 
						}
					});
					  
					//CALCULAR EDAD
					ubigeos();
					nro_documento();
					//BUSCAR trabajadors
					$('#nro_documento').keydown(function(event){
						if (event.which == 13){
							event.preventDefault();

							buscar_por_dni();
						}
					});

				

				})

			";
		}

		if ($modulo == "getIndex") {

			$url_desactivar = CRUDBooster::mainpath('open-anular/');
			$url_activar = CRUDBooster::mainpath('open-activar/');

			$this->script_js = "  
			
			$(function() {

				$('#resumen').click(function() {
					$('#resumen').html('<i class=\"fa fa-circle-o-notch rotate\"></i> Procesando');
					$('#resumen').attr('disabled', true); 
					setTimeout(function() {
						$('#resumen').attr('disabled', false); 
						$('#resumen').html('<i class=\"fa fa-download\"></i> RESUMEN');
					}, 2000);
				  });
		
			});
			
			function desactivar(id) {
				//alert(id);
				$.fancybox.open({
					src  : '$url_desactivar'+id,
					type : 'iframe',
					opts : {
						iframe:{
							css:{
								width:800,
								height:600
							}
						},
						afterShow : function( instance, current ) {
							console.info( 'popup anular done!' );
						},
						beforeClose: function(instance, current, e) {
							reload = $('.fancybox-iframe').contents().find('#reload').val();
							reload = (reload === 'true') ? true : false;
							console.info(\"reload: \" + reload);
						},
						afterClose: function(instance, current) {
							console.info('popup closed!');
							if (reload) {
								console.info('recargar listado');
								location.reload(true);
							}
						}
					}
				});
			}
			function activar(id) {
				//alert(id);
				$.fancybox.open({
					src  : '$url_activar'+id,
					type : 'iframe',
					opts : {
						iframe:{
							css:{
								width:800,
								height:600
							}
						},
						afterShow : function( instance, current ) {
							console.info( 'popup anular done!' );
						},
						beforeClose: function(instance, current, e) {
							reload = $('.fancybox-iframe').contents().find('#reload').val();
							reload = (reload === 'true') ? true : false;
							console.info(\"reload: \" + reload);
						},
						afterClose: function(instance, current) {
							console.info('popup closed!');
							if (reload) {
								console.info('recargar listado');
								location.reload(true);
							}
						}
					}
				});
			}
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

		if ($modulo == "getIndex") {
			$this->style_css = '';
		}

		/*
		    | ---------------------------------------------------------------------- 
		    | Include css File 
		    | ---------------------------------------------------------------------- 
		    | URL of your css each array 
		    | $this->load_css[] = asset("myfile.css");
		    |email
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

		if (CRUDBooster::myPrivilegeName() == "Super Administrator") {
		} else if (CRUDBooster::myPrivilegeName() == "Administrador") {
		} else {
			$query->where('trabajadors.id_user_create', '=', CRUDBooster::myId());
		}
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
		$number = 1;
		$numero_de_tarjeta_texto = str_pad($number, 6, "0", STR_PAD_LEFT);
		//dd('CTO-'.$numero_de_tarjeta_texto);

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

		//VALIDAR DUPLICADOS
		$id_tipo_documento = $postdata['id_tipo_documento'];
		$nro_documento = $postdata['nro_documento'];
		$email = $postdata['email'];
		$postdata['nombre_completo'] = $postdata['apellidos'] . ', ' . $postdata['nombres'];


		if (isset($nro_documento)) {

			$hasDocumento = DB::table('trabajadors')->where([
				['id_tipo_documento', '=', $id_tipo_documento],
				['nro_documento', '=', $nro_documento]
			])->get();

			if (count($hasDocumento)) {
				CRUDBooster::redirect($_SERVER['HTTP_REFERER'], "Ya existe un registro con el mismo número de documento", "warning");
				return false;
			}
		}
		if (isset($email)) {

			$hasEmail = DB::table('trabajadors')->where([
				['email', '=', $email],
			])->get();

			if (count($hasEmail)) {
				CRUDBooster::redirect($_SERVER['HTTP_REFERER'], "Ya existe un registro con el mismo correo", "warning");
				return false;
			}
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

		$id_conyugue = $postdata['id_conyugue'];
		$postdata['nombre_completo'] = $postdata['apellidos'] . ', ' . $postdata['nombres'];

		if ($id_conyugue > 0) {

			DB::table('trabajadors')
				->where('id', '=', $id_conyugue)
				->update([
					'id_conyugue' => $id,
					'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
				]);
		}

		//INSERTAR PAGOS
		$fecha_inicio =  Carbon::parse($postdata['fecha_inicio']);
		$fecha_fin = Carbon::parse($postdata['fecha_fin']);
		$monto = $postdata['monto'];


		//OBTENER ID DE CONTRATO
		$id_contrato	= DB::table('pagos')->where('id_trabajador', 37)->max('id_contrato');

		if ($id_contrato == null) {
			$id_contrato = 1;
		}

		/*
		$query_categorias = DB::select('SELECT COUNT(t.id_categoria) AS total, ca.nombre_p AS categoria
										FROM trabajadors AS c
										LEFT JOIN categorias AS ca 
										ON t.id_categoria = ca.id
										WHERE t.listable = 0
										GROUP BY t.id_categoria, ca.nombre_p
										ORDER BY t.id_categoria ASC;');

										*/



		if (isset($fecha_inicio) && isset($fecha_fin) && $monto) {

			$referencia = 'CONTRATO DEL ' . Carbon::parse($postdata['fecha_inicio'])->isoFormat('D MMMM Y') . ' AL ' . Carbon::parse($postdata['fecha_fin'])->isoFormat('D MMMM Y');

			$id_tipo_contrato = 2; //quincenal mensual / anual / 


			while ($fecha_inicio->lte($fecha_fin)) {
				$endOfMonth = $fecha_inicio->copy()->endOfMonth()->format('Y-m-d');

				DB::table('pagos')->insert([
					'id_trabajador' 		=> $id,
					'id_tipo_contrato' 	=> $id_tipo_contrato,
					'id_contrato' 		=> $id_contrato,
					'referencia' 		=>  'CTR-' . str_pad($id_contrato, 6, "0", STR_PAD_LEFT), //  strtoupper($referencia),
					'fecha' 			=> $endOfMonth,
					'fecha_inicio' 		=> $fecha_inicio,
					'fecha_fin' 		=> $fecha_fin,
					'id_estado' 		=> 0,
					'monto'				=> $monto,
					'detalle_contrato' 	=> 'Detalle'
				]);

				$fecha_inicio->addMonthNoOverflow();
			}

			unset($postdata['monto']);
			unset($postdata['fecha_inicio']);
			unset($postdata['fecha_fin']);
			unset($postdata['id_tipo_contrato']);
			unset($postdata['detalle_contrato']);
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
	    | ESTADOS
	    | ----------------------------------------------------------------------     
	    | 
	*/


	public function getActivar($id)
	{

		DB::table('trabajadors')
			->where('id', '=', $id)
			->update([
				'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
				'id_estado' => 1,
				'listable' => 1

			]);
		CRUDBooster::redirect($_SERVER['HTTP_REFERER'], "Registro desactivado!", "success");
	}

	public function getDesactivar($id)
	{
		DB::table('trabajadors')
			->where('id', '=', $id)
			->update([
				'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
				'id_estado' => 2,
				'listable' => 0
			]);
		CRUDBooster::redirect($_SERVER['HTTP_REFERER'], "Registro desactivado!", "success");
	}



	/* 
	    | ---------------------------------------------------------------------- 
	    | DASHBOARD GRÁFICO :: GET/POST
	    | ----------------------------------------------------------------------     
	    | 
	*/

	public function getDashboard()
	{

		$generos 		= DB::table('generos')->orderBy('id', 'asc')->get();
		$estados 		= DB::table('estados')->orderBy('id', 'asc')->where('grupo', 1)->get();

		$query_generos = DB::select('SELECT COUNT(t.id_genero) as total, tg.nombre as genero
								FROM trabajadors AS t
								INNER JOIN generos as tg
								on t.id_genero = tg.id
								GROUP BY t.id_genero, tg.nombre
								ORDER BY t.id_genero ASC;');

		$query_estados = DB::select('SELECT COUNT(t.id_estado) as total, e.estado_activacion as estado
								FROM trabajadors AS t
								INNER JOIN estados as e
								on t.id_estado = e.id
								GROUP BY t.id_estado, e.estado_activacion
								ORDER BY t.id_estado ASC;');

		$data_estados = array();
		$data_generos = array();



		foreach ($query_generos as $row) {
			array_push($data_generos, $row);
		}
		foreach ($query_estados as $row) {
			array_push($data_estados, $row);
		}

		$label_genero = json_encode(array_column($data_generos, 'genero'));
		$value_genero = json_encode(array_column($data_generos, 'total'));

		$label_estado = json_encode(array_column($data_estados, 'estado'));
		$value_estado = json_encode(array_column($data_estados, 'total'));

		$data = compact(
			'generos',
			'estados',
			'label_genero',
			'value_genero',
			'label_estado',
			'value_estado'
		);

		return view('dashboard.trabajadores_dashboard', $data);
	}

	public function postDashboard(HttpRequest $request)
	{

		$id_genero = (isset($request->id_genero)) ? (int)$request->id_genero : 0;
		$generos 		= DB::table('generos')->orderBy('id', 'asc')->get();

		$id_estado = (isset($request->id_estado)) ? (int)$request->id_estado : 0;
		$estados 		= DB::table('estados')->orderBy('id', 'asc')->where('grupo', 1)->get();

		//QUERY GÉNERO
		$query_generos = 'SELECT COUNT(t.id_genero) as total, g.nombre as genero
								FROM trabajadors AS t
								INNER JOIN generos as g
								on t.id_genero = g.id';

		$query_estados = 'SELECT COUNT(t.id_estado) as total, e.estado_activacion as estado
								FROM trabajadors AS t
								INNER JOIN estados as e
								on t.id_estado = e.id';


		//PARAMETRO:: TIPO GÉNERO
		if ($id_genero > 0) {
			$query_estados 	= $query_estados . ' AND  t.id_genero = ' . $id_genero . ' ';
			$query_generos 	= $query_generos . ' AND  t.id_genero = ' . $id_genero . ' ';
		}

		//PARAMETRO:: ESTADO
		if ($id_estado > 0) {
			$query_estados 	= $query_estados . ' AND  t.id_estado = ' . $id_estado . ' ';
			$query_generos 	= $query_generos . ' AND  t.id_estado = ' . $id_estado . ' ';
		}

		$query_estados 	= $query_estados . " GROUP BY t.id_estado, e.estado_activacion ORDER BY t.id_estado ASC;";
		$query_generos 	= $query_generos . " GROUP BY t.id_genero, g.nombre ORDER BY t.id_genero ASC;";

		$data_estados 	= DB::select($query_estados);
		$data_generos 	= DB::select($query_generos);


		$label_estado = json_encode(array_column($data_estados, 'estado'));
		$value_estado = json_encode(array_column($data_estados, 'total'));

		$label_genero = json_encode(array_column($data_generos, 'genero'));
		$value_genero = json_encode(array_column($data_generos, 'total'));

		$data = compact(
			'id_estado',
			'id_genero',
			'generos',
			'estados',
			'label_genero',
			'value_genero',
			'label_estado',
			'value_estado'
		);

		return view('dashboard.trabajadores_dashboard', $data);
	}



	/* 
	    | ---------------------------------------------------------------------- 
	    | DESCARGA DE ARCHIVOS PDF, EXCEL
	    | ----------------------------------------------------------------------     
	    | 
	*/

	/* 
	    | ---------------------------------------------------------------------- 
	    | REPORTES
	    | ----------------------------------------------------------------------     
	    | 
	*/


	public function getPdf($id)
	{
		$vista_informe = 'files.pdf.trabajadores_pdf';
		$data = self::getDataPdf($id);
		$pdf = App::make('dompdf.wrapper');
		$context = stream_context_create([
			'ssl' => [
				'verify_peer' => FALSE,
				'verify_peer_name' => FALSE,
				'allow_self_signed' => TRUE
			]
		]);
		$pdf->getDomPDF()->setHttpContext($context);
		$pdf->loadView($vista_informe, $data);
		return $pdf->stream();
	}

	public static function getDataPdf($id)
	{

		$where_yacimiento = ' ';
		if (CRUDBooster::myNivel() != "GENERAL") {
			$where_yacimiento = ' AND id_yacimiento = ' . CRUDBooster::mySedeId();
		}

		//QUERY TIPO MIEMBRO
		$query_estados = 'SELECT COUNT(t.id_estado) as total, e.estado_activacion as estado
						FROM trabajadors AS t
						INNER JOIN estados AS e  ON t.id_estado = e.id 
						WHERE ' . $where_yacimiento . '
						GROUP BY t.id_estado, e.estado_activacion 
						ORDER BY t.id_estado ASC;';

		//QUERY GÉNERO
		$query_generos = 'SELECT COUNT(t.id_genero) as total, g.nombre as genero
						FROM trabajadors AS t
						INNER JOIN generos as g ON t.id_genero = g.id 
						WHERE ' . $where_yacimiento . '
						GROUP BY t.id_genero, g.nombre ORDER BY t.id_genero ASC;';

		//TOTAL GENERAL
		$query_total = 'SELECT COUNT(id) as total
						FROM trabajadors 
						WHERE ' . $where_yacimiento;

		$estados 		= DB::select($query_estados);
		$generos 		= DB::select($query_generos);
		$query_total 	= DB::select($query_total);
		$fecha =  date('Ymd_His') . CRUDBooster::myId() . rand(0, 9);

		foreach ($query_total as $row) {
			$total = $row->total;
		}
		$data = compact(
			'estados',
			'generos',
			'total',
			'fecha'
		);
		return $data;
	}


	public function postExcelTrabajadores(HttpRequest $request)
	{

		$id_estado = $request->id_estado;
		$id_genero = $request->id_genero;

		$where_yacimiento = ' ';
		if (CRUDBooster::myNivel() != "GENERAL") {
			$where_yacimiento = 'WHERE id_yacimiento = ' . CRUDBooster::mySedeId();
		}

		$query_trabajadores = 'SELECT t.*, g.nombre AS genero, e.estado_activacion AS estado, td.nombre as tipo_documento
							FROM trabajadors AS t
							INNER JOIN generos AS g ON t.id_genero = g.id
							INNER JOIN estados AS e ON t.id_estado = e.id
							LEFT JOIN tipo_documentos AS td ON t.id_tipo_documento = td.id' . $where_yacimiento;
		if ($id_estado > 0) {
			$query_trabajadores = $query_trabajadores . 'AND t.id_estado = ' . $id_estado . ' ';
		}
		if ($id_genero > 0) {
			$query_trabajadores = $query_trabajadores . 'AND t.id_genero = ' . $id_genero . ' ';
		}

		$query_trabajadores = $query_trabajadores . ' ORDER BY t.apellidos ASC;';
		$trabajadores 		= DB::select($query_trabajadores);

		if (count($trabajadores)) {

			$data = compact('trabajadores');
			$xls = Excel::create('trabajadores', function ($excel) use ($data) {
				$excel->sheet('Trabajadores', function ($sheet) use ($data) {

					//SALTO DE LINEA, SI EL TAMAÑO SUPERA EL LÍMITE ESTABLECIDO
					/*$sheet->getStyle('D4')->getAlignment()->setWrapText(true);
					$sheet->getStyle('E4')->getAlignment()->setWrapText(true);
					$sheet->getStyle('F4')->getAlignment()->setWrapText(true);*/

					//CENTRADO VERTICAL PARA TITULOS
					for ($column = 'A'; $column <= 'K'; $column++) {
						$cell = $column . '4';
						$sheet->getStyle($cell)->getAlignment()->setWrapText(true)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
					}

					// FIN CENTRADO VERTICAL

					$sheet->setHeight(4, 35);

					$sheet->setColumnFormat(array(
						'A' => '0',
						'E' => 'General',
						'F' => 'date_format:d/m/yyyy',
						'G' => '0',
					));
					$sheet->loadView('files.excel.trabajadores_excel', $data);
				});
			})->export('xlsx');
		} else {
			CRUDBooster::redirect($_SERVER['HTTP_REFERER'], "Sin registros para exportar", "info");
		}
	}


	public function apitrabajadors()
	{

		$query = DB::select('SELECT * FROM trabajadors');
		foreach ($query as $row) {
			$data['user'] = $row;
		}
		return response()->json($data, 200);
	}
}
