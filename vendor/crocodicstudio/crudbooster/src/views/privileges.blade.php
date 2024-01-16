@extends('crudbooster::admin_template')

@section('content')

<div style="width:80%;margin:0 auto ">
    @if(CRUDBooster::getCurrentMethod() != 'getProfile')
    <p><a href='{{CRUDBooster::mainpath()}}'><i class="fa fa-chevron-circle-left "></i> {{trans("crudbooster.form_back_to_list",['module'=>CRUDBooster::getCurrentModule()->name])}}</a></p>
    @endif

    <!-- Box -->
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">{{ $page_title }}</h3>
            <div class="box-tools">

            </div>
        </div>
        <form method='post' action='{{ (@$row->id)?route("PrivilegesControllerPostEditSave")."/$row->id":route("PrivilegesControllerPostAddSave") }}'>
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <div class="box-body">
                <div class="alert alert-info">
                    <strong>Nivel GENERAL:</strong> Acceso a todos los registros de todos los sedes <br>
                    <strong>Nivel LOCAL:</strong> Acceso a todos los registros de su sede <br>
                    <strong>Nivel LIMITADO:</strong> Acceso a datos que ha registrado o asignados a su nombre.
                </div>
                <div class='form-group'>
                    <label>{{trans('crudbooster.privileges_name')}}</label>
                    <input type='text' class='form-control' name='name' required value='{{ @$row->name }}' />
                    <div class="text-danger">{{ $errors->first('name') }}</div>
                </div>

                <div class='form-group'>
                    <label>Nivel de acceso</label>
                    <select name='nivel' class='form-control' required>
                        <option value=''>** Seleccione nivel</option>
                        <option <?= (@$row->nivel == 'GENERAL') ? "selected" : "" ?> value="GENERAL"><?= ucwords(str_replace('-', ' ', 'GENERAL')) ?></option>
                        <option <?= (@$row->nivel == 'LOCAL') ? "selected" : "" ?> value="LOCAL"><?= ucwords(str_replace('-', ' ', 'LOCAL')) ?></option>
                        <option <?= (@$row->nivel == 'LIMITADO') ? "selected" : "" ?> value="LIMITADO"><?= ucwords(str_replace('-', ' ', 'LIMITADO')) ?></option>

                    </select>
                    <div class="text-danger">{{ $errors->first('theme_color') }}</div>
                </div>

                <div class='form-group'>
                    <label>{{trans('crudbooster.set_as_superadmin')}}</label>
                    <div id='set_as_superadmin' class='radio'>
                        <label><input required {{ (@$row->is_superadmin==1)?'checked':'' }} type='radio' name='is_superadmin' value='1' /> {{trans('crudbooster.confirmation_yes')}}</label> &nbsp;&nbsp;
                        <label><input {{ (@$row->is_superadmin==0)?'checked':'' }} type='radio' name='is_superadmin' value='0' /> {{trans('crudbooster.confirmation_no')}}</label>
                    </div>
                    <div class="text-danger">{{ $errors->first('is_superadmin') }}</div>
                </div>

                <div class='form-group'>
                    <label>{{trans('crudbooster.chose_theme_color')}}</label>
                    <select name='theme_color' class='form-control' required>
                        <option value=''>{{trans('crudbooster.chose_theme_color_select')}}</option>
                        <?php
                        $skins = array(
                            'light-skin-primary',
                            'light-skin-spoty',
                            'dark-skin-navi',
                            'dark-skin-black'
                        );
                        foreach ($skins as $skin) :
                        ?>
                            <option <?= (@$row->theme_color == $skin) ? "selected" : "" ?> value='<?= $skin ?>'><?= ucwords(str_replace('-', ' ', $skin)) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <div class="text-danger">{{ $errors->first('theme_color') }}</div>
                    @push('bottom')
                    <script type="text/javascript">
                        $(function() {
                            $("select[name=theme_color]").change(function() {
                                var n = $(this).val();
                                $("body").attr("class", n);
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
                        })
                    </script>
                    @endpush
                </div>

                <div id='privileges_configuration' class='form-group'>
                    <label>{{trans('crudbooster.privileges_configuration')}}</label>
                    @push('bottom')
                    <script>
                        $(function() {
                            $("#is_visible").click(function() {
                                var is_ch = $(this).prop('checked');
                                console.log('is checked create ' + is_ch);
                                $(".is_visible").prop("checked", is_ch);
                                console.log('Create all');
                            })
                            $("#is_create").click(function() {
                                var is_ch = $(this).prop('checked');
                                console.log('is checked create ' + is_ch);
                                $(".is_create").prop("checked", is_ch);
                                console.log('Create all');
                            })
                            $("#is_read").click(function() {
                                var is_ch = $(this).is(':checked');
                                $(".is_read").prop("checked", is_ch);
                            })
                            $("#is_edit").click(function() {
                                var is_ch = $(this).is(':checked');
                                $(".is_edit").prop("checked", is_ch);
                            })
                            $("#is_delete").click(function() {
                                var is_ch = $(this).is(':checked');
                                $(".is_delete").prop("checked", is_ch);
                            })
                            $("#is_download").click(function() {
                                var is_ch = $(this).is(':checked');
                                $(".is_download").prop("checked", is_ch);
                            })
                            $("#is_dashboard").click(function() {
                                var is_ch = $(this).is(':checked');
                                $(".is_dashboard").prop("checked", is_ch);
                            })
                            $(".select_horizontal").click(function() {
                                var p = $(this).parents('tr');
                                var is_ch = $(this).is(':checked');
                                p.find("input[type=checkbox]").prop("checked", is_ch);
                            })
                        })
                    </script>
                    @endpush
                    <table class='table table-striped table-hover table-bordered'>
                        <thead>
                            <tr class='active'>
                                <th width='3%'>{{trans('crudbooster.privileges_module_list_no')}}</th>
                                <th width='60%'>{{trans('crudbooster.privileges_module_list_mod_names')}}</th>
                                <th>&nbsp;</th>
                                <th>Listar</th>
                                <th>{{trans('crudbooster.privileges_module_list_create')}}</th>
                                <th>{{trans('crudbooster.privileges_module_list_read')}}</th>
                                <th>{{trans('crudbooster.privileges_module_list_update')}}</th>
                                <th>{{trans('crudbooster.privileges_module_list_delete')}}</th>
                                <th>Descargar</th>
                                <th>Dashboard</th>
                            </tr>
                            <tr class='info'>
                                <th>&nbsp;</th>
                                <th>&nbsp;</th>
                                <th>&nbsp;</th>
                                <td align="center"><input title='Check all vertical' type='checkbox' id='is_visible' /></td>
                                <td align="center"><input title='Check all vertical' type='checkbox' id='is_create' /></td>
                                <td align="center"><input title='Check all vertical' type='checkbox' id='is_read' /></td>
                                <td align="center"><input title='Check all vertical' type='checkbox' id='is_edit' /></td>
                                <td align="center"><input title='Check all vertical' type='checkbox' id='is_delete' /></td>
                                <td align="center"><input title='Check all vertical' type='checkbox' id='is_download' /></td>
                                <td align="center"><input title='Check all vertical' type='checkbox' id='is_dashboard' /></td>

                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; ?>
                            @foreach($moduls as $modul)
                            <?php
                            $roles = DB::table('cms_privileges_roles')->where('id_cms_moduls', $modul->id)->where('id_cms_privileges', $row->id)->first();
                            ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td>{{$modul->name}}</td>
                                <td class='info' align="center"><input type='checkbox' title='Check All Horizontal' <?= ($roles->is_create && $roles->is_read && $roles->is_edit && $roles->is_delete) ? "checked" : "" ?> class='select_horizontal' />
                                </td>
                                <td class='active' align="center"><input type='checkbox' class='is_visible' name='privileges[<?= $modul->id ?>][is_visible]' <?= @$roles->is_visible ? "checked" : "" ?> value='1' /></td>
                                <td class='warning' align="center"><input type='checkbox' class='is_create' name='privileges[<?= $modul->id ?>][is_create]' <?= @$roles->is_create ? "checked" : "" ?> value='1' /></td>
                                <td class='info' align="center"><input type='checkbox' class='is_read' name='privileges[<?= $modul->id ?>][is_read]' <?= @$roles->is_read ? "checked" : "" ?> value='1' /></td>
                                <td class='success' align="center"><input type='checkbox' class='is_edit' name='privileges[<?= $modul->id ?>][is_edit]' <?= @$roles->is_edit ? "checked" : "" ?> value='1' /></td>
                                <td class='danger' align="center"><input type='checkbox' class='is_delete' name='privileges[<?= $modul->id ?>][is_delete]' <?= @$roles->is_delete ? "checked" : "" ?> value='1' /></td>
                                <td class='danger' align="center"><input type='checkbox' class='is_download' name='privileges[<?= $modul->id ?>][is_download]' <?= @$roles->is_download ? "checked" : "" ?> value='1' /></td>
                                <td class='danger' align="center"><input type='checkbox' class='is_dashboard' name='privileges[<?= $modul->id ?>][is_dashboard]' <?= @$roles->is_dashboard ? "checked" : "" ?> value='1' /></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                </div>

            </div><!-- /.box-body -->
            <div class="box-footer" align="right">
                <button type='button' onclick="location.href='{{CRUDBooster::mainpath()}}'" class='btn btn-sm btn-default'>{{trans("crudbooster.button_cancel")}}</button>
                <button type='submit' class='btn btn-sm btn-primary'><i class='fa fa-save'></i> {{trans("crudbooster.button_save")}}</button>
            </div><!-- /.box-footer-->
    </div>

    <!-- /.box -->

</div>
@endsection