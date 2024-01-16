function confirmar(event) {
    event.preventDefault();

    swal(
        {
            title: "Confirmación",
            text: "¿Está seguro que desea realizar esta acción?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "¡Sí!",
            cancelButtonText: "No",
            closeOnConfirm: true,
        },
        function () {
            location.href =
                "http://127.0.0.1:8000/admin/actividads/activar/1";
        }
    );
}
