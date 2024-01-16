@include('files.pdf.header')
@include('files.pdf.footer')

<div class="reporte">

    <div class="report-body">
        <div class="text-center sub_titulo">
            <span>RESUMEN TRABAJADORES</span>
        </div>

        <div class="block">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width: 50px;">N°</th>
                        <th>POR ESTADO</th>
                        <th style="width: 80px;">Cantidad</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($estados as $key=>$row)
                    <tr>
                        <td>{{ str_pad($key + 1, 2, '0', STR_PAD_LEFT) }}</td>
                        <td>{{ $row->estado }}</td>
                        <td>{{ $row->total }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="block">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width: 50px;">N°</th>
                        <th>POR GÉNERO</th>
                        <th style="width: 80px;">Cantidad</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($generos as $key=>$row)
                    <tr>
                        <td>{{ str_pad($key + 1, 2, '0', STR_PAD_LEFT) }}</td>
                        <td>{{ $row->genero }}</td>
                        <td>{{ $row->total }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="block">
            <table class="table">
                <tr>
                    <td class="text-right no-border" colspan="2"><b>TOTAL: </b></td>
                    <td class="resumen_total" style="width: 80px; font-size: 13px;"><b> {{ $total }}</b></td>
                </tr>
            </table>
        </div>

    </div>

</div>

</body>

</html>