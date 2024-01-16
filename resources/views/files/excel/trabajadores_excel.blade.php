<table>
    <tr></tr>
    <tr class="title">
        <td colspan="11">
            <img src="{{ public_path('vendor/crudbooster/avatar.png') }}" height="40" alt="Logo">
            <h2> REPORTE DE TRABAJADORES</h2>
        </td>
    </tr>
    <tr>
        <td colspan="11"></td>
    </tr>
    <thead>
        <tr class="header">
            <th>#</th>
            <th>APELLIDOS</th>
            <th>NOMBRES</th>
            <th width="15">TIPO <br> DOCUMENTO</th>
            <th width="15">NÚMERO <br> DOCUMENTO</th>
            <th width="15">FECHA <br> NACIMIENTO</th>
            <th>EDAD</th>
            <th>GÉNERO</th>
            <th>CORREO</th>
            <th>CELULAR</th>
            <th>ESTADO</th>
        </tr>
    </thead>
    <tbody>
        @php
        $hoy = Carbon\Carbon::now();
        @endphp
        @foreach($trabajadores as $key => $row)
        <tr class="border">
            <td class="text-center">{{ $key + 1 }}</td>
            <td>{{ $row->apellidos }}</td>
            <td>{{ $row->nombres }}</td>
            <td class="text-center">{{ $row->tipo_documento }}</td>
            <td class="text-center">{{ $row->nro_documento }}</td>
            <td class="text-center">{{ $row->fecha_nacimiento ? Carbon\Carbon::parse($row->fecha_nacimiento)->format('d/m/Y') : '' }}</td>
            <td class="text-center">{{ $row->fecha_nacimiento ? $hoy->diffInYears($row->fecha_nacimiento) : '' }}</td>
            <td class="text-center">{{ $row->genero }}</td>
            <td class="text-center">{{ $row->correo }}</td>
            <td class="text-center">{{ $row->celular }}</td>
            @if($row->estado == 'ACTIVO')
            <td style="color: #29c486"> {{ $row->estado }}</td>
            @else
            <td style="color: #ff0000">{{ $row->estado }}</td>
            @endif

        </tr>
        @endforeach
    </tbody>
</table>

<style>
    table tr th {
        background: #27284f;
        color: #ffffff;
        text-align: center;
    }

    table tbody tr.border td {
        border: 1px solid #27284f;
    }

    .title {
        text-align: center;
        border: 1px solid #27284f;
        vertical-align: middle;
    }

    .text-center {
        text-align: center;
    }

    .text-right {
        text-align: right;
    }
</style>