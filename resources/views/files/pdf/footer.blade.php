<footer>
    <div id="numero_pagina"><span class="pagenum"></span></div>
</footer>

<style>
    @page {
        margin: 0cm 0cm;
    }

    body {
        margin-top: 7cm;
    }

    header {
        position: fixed;
        top: 1cm;
        left: 0cm;
        right: 0cm;
        height: 2cm;
        text-align: center;
        line-height: 0.1cm;
        width: 80%;
        margin-left: 10%;
        padding: 15px;

    }

    header .titulo {
        color: #27284f;
    }

    header .subtitulo {
        color: red;
    }

    .sub_titulo {
        font-size: 14px;
    }

    .resumen_total {
        background: #d2cfe1;
    }

    /*
        #watermark {
            position: fixed;
            bottom: 0px;
            right: 0px;
            width: 816px;
            height: 1056px;
            opacity: .6;
            background: blue;
        }
    */

    .reporte {
        margin: 0;
        width: 80%;
        margin-left: 10%;
        padding: 0 15px;
    }

    .report-body .block span {
        font-size: 13px;
    }

    .reporte table {
        border-collapse: collapse;
        font-size: 12px;
        padding: 15px;
    }

    .report-body table tr th {
        padding: 2px 5px;
        background: #27284f;
        color: #ffffff;
        border-top: 1px solid #27284f;
        border-bottom: 1px solid transparent;
        text-transform: uppercase;
        text-align: center;
    }

    table tr th:first-child {
        border-left: 1px solid #27284f;
    }

    th+th {
        border-left: 1px solid #bab8c8;
    }

    tr>th:last-of-type {
        border-right: 1px solid #27284f;
    }

    .report-body table td {
        padding: 1px 5px;
        border: 1px solid #bab8c8;
        text-align: center;
        font-size: 11px;
    }

    .report-footer {
        position: absolute;
        bottom: 0;
        width: 670px;
        padding: 0 15px;
        margin-bottom: -8px;
    }

    .report-footer span {
        background-color: #ffffff;
        font-size: 11px;
        display: block;
        width: 100px;
        margin-left: 285px;
        text-align: center;
    }

    .text-right {
        text-align: right !important;
    }

    .text-bold {
        font-weight: bold !important;
    }

    .no-border {
        border-left: 1px solid white !important;
        border-bottom: 1px solid white !important;
        border-top: 1px solid white !important;

    }

    footer {
        position: fixed;
        bottom: 0cm;
        left: 0cm;
        right: 0cm;
        height: 1cm;
        text-align: center;
        line-height: 0.6cm;
        font-size: small;
    }

    .pagenum:before {
        content: counter(page);
    }
</style>