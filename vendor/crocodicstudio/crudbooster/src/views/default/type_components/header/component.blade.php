<div id='header{{$index}}' data-collapsed="{{ ($form['collapsed']===false)?'false':'true' }}" class='header-title form-divider'>
    <h5>
        <strong><i class='{{$form['icon']?:"fa fa-check-square-o"}}'></i> {{$form['label']}}</strong>
        <span class='pull-right icon'><i class='fa fa-minus-square-o'></i></span>
    </h5>
</div>