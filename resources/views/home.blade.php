@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card text-white bg-dark mb-3">
                    <div class="card-header">Redes conectadas BrandMe</div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        ¡Hola {{ $user['name'] }}!

                        <br><br>

                        Bienvenido a Redes Conectadas

                        <br><br>

                        Gracias por ingresar con {{ $user['provider'] }}, haremos llegar las notificaciones al mail 
                        {{ $user['email'] }}

                    </div>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        $(document).ready(function(){
            let time = '{{ $sleep }}';
            let route = '{{ route('refresh-token') }}'

            setTimeout(function() { 
                
                $.ajax({
                    url : route,
                    type : 'POST',
                    data : {
                        '_token': '{{ csrf_token() }}',
                    },
                    dataType:'json',
                    success : function(data) {              
                        alert('Exito al refrescar el sitio');
                    },
                    error : function(request, error)
                    {
                        alert("Error al refrescar el sitio");
                    }
                });

            }, time * 1000 * .90); //Lo convertimos a milisegundos
        });
    </script>
@endsection
