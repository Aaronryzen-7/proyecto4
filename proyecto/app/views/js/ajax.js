const formularios_ajax = document.querySelectorAll(".FormularioAjax"); // seleccionamos todos los formularios

formularios_ajax.forEach(formularios=>{ // le agregamos a cada uno el evento submit

    formularios.addEventListener("submit", function(e){
        e.preventDefault(); // evitamos que se cargue de nuevo la pagina

        Swal.fire({ // esto es sweeralert
            title: 'Estas Seguro?',
            text: "Quieres Realizar la Accion Solicitada",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Si, realizar',
            cancelButtonText: 'No, cancelar'
        }).then((result) => {
            if (result.isConfirmed) { // si el resultado es confirmado
               
                let data = new FormData(this); // data guarda los datos o el valor del formulario
                let method = this.getAttribute("method"); // guarda el metodo que tenia el formulario puesto en el html
                let action = this.getAttribute("action"); // guarda la accion o direccion que tiene el formulario en el html

                let encabezados = new Headers(); // los encabezados siempre van

                let config = {
                    method: method,
                    headers: encabezados,
                    mode: 'cors',
                    cache: 'no-cache',
                    body: data
                } // config siempre la preparamos con todos los permisos

                fetch(action, config) // enviamos el json por fetch, se pasa la direccion con los demas datos
                .then(respuesta => respuesta.json()) // la respuesta recibida se transformara en json
                .then(respuesta => {
                    return alertas_ajax(respuesta); // retorna la respuesta json dependiendo del resultado de la funcion
                })

            }
        })


    })

});



function alertas_ajax(alerta){ // funcion que dependiendo de la respuesta json, lanzara un sweetalert diferente
    if(alerta.tipo=="simple"){

        Swal.fire({
            icon: alerta.icono,
            title: alerta.titulo,
            text: alerta.texto,
            confirmButtonText: 'Aceptar'
        });

    }else if(alerta.tipo=="recargar"){

        Swal.fire({
            icon: alerta.icono,
            title: alerta.titulo,
            text: alerta.texto,
            confirmButtonText: 'Aceptar'
        }).then((result) => {
            if(result.isConfirmed){
                location.reload();
            }
        });

    }else if(alerta.tipo=="limpiar"){

        Swal.fire({
            icon: alerta.icono,
            title: alerta.titulo,
            text: alerta.texto,
            confirmButtonText: 'Aceptar'
        }).then((result) => {
            if(result.isConfirmed){
                document.querySelector(".FormularioAjax").reset();
            }
        });

    }else if(alerta.tipo=="redireccionar"){
        window.location.href=alerta.url;
    }
}


// boton para cerrar sesion, es para cuando se le de salga una confirmacion para cerrar la sesion



let btn_exit = document.getElementById("btn_exit"); // el boton de salir del dom

btn_exit.addEventListener("click", function(e){ // se le agrega la funcion o evento click
    e.preventDefault(); // para que no recargue la pagina de una

    Swal.fire({ // esto es sweeralert
        title: 'Quieres salir del sistema?',
        text: "La sesion actual se cerrara y saldras del sistema",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Si, salir',
        cancelButtonText: 'Cancelar'
    }).then((result) => { // se envia la alerta de querer salir si o no
        if (result.isConfirmed) { // si el resultado es confirmado
           
            let url = this.getAttribute("href"); // tomara el href del elemento boton de salir y se guardara en la variable url
            window.location.href=url; // aqui direccionamos al valor de url
        }
    })
});