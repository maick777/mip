<footer class="main-footer">
    <!-- To the right -->
    <div class="pull-{{ trans('crudbooster.right') }} hidden-xs">
        <small>{{ trans('crudbooster.powered_by') }} {{Session::get('appname')}}</small>
    </div>
    <!-- Default to the left -->
    <small>{{ trans('crudbooster.copyright') }} &copy; <?php echo date('Y') ?>. {{ trans('crudbooster.all_rights_reserved') }} .</small>
</footer>
