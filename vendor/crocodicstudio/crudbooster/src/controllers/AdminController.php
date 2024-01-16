<?php namespace crocodicstudio\crudbooster\controllers;

use Carbon\Carbon;
use crocodicstudio\crudbooster\helpers\CRUDBooster as CRUDBooster;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class AdminController extends CBController
{
    function getIndex()
    {
        $data = [];
        $data['page_title'] = '<strong>Dashboard</strong>';

        setlocale(LC_ALL, 'es_ES.UTF-8');

        $page_title = 'Inicio';
        $path = url('');
        $mes_actual = Carbon::now()->format('m');
        $dia_actual = Carbon::now()->format('d');
        $mes_proximo = Carbon::now()->addMonth()->format('m');
        $anio_actual = Carbon::now()->format('Y');

       
        $query_trabajadors  = 'SELECT nombres, apellidos, foto, DATE_FORMAT(fecha_nacimiento, "' . $anio_actual . '-%m%-%d") as fecha_nacimiento, MONTH(fecha_nacimiento) as mes
        FROM trabajadors 
        WHERE (MONTH(fecha_nacimiento) = ' . $mes_actual . '  and DAY(fecha_nacimiento) >= ' . $dia_actual . ') OR MONTH(fecha_nacimiento) = ' . $mes_proximo . '
        ORDER BY MONTH(fecha_nacimiento) ASC, DAY(fecha_nacimiento) ASC;';

        $trabajadors = collect(DB::select($query_trabajadors));

        $query_slider  = 'SELECT *
        FROM sliders
        WHERE id_estado = 1
        ORDER BY id ASC LIMIT 10;';

        $sliders = DB::select($query_slider);

        $trabajadors_por_mes = $trabajadors->groupBy([
            'mes'
        ]);

        $data = compact(
            'page_title',
            'trabajadors',
            'sliders',
            'trabajadors_por_mes',
            'dia_actual',
            'mes_actual',
            'path'
        );
        

        return view('crudbooster::home', $data);



    }

    public function getLockscreen()
    {

        if (! CRUDBooster::myId()) {
            Session::flush();

            return redirect()->route('getLogin')->with('message', trans('crudbooster.alert_session_expired'));
        }

        Session::put('admin_lock', 1);

        return view('crudbooster::lockscreen');
    }

    public function postUnlockScreen()
    {
        $id = CRUDBooster::myId();
        $password = Request::input('password');
        $users = DB::table(config('crudbooster.USER_TABLE'))->where('id', $id)->first();

        if (\Hash::check($password, $users->password)) {
            Session::put('admin_lock', 0);

            return redirect(CRUDBooster::adminPath());
        } else {
            echo "<script>alert('".trans('crudbooster.alert_password_wrong')."');history.go(-1);</script>";
        }
    }

    public function getLogin()
    {

        if (CRUDBooster::myId()) {
            return redirect(CRUDBooster::adminPath());
        }

        return view('crudbooster::login');
    }

    public function postLogin()
    {

        $validator = Validator::make(Request::all(), [
            'email' => 'required|email|exists:'.config('crudbooster.USER_TABLE'),
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            $message = $validator->errors()->all();

            return redirect()->back()->with(['message' => implode(', ', $message), 'message_type' => 'danger']);
        }

        $email = Request::input("email");
        $password = Request::input("password");
        $users = DB::table(config('crudbooster.USER_TABLE'))->where("email", $email)->first();

        if (\Hash::check($password, $users->password)) {
            $priv = DB::table("cms_privileges")->where("id", $users->id_cms_privileges)->first();

            $minas = DB::table("yacimientos")->where("yacimientos.id", $users->id_yacimiento)
            ->join('minas', 'yacimientos.id_mina', 'minas.id')->select('minas.logo')->first();

            $roles = DB::table('cms_privileges_roles')->where('id_cms_privileges', $users->id_cms_privileges)->join('cms_moduls', 'cms_moduls.id', '=', 'id_cms_moduls')->select('cms_moduls.name', 'cms_moduls.path', 'is_visible', 'is_create', 'is_read', 'is_edit', 'is_delete', 'is_download', 'is_dashboard')->get();

            $photo = ($users->photo) ? asset($users->photo) :  asset(CRUDBooster::appAvatar());
            $logotipo = ($minas->logo) ? asset($minas->logo) :  asset(CRUDBooster::appAvatar());

            Session::put('admin_id', $users->id);
            Session::put('admin_is_superadmin', $priv->is_superadmin);
            Session::put('admin_name', $users->name);
            Session::put('admin_photo', $photo);
            Session::put('admin_privileges_roles', $roles);
            Session::put("admin_privileges", $users->id_cms_privileges);
            Session::put('admin_privileges_name', $priv->name);
            Session::put('admin_privileges_nivel', $priv->nivel);
            Session::put('admin_lock', 0);
            Session::put('theme_color', $users->theme_color);
            Session::put('admin_logotipo', $logotipo);

            Session::put("app_avatar", CRUDBooster::getSetting('avatar'));
            Session::put("app_name", CRUDBooster::getSetting('appname'));
            Session::put("app_logo", CRUDBooster::getSetting('logo'));

            CRUDBooster::insertLog(trans("crudbooster.log_login", ['email' => $users->email, 'ip' => Request::server('REMOTE_ADDR')]));

            $cb_hook_session = new \App\Http\Controllers\CBHook;
            $cb_hook_session->afterLogin();

            return redirect(CRUDBooster::adminPath());
        } else {
            return redirect()->route('getLogin')->with('message', trans('crudbooster.alert_password_wrong'));
        }
    }

    public function getForgot()
    {
        if (CRUDBooster::myId()) {
            return redirect(CRUDBooster::adminPath());
        }

        return view('crudbooster::forgot');
    }

    public function postForgot()
    {
        $validator = Validator::make(Request::all(), [
            'email' => 'required|email|exists:'.config('crudbooster.USER_TABLE'),
        ]);

        if ($validator->fails()) {
            $message = $validator->errors()->all();

            return redirect()->back()->with(['message' => implode(', ', $message), 'message_type' => 'danger']);
        }

        $rand_string = str_random(5);
        $password = \Hash::make($rand_string);

        DB::table(config('crudbooster.USER_TABLE'))->where('email', Request::input('email'))->update(['password' => $password]);

        $appname = CRUDBooster::getSetting('appname');
        $user = CRUDBooster::first(config('crudbooster.USER_TABLE'), ['email' => g('email')]);
        $user->password = $rand_string;
        CRUDBooster::sendEmail(['to' => $user->email, 'data' => $user, 'template' => 'forgot_password_backend']);

        CRUDBooster::insertLog(trans("crudbooster.log_forgot", ['email' => g('email'), 'ip' => Request::server('REMOTE_ADDR')]));

        return redirect()->route('getLogin')->with('message', trans("crudbooster.message_forgot_password"));
    }

    public function getLogout()
    {

        $me = CRUDBooster::me();
        CRUDBooster::insertLog(trans("crudbooster.log_logout", ['email' => $me->email]));

        Session::flush();

        return redirect()->route('getLogin')->with('message', trans("crudbooster.message_after_logout"));
    }
}
