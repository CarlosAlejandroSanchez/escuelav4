<?php
require_once 'functions.php';
use Illuminate\Database\Capsule\Manager as DB;

$aviso = $esp = $mate = $hist = "";

echo'
<body onload="alumnos()">
';


if($loggedin)
{
    require_once 'header.php';
    if($rol == 1)
    {
        echo'
        <div class="container">
            <div class="card mt-6 mb-6">
                <header class="card-header">
                    <p class="card-header-title">
                        Selecciona a un alumno para eliminar sus calificaciones
                    </p>
                    <a href="#" class="card-header-icon" aria-label="more options">
                        <span class="icon">
                            <i class="fas fa-angle-down" aria-hidden="true"></i>
                        </span>
                    </a>
                </header>
                <div class="card-content">
                    <div class="content">
                        <form method="post"
                            <div class="field">
                                <label class="label">Alumno</label>
                                <div class="control">
                                    <div class="select is-medium">
                                        <select id="alumno">
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <button type="button" onclick="eliminar()" class="button is-link mt-5">eliminar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        ';
    }
    else
    {
        echo"<p class='is-size-5 is-center mt-6'>No tienes permisos para estar aquí <a href='index.php'>click aquí para regresar al inicio</a></p>";
    }
}
else{
    echo"<p class='is-size-5 is-center mt-6'>Necesitas una cuenta para usar este sistema <a href='login.php'>click aquí para regresar al login</a></p>";
}

echo'
    <script>
        function alumnos()
        {
            axios.post(`api/index.php/alumnos`, {
            })
            .then(resp => {
                if(resp.data.alumnos)
                {
                    const nombres = resp.data.nombres;
                    nombres.forEach(nombres => {
                        var miSelect=document.getElementById("alumno");
                        var miOption=document.createElement("option");
                        miOption.setAttribute("value",nombres.id_miembros);
                        miOption.setAttribute("label",nombres.nombre + " " + nombres.apellido);
                        miSelect.appendChild(miOption);
                    });
                }
                else
                {
                    alert("No hay alumnos, por favor registre por lo menos un alumno");
                }
            })
            .catch(error => {
                console.log(error);
            });
        }

        function eliminar()
        {
            axios.post(`api/index.php/eliminar`, {
                id: document.getElementById("alumno").value
            })
            .then(resp => {
                alert(resp.data.mensaje)
            })
            .catch(error => {
                console.log(error);
            });
        }
    </script>

    </body>
</html>
';
?>