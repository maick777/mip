@extends('crudbooster::admin_template')

@section('content')


<div class='panel panel-default'>
    <div class='panel-body'>
        <div class="row">

            <div class="col-lg-8 col-md-6 col-sm-6 col-sx-12">
                <div class="carousel fade-carousel carousel-fade slide" data-ride="carousel" data-interval="8000" id="bs-carousel">
                    <!-- Overlay -->

                    <!-- Indicators -->
                    <ol class="carousel-indicators">
                        <li data-target="#bs-carousel" data-slide-to="0" class="active"></li>
                        @foreach($sliders as $key => $slider)
                        <li data-target="#bs-carousel" data-slide-to="{{$key + 1}}"></li>
                        @endforeach
                    </ol>

                    <!-- Wrapper for slides -->
                    <div class="carousel-inner">

                        <div class="item slides active">

                            <div class="slide-1" style="background-image: url( {{ asset('vendor/crudbooster/assets/slider-'.strtok(Session::get('theme_color'), '-').'-'.rand(1, 5).'.jpg') }} )"></div>
                            <div class="hero">
                                <hgroup>
                                    <h1>¡Bienvenido!</h1>
                                    <h3>Estimado(a)</h3>
                                </hgroup>
                                <span class="btn btn-hero btn-lg" role="button"> {{ CRUDBooster::myName() }}</span>
                            </div>
                        </div>


                        @foreach($sliders as $slider)

                        <div class="item slides">
                            <div class="slide-1" style="background-image: url( {{ asset('vendor/crudbooster/assets/slider-'.strtok(Session::get('theme_color'), '-').'-'.rand(1, 5).'.jpg') }} )"></div>
                            <div class="hero">
                                <hgroup>
                                    <h1>{{ $slider->titulo }}</h1>
                                    <h3>{{ $slider->texto }}</h3>
                                </hgroup>
                                <span class="btn btn-hero btn-lg" role="button"> {{ $slider->libro }}</span>
                            </div>
                        </div>

                        @endforeach

                    </div>

                    <!-- Controls -->
                    <a class="left carousel-control" href="#bs-carousel" role="button" data-slide="prev">
                        <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
                        <span class="sr-only">Previous</span>
                    </a>
                    <a class="right carousel-control" href="#bs-carousel" role="button" data-slide="next">
                        <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
                        <span class="sr-only">Next</span>
                    </a>

                </div>
            </div>

            <div class="col-lg-4 col-md-6 col-sm-6 col-sx-12">
                <div class="list-container mt-4">

                    <h1 class="title">
                        <span class="chispa">
                            <img src="{{ asset('vendor/crudbooster/assets/torta.png') }}" alt="torta" width="60px">
                        </span>
                    </h1>

                    <h3 class="title">~ CUMPLEAÑOS ~</h3>

                    <ul class="list-group">
                        @foreach($trabajadors_por_mes as $mes => $lista_trabajadors)
                        <center>
                           <h6 class="sub-title">
                            @php 
                            $fecha = Carbon\Carbon::create(null, $mes, 1);
                            $nombreMes = $fecha->formatLocalized('%B');
                            @endphp
                            {{ $nombreMes }}
                           </h6>
                        </center>
                        @foreach($lista_trabajadors as $list_key => $row)
                        <li class="list-group-item">
                            <a data-lightbox="roadtrip" rel="fotos" title="Foto: {{ $row->nombres }} {{ $row->apellidos }}" href="{{ ($row->foto) ? $path.'/'.$row->foto : asset('vendor/crudbooster/avatar.jpg') }}">
                                <img width="30px" class="" src="{{ ($row->foto) ? $path.'/'.$row->foto : asset('vendor/crudbooster/avatar.jpg') }}">
                            </a>
                            <small>
                            {{ $row->apellidos }}, {{ strtok($row->nombres, " ") }}
                            </small>

                            @if(isset($row->fecha_nacimiento))
                            @if( Carbon\Carbon::parse($row->fecha_nacimiento)->format('m-d') == $mes_actual.'-'.$dia_actual)

                            <span class="badge animated infinite flipInY chispa">
                                <small>
                                    <i class="fa fa-birthday-cake text-danger"></i> FELÍZ CUMPLEAÑOS!
                                </small>
                            </span>

                            @else
                            <span class="badge">
                                <small>
                                @php 
                                   $cumple =  Carbon\Carbon::parse($row->fecha_nacimiento)->formatLocalized('%A %d')
                                @endphp
                                {{ $cumple }}
                                </small>
                            </span>
                            @endif
                            @endif

                        </li>
                        @endforeach
                        @endforeach

                    </ul>
                </div>
            </div>
        </div>

    </div>
</div>

@push('bottom')
<link rel='stylesheet' href='{{asset("vendor/crudbooster/assets/css/slider.css")}}' />
@endpush


@endsection