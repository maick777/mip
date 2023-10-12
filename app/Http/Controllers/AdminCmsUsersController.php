<?php

namespace App\Http\Controllers;

use Request;
use Illuminate\Support\Facades\DB;
use crocodicstudio\crudbooster\helpers\CRUDBooster as CRUDBooster;
use Illuminate\Support\Facades\Session;

class AdminCmsUsersController extends \crocodicstudio\crudbooster\controllers\CBController
{


	public function cbInit()
	{
		# START CONFIGURATION DO NOT REMOVE THIS LINE
		$this->table               = 'cms_users';
		$this->primary_key         = 'id';
		$this->title_field         = "name";
		$this->button_action_style = 'button_icon';
		$this->button_bulk_action  = false;
		$this->button_import 	   = false;
		$this->button_export 	   = false;
		$this->button_show 		= false;
		$this->button_filter 	= false;

		# END CONFIGURATION DO NOT REMOVE THIS LINE

		# START COLUMNS DO NOT REMOVE THIS LINE
		$this->col = [];
		$this->col[] = ["label" => "Foto", "name" => "photo", "width" => "55", "image" => 1];
		if (CRUDBooster::isUpdate() && $this->button_edit) {
			$this->col[] = ["label" => "", "name" => "id", "width" => "20", "callback" => function ($row) {
				return  '<a href="' . CRUDBooster::mainpath("edit/" . $row->id) . '" class="table-link"><i class="fa fa-pencil text-success"></i></a>';
			}];
		}
		$this->col[] = ["label" => "Apellidos & Nombres", "name" => "nombre_completo", "callback" => function ($row) {
			return (CRUDBooster::isRead() && $this->button_detail) ? '<a href="' . CRUDBooster::mainpath("detail/" . $row->id) . '" class="table-link">' . $row->nombre_completo . '</a>' :  $row->nombre_completo;
		}];
		$this->col[] = ["label" => "Correo", "name" => "email", "callback" => function ($row) {
			return ($row->email) ? "<a href=\"mailto:$row->email\" class='table-link'><i class=\"fa fa-envelope-o text-success\"></i>&nbsp;&nbsp;$row->email</a>" : "&nbsp;";
		}];
		$this->col[] = ["label" => "Privilegio", "name" => "id_cms_privileges", "join" => "cms_privileges,name"];
		$this->col[] = [
			"label" => "Nivel", "name" => "id_cms_privileges", "join" => "yacimientos,nombre",
			"callback" => function ($row) {
				$rows = DB::table('cms_privileges')
					->where('id', "=", $row->id_cms_privileges)
					->select('nivel')
					->first();
				return  $rows->nivel;
			}
		];
		if (CRUDBooster::myNivel() == "GENERAL") {
			$this->col[] = ["label" => "Sede", "name" => "id_yacimiento", "join" => "yacimientos,nombre_corto"];
		}

		# END COLUMNS DO NOT REMOVE THIS LINE

		# START FORM DO NOT REMOVE THIS LINE
		$this->form = [];
		$this->form[] = ["label" => "Apellidos", "name" => "apellidos", 'required' => true, 'validation' => 'required|alpha_spaces|min:3|max:50', 'width' => 'col-sm-6'];
		$this->form[] = ["label" => "Nombres", "name" => "name", 'required' => true, 'validation' => 'required|alpha_spaces|min:3|max:50', 'width' => 'col-sm-6'];
		$this->form[] = ["label" => "Correo", "name" => "email", 'required' => true, 'type' => 'email', 'validation' => 'required|email|min:5|max:70|unique:cms_users,email,' . CRUDBooster::getCurrentId(), 'width' => 'col-sm-6'];
		$this->form[] = ["label" => "Foto", "name" => "photo", "type" => "upload", "help" => "Recommended resolution is 200x200px", 'required' => false, 'validation' => 'file|max:1000', 'resize_width' => 500, 'resize_height' => 500];
		$this->form[] = ["label" => "Privilegio", "name" => "id_cms_privileges", "type" => "select", "datatable" => "cms_privileges,name,name", 'datatable_where' => 'id != ' . 1, 'required' => true, 'width' => 'col-sm-6'];
		if (CRUDBooster::myNivel() == "GENERAL") {
			$this->form[] = ['label' => 'Yacimiento', 'name' => 'id_yacimiento', 'type' => 'select', 'validation' => 'required|integer|min:0', 'width' => 'col-sm-4', 'datatable' => 'yacimientos,nombre_corto,id', 'value' => CRUDBooster::mySedeId()];
		} else {
			$this->form[] = ['label' => 'Yacimiento', 'name' => 'id_yacimiento', 'type' => 'hidden', 'validation' => 'required|integer|min:0', 'width' => 'col-sm-4', 'value' => CRUDBooster::mySedeId()];
		}
		$this->form[] = ["label" => "Contraseña", "name" => "password", "type" => "password", "help" => "Dejar vacía si no ha cambiado", 'width' => 'col-sm-6'];
		$this->form[] = ['label' => 'Color del Tema', 'name' => 'theme_color', 'type' => 'select', 'validation' => 'required', 'width' => 'col-sm-3', 'dataenum' => 'light-skin-primary|Light Skin Primary;light-skin-spoty|Light Skin Spoty;dark-skin-navi|Dark Skin Navi; dark-skin-black|Dark Skin Black', 'value' => '1'];

		$this->form[] = ["label" => "Envío de correos", "type" => "header", "name" => "imagen_firma", "collapsed" => false];
		$this->form[] = ['label' => 'Recibir Notificaciones', 'name' => 'notificacion', 'type' => 'radio', 'validation' => 'min:1|max:2', 'width' => 'col-sm-3', 'dataenum' => '1|Sí;0|No', 'value' => '1'];
		$this->form[] = ["label" => "Contraseña Correo", "name" => "password_email", "type" => "text", "help" => "Contraseña de correo", 'width' => 'col-sm-6'];
		$this->form[] = ["label" => "Firma", "name" => "imagen_firma", "type" => "upload", "help" => "Resolución recomendada 500x500px", 'required' => false, 'validation' => '|image|max:2000', 'resize_width' => 500, 'resize_height' => 500, 'width' => 'col-sm-6'];

		# END FORM DO NOT REMOVE THIS LINE


		$modulo = CRUDBooster::getCurrentMethod();

		if ($modulo == "getAdd" || $modulo == "getEdit" || $modulo == "getProfile") {

			$this->script_js = "
			$(function() {
				$('select[name=theme_color]').change(function() {
					var n = $(this).val();
					$('body').attr('class', n);
				})

				$('#set_as_superadmin input').click(function() {
					var n = $(this).val();
					if (n == '1') {
						$('#privileges_configuration').hide();
					} else {
						$('#privileges_configuration').show();
					}
				})

				$('#set_as_superadmin input:checked').trigger('click');
			})";
		}
	}

	public function getProfile()
	{

		$this->button_addmore = FALSE;
		$this->button_cancel  = FALSE;
		$this->button_show    = FALSE;
		$this->button_add     = FALSE;
		$this->button_delete  = FALSE;
		$this->hide_form 	  = ['id_cms_privileges'];

		$data['page_title'] = trans("crudbooster.label_button_profile");
		$data['row']        = CRUDBooster::first('cms_users', CRUDBooster::myId());
		return $this->view('crudbooster::default.form', $data);
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
		if (CRUDBooster::myNivel() == "LIMITADO") {
			$query->where('cms_users.id_user_create', '=', CRUDBooster::myId());
		} else if (CRUDBooster::myNivel() == "LOCAL") {
			$query->where('cms_users.id_yacimiento', '=', CRUDBooster::mySedeId());
		} else {
		}

		$query->where('cms_users.id', '!=', 1);
	}

	public function hook_before_add(&$postdata)
	{
		$postdata['nombre_completo'] = $postdata['apellidos'] . ', ' . $postdata['name'];
		unset($postdata['password_confirmation']);
	}


	public function hook_before_edit(&$postdata, $id)
	{
		$postdata['nombre_completo'] = $postdata['apellidos'] . ', ' . $postdata['name'];
		unset($postdata['password_confirmation']);
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
		$user = DB::table('cms_users')->where("id", $id)->first();

		if (CRUDBooster::myId() == $id) {
			Session::put('theme_color', $this->arr['theme_color']);
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

		if ($id == 1) {

			CRUDBooster::redirect(
				$_SERVER['HTTP_REFERER'],
				"Upsss! No se puede eliminar este registro!",
				"warning"
			);
		}
	}
}
