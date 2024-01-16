@foreach($addaction as $a)
    <?php
    foreach ($row as $key => $val) {
        $a['url'] = str_replace("[".$key."]", $val, $a['url']);
    }

    $confirm_box = '';
    if (isset($a['confirmation']) && ! empty($a['confirmation']) && $a['confirmation']) {

        $a['confirmation_title'] = ! empty($a['confirmation_title']) ? $a['confirmation_title'] : trans('crudbooster.confirmation_title');
        $a['confirmation_text'] = ! empty($a['confirmation_text']) ? $a['confirmation_text'] : trans('crudbooster.confirmation_text');
        $a['confirmation_type'] = ! empty($a['confirmation_type']) ? $a['confirmation_type'] : 'warning';
        $a['confirmation_showCancelButton'] = empty($a['confirmation_showCancelButton']) ? 'true' : 'false';
        $a['confirmation_confirmButtonColor'] = ! empty($a['confirmation_confirmButtonColor']) ? $a['confirmation_confirmButtonColor'] : '#DD6B55';
        $a['confirmation_confirmButtonText'] = ! empty($a['confirmation_confirmButtonText']) ? $a['confirmation_confirmButtonText'] : trans('crudbooster.confirmation_yes');;
        $a['confirmation_cancelButtonText'] = ! empty($a['confirmation_cancelButtonText']) ? $a['confirmation_cancelButtonText'] : trans('crudbooster.confirmation_no');;
        $a['confirmation_closeOnConfirm'] = empty($a['confirmation_closeOnConfirm']) ? 'true' : 'false';

        $confirm_box = '
        swal({   
            title: "'.$a['confirmation_title'].'",
            text: "'.$a['confirmation_text'].'",
            type: "'.$a['confirmation_type'].'",
            showCancelButton: '.$a['confirmation_showCancelButton'].',
            confirmButtonColor: "'.$a['confirmation_confirmButtonColor'].'",
            confirmButtonText: "'.$a['confirmation_confirmButtonText'].'",
            cancelButtonText: "'.$a['confirmation_cancelButtonText'].'",
            closeOnConfirm: '.$a['confirmation_closeOnConfirm'].', }, 
            function(){  location.href="'.$a['url'].'"});        

        ';
    }

    $label = $a['label'];
    $title = ($a['title']) ?: $a['label'];
    $icon = $a['icon'];
    $color = $a['color'] ?: 'primary';
    $confirmation = $a['confirmation'];
    $target = $a['target'] ?: '_self';

    $url = $a['url'];
    if (isset($confirmation) && ! empty($confirmation)) {
        $url = "javascript:;";
    }

    if (isset($a['showIf'])) {

        $query = $a['showIf'];

        foreach ($row as $key => $val) {
            $query = str_replace("[".$key."]", '"'.$val.'"', $query);
        }

        
        @eval("if($query) {
         
      } 
      
      ");
    } else {
        //echo "<a class='btn btn-xs btn-$color' title='$title' onclick='$confirm_box' href='$url' target='$target'><i class='$icon'></i> $label</a>&nbsp;";
    }
    ?>
@endforeach


    @if(CRUDBooster::isUpdate() && $button_edit)
        <a class='btn btn-xs btn-success btn-edit btn-custom-action-table' title='{{trans("crudbooster.action_edit_data")}}'
           href='{{CRUDBooster::mainpath("edit/".$row->$pk)."?return_url=".urlencode(Request::fullUrl())."&parent_id=".g("parent_id")."&parent_field=".$parent_field}}'><i
                    class='fa fa-pencil'></i></a>
    @endif

