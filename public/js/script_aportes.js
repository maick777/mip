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

    var ctx_aporte = document.getElementById("char_aportes");
    var char_aportes = new Chart(ctx_aporte, {
        type: "bar",
        data: {
            labels: labels,
            datasets: [
                {
                    label: "Monto",
                    backgroundColor: background_color,
                    borderColor: color_borde,
                    borderWidth: 1,
                    fill: false,
                    data: values,
                },
            ],
        },

        options: {
            responsive: true,
            plugins: {
                labels: {
                    render: "value",
                },
            },
        },
    });

    $("#descargar_reporte_por_mes").click(function (e) {
        let _url = "/admin/aportes/download_aportes_mes";
        let token = $("[name='_token']").val();
        let formData = new FormData();
        formData.append("_token", token);
        formData.append("brokers_id", $("#brokers_id").val());
        formData.append("anio", $("#anio").val());
        formData.append("monedas_id", $("#monedas_id").val());
        formData.append("mes_inicio_id", $("#mes_inicio_id").val());
        formData.append("mes_fin_id", $("#mes_fin_id").val());

        fetch(_url, {
            method: "post",
            headers: {
                "Csrf-Token": token,
            },
            body: formData,
        })
            .then((response) => {
                // alert({response});

                //console.log({response})
                return response.blob();
            })
            .then((blob) => {
                //console.log({blob})

                let url = window.URL.createObjectURL(blob);
                let a = document.createElement("a");
                a.href = url;
                a.download = "reporte_por_mes.xlsx";
                document.body.appendChild(a);
                a.click();
                a.remove();
            });
    });
});
