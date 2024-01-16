/*-----------------------------------------------------------------------
// TYPE: horizontalBar, line, bar, pie, doughnut, polarArea, radar /bubble, scatter
------------------------------------------------------------------------*/

$(function () {
    var color_borde = [
        "rgb(89, 182, 106)", //verde
        "rgb(153, 102, 255)", //azul
        "rgb(255, 205, 86)", //amarillo
        "rgb(255, 99, 132)", //rojo
        "rgb(201, 203, 207)", //gris
        "rgb(255, 159, 64)", //naranja
        "rgb(54, 162, 235", //celeste

        "rgb(89, 182, 106)", //verde
        "rgb(153, 102, 255)", //azul
        "rgb(255, 205, 86)", //amarillo
        "rgb(255, 99, 132)", //rojo
        "rgb(255, 159, 64)", //naranja
    ];
    var background_color = [
        "rgba(92, 201, 143, 0.2)", //verde
        "rgba(153, 102, 255, 0.2)", //azul
        "rgba(255, 205, 86, 0.2)", //amarillo
        "rgba(255, 99, 132, 0.2)", //rojo
        "rgba(201, 203, 207, 0.2)", // gris
        "rgba(255, 159, 64, 0.2)", //naranja
        "rgba(54, 162, 235, 0.2)", //celeste

        "rgba(92, 201, 143, 0.2)", //verde
        "rgba(153, 102, 255, 0.2)", //azul
        "rgba(255, 205, 86, 0.2)", //amarillo
        "rgba(255, 99, 132, 0.2)", //rojo
        "rgba(255, 159, 64, 0.2)", //naranja
    ];

  

    var ctx_estado = document.getElementById("chart_estado");
    var chart_estado = new Chart(ctx_estado, {
        type: "pie",
        data: {
            labels: label_estado,
            datasets: [
                {
                    label: "Estado",
                    backgroundColor: background_color,
                    borderColor: color_borde,
                    borderWidth: 1,
                    fill: true,
                    data: value_estado,
                },
            ],
        },
        options: {
            responsive: true,
            plugins: {
                labels: {
                    render: "percentage",
                },
            },
        },
    });

    var ctx_genero = document.getElementById("chart_genero");
    var chart_genero = new Chart(ctx_genero, {
        type: "doughnut",
        data: {
            labels: label_genero,
            datasets: [
                {
                    label: "Género",
                    backgroundColor: background_color,
                    borderColor: color_borde,
                    borderWidth: 1,
                    fill: "origin",
                    data: value_genero,
                },
            ],
        },
        options: {
            responsive: true,
            plugins: {
                labels: {
                    render: "percentage",
                    position: "border",
                },
            },
        },
    });

    excel_trabajadores();
});

function excel_trabajadores() {

    $("#excel_trabajadores").click(function (e) {

        let _url = "/admin/trabajadores/trabajadores_excel";
        let token = $("[name='_token']").val();
        let formData = new FormData();

        let myId = $("#myId").val();
        let date = moment();
        let fecha = date.format("YYYYMMDD-Hmmss"); 

        formData.append("_token", token);
        formData.append("id_estado", $("#id_estado").val());
        formData.append("id_genero", $("#id_genero").val());

        fetch(_url, {
            method: "post",
            headers: {
                "Csrf-Token": token,
            },
            body: formData,
        })
            .then((response) => {
                console.log({ response });
                return response.blob();
            })
            .then((blob) => {
                console.log({ blob });
                let url = window.URL.createObjectURL(blob);
                let a = document.createElement("a");
                a.href = url;
                a.download = "REPORTE-TRABAJADORES-" + fecha + myId + ".xlsx";
                document.body.appendChild(a);
                a.click();
                a.remove();
            });
    });
}
