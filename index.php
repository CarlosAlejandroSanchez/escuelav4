<?php
require_once 'functions.php';
use Illuminate\Database\Capsule\Manager as DB;

echo'
<body onload="calificación()">
';

if($loggedin){
    require_once 'header.php';
    echo'
    <div class="container">
    ';

    if($rol == 1)
    {
        echo'
        <div class="card mt-6 mb-6">
            <header class="card-header">
                <p class="card-header-title">
                    Ingresa los datos para registrar a un alumno (el usuario será el primer nombre y apellido sin espacios y la contraseña "escuela123")
                </p>
                <a href="#" class="card-header-icon" aria-label="more options">
                    <span class="icon">
                        <i class="fas fa-angle-down" aria-hidden="true"></i>
                    </span>
                </a>
            </header>
            <div class="card-content">
                <div class="content">
                    <form method="post" action="index.php">
                        <div class="field">
                            <label class="label mt-4">Nombre</label>
                            <div class="control">
                                <input class="input" type="text" maxlength="45" id="nombre" placeholder="Nombre">
                            </div>
                            <label class="label mt-4">Segundo nombre</label>
                            <div class="control">
                                <input class="input" type="text" maxlength="45" id="nombre2" placeholder="Segundo nombre">
                            </div>
                            <label class="label mt-4">Apellido Paterno</label>
                            <div class="control">
                                <input class="input" type="text" maxlength="45" id="apellido" placeholder="Apellido Paterno">
                            </div>
                            <label class="label mt-4">Apellido Materno</label>
                            <div class="control">
                                <input class="input" type="text" maxlength="45" id="apellido2" placeholder="Apellido Materno">
                            </div>
                        </div>
                        <button type="button" onclick="añadir()" class="button is-link mt-3">Registrar alumno</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    ';
    }
    else
    {
        $calificaciones = DB::table('calificaciones')->where('miembros_id_miembros', $id)->first();

        echo'
        <div class="card mt-6 mb-6">
            <header class="card-header">
                <p class="card-header-title">
                    Calificaciones y promedio general
                </p>
                <a href="#" class="card-header-icon" aria-label="more options">
                    <span class="icon">
                        <i class="fas fa-angle-down" aria-hidden="true"></i>
                    </span>
                </a>
            </header>
            <div class="card-content">
                <div class="content">
                    <form>
                        <div class="field">
                            <div class="control">
                                <input class="input is-hidden" type="text" id="id" placeholder="id" value="'.$id.'" readonly>
                            </div>
                            <label class="label mt-4">Español</label>
                            <div class="control">
                                <input class="input" type="number" max="10" id="esp" placeholder="Español" readonly>
                            </div>
                            <label class="label mt-4">Matemáticas</label>
                            <div class="control">
                                <input class="input" type="number" max="10" id="mate" placeholder="Matemáticas" readonly>
                            </div>
                            <label class="label mt-4">Historia</label>
                            <div class="control">
                                <input class="input" type="number" max="10" id="hist" placeholder="Historia" readonly>
                            </div>
                            <label class="label mt-6">Promedio General</label>
                            <div class="control">
                                <input class="input" type="number" max="10" id="prom" readonly>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    ';
    }
}
else{
    echo"<p class='is-size-5 is-center mt-6'>Necesitas una cuenta para usar este sistema <a href='login.php'>click aquí para regresar al login</a></p>";
}

echo'
    <script>
        function añadir()
        {
            axios.post(`api/index.php/añadir`, {
                nombre: document.forms[0].nombre.value,
                nombre2: document.forms[0].nombre2.value,
                apellido: document.forms[0].apellido.value,
                apellido2: document.forms[0].apellido2.value
            })
            .then(resp => {
                if(resp.data.vacio)
                {
                    alert(resp.data.mensaje);
                }
                else
                {
                    if(resp.data.validar)
                    {
                        alert(resp.data.mensaje);
                    }
                    else
                    {
                        alert(resp.data.mensaje);
                    }
                }
            })
            .catch(error => {
                console.log(error);
            });
        }
        function calificación()
        {
            axios.post(`api/index.php/calificacion/${document.forms[0].id.value}`, {
            })
            .then(resp => {
                if(resp.data.existe)
                {
                    document.getElementById("esp").value = resp.data.esp;
                    document.getElementById("mate").value = resp.data.mate;
                    document.getElementById("hist").value = resp.data.hist;
                    document.getElementById("prom").value = resp.data.prom;
                }
                else
                {
                    alert(resp.data.mensaje)
                }
            })
            .catch(error => {
                console.log(error);
            });
        }
    </script>

    </body>
</html>
';