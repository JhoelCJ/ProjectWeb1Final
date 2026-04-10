//FUNCIONES DE ADMINISTRADOR


window.registrarAlumnoAjax = function(event) {
    event.preventDefault();
    const form = event.target;

    const cedula = form.querySelector('[name="cedula"]');
    if (cedula && typeof validarCedula === 'function') {
        if (!validarCedula(cedula.value)) {
            alert("La cédula ingresada no es válida.");
            return;
        }
    }

    fetch(form.action, { method: 'POST', body: new FormData(form) })
    .then(res => res.text())
    .then(resp => {
        if(resp.trim() === 'exito') {
            alert("Alumno registrado correctamente.");
            cargarTab('paginas/alumno.php');
        } else {
            alert("Error: " + resp);
        }
    })
    .catch(err => alert("Error de red: " + err));
};

window.matricularAlumnoAjax = function(event) {
    event.preventDefault();
    const form = event.target;
    const btn = form.querySelector('button[type="submit"]');
    const textoOriginal = btn.innerHTML;
    btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Procesando...';
    btn.disabled = true;

    fetch(form.action, { method: 'POST', body: new FormData(form) })
    .then(res => res.text())
    .then(resp => {
        btn.innerHTML = textoOriginal;
        btn.disabled = false;

        if(resp.trim() === 'exito') {
            alert("Alumno matriculado exitosamente.");
            cargarTab('paginas/asignarCurso.php'); 
        } else {
            alert("Atención: " + resp); 
        }
    })
    .catch(err => {
        btn.innerHTML = textoOriginal;
        btn.disabled = false;
        alert("Error de conexión: " + err);
    });
};

window.guardarCursoAjax = function(event) {
    event.preventDefault();
    const form = event.target;
    const boton = form.querySelector('button[type="submit"]');
    const textoOriginal = boton.innerHTML;
    
    boton.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Guardando...';
    boton.disabled = true;

    fetch(form.action, { method: 'POST', body: new FormData(form) })
    .then(res => res.text())
    .then(resp => {
        boton.innerHTML = textoOriginal;
        boton.disabled = false;

        if(resp.trim() === 'exito') {
            alert("Operación exitosa.");
            document.getElementById('modalCurso').style.display = 'none';
            cargarTab('paginas/visualizacionCursos.php');
        } else {
            alert(resp);
        }
    });
};

window.abrirModalCurso = function(id = '', nombre = '', capacidad = '', instructor = '', especialidad = '') {
    document.getElementById('c_id_curso').value = id;
    document.getElementById('c_nombre').value = nombre;
    document.getElementById('c_capacidad').value = capacidad;
    document.getElementById('c_instructor').value = instructor;
    document.getElementById('c_especialidad').value = especialidad;
    
    const titulo = document.getElementById('tituloModalCurso');
    if (id) {
        titulo.innerHTML = "<i class='fa fa-pencil'></i> Editar Curso";
    } else {
        titulo.innerHTML = "<i class='fa fa-plus'></i> Nuevo Curso";
    }
    document.getElementById('modalCurso').style.display = 'block';
};

window.cambiarEstadoCurso = function(id, accion) {
    if(!confirm("¿Estás seguro de " + accion + " este curso?")) return;
    const formData = new FormData();
    formData.append('id', id);
    formData.append('accion', accion);

    fetch('php/curso/estado_curso.php', { method: 'POST', body: formData })
    .then(res => res.text())
    .then(resp => {
        if(resp.trim() === 'exito') cargarTab('paginas/visualizacionCursos.php');
        else alert("Error: " + resp);
    });
};

//FUNCIONES DE ALUMNO

window.inscribirseCurso = function(idCurso) {
    if(!confirm("¿Confirmas la inscripción?")) return;
    
    const formData = new FormData();
    formData.append('id_curso', idCurso);

    fetch('php/alumno/procesar_inscripcion.php', { method: 'POST', body: formData })
    .then(res => res.text())
    .then(resp => {
        if(resp.trim() === 'exito') {
            alert("¡Inscripción realizada!");
            cargarTab('paginas/inscripcionCurso.php'); 
        } else {
            alert("Error: " + resp);
        }
    });
};

window.actualizarPerfilAlumno = function(e) {
    e.preventDefault();
    const form = e.target;
    const email = form.querySelector('[name="email"]').value;
    
    if (!email.endsWith('@espe.edu.ec')) {
        alert("El correo debe ser @espe.edu.ec");
        return;
    }

    const boton = form.querySelector('button[type="submit"]');
    const textoOriginal = boton.innerHTML;
    boton.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Guardando...';
    boton.disabled = true;

    fetch('php/alumno/actualizar_perfil.php', { method: 'POST', body: new FormData(form) })
    .then(res => res.text())
    .then(resp => {
        boton.innerHTML = textoOriginal;
        boton.disabled = false;

        if(resp.trim() === 'exito') {
            alert("Perfil actualizado.");
            cargarTab('paginas/perfil.php');
        } else {
            alert("Error: " + resp);
        }
    });
};

window.abrirModalEditarAlumno = function(id, nombre, apellido, email, telefono, direccion) {
    document.getElementById('ea_id').value = id;
    document.getElementById('ea_nombre').value = nombre;
    document.getElementById('ea_apellido').value = apellido;
    document.getElementById('ea_email').value = email;
    document.getElementById('ea_telefono').value = telefono;
    document.getElementById('ea_direccion').value = direccion;
    
    document.getElementById('modalEditarAlumno').style.display = 'block';
};

window.editarAlumnoAdminAjax = function(event) {
    event.preventDefault();
    const form = event.target;
    
    fetch('php/alumno/actualizar_admin.php', { method: 'POST', body: new FormData(form) })
    .then(res => res.text())
    .then(resp => {
        if(resp.trim() === 'exito') {
            alert("Datos actualizados correctamente.");
            document.getElementById('modalEditarAlumno').style.display = 'none';
            cargarTab('paginas/listadoAlumnos.php');
        } else {
            alert(resp);
        }
    });
};

window.cambiarEstadoAlumno = function(id, accion) {
    if(!confirm("¿Estás seguro de " + accion + " a este alumno?")) return;

    const formData = new FormData();
    formData.append('id', id);
    formData.append('accion', accion);

    fetch('php/alumno/estado_alumno.php', { method: 'POST', body: formData })
    .then(res => res.text())
    .then(resp => {
        if(resp.trim() === 'exito') {
            cargarTab('paginas/listadoAlumnos.php');
        } else {
            alert("Error: " + resp);
        }
    });
};


window.imprimirReporteRapido = function(tipo) {
    let cantidad = prompt("¿Cuántos registros desea generar en el PDF?", "100");

    if (cantidad != null && cantidad.trim() !== "") {
        if (isNaN(cantidad) || cantidad <= 0) cantidad = 100;
        window.open('php/reportes/rep_' + tipo + '.php?limit=' + cantidad, '_blank');
    }
};

window.generarReporte = function(tipo) {
    let input = document.getElementById('cant_' + tipo);
    
    let cantidad = (input && input.value) ? input.value : 100;
    
    if (cantidad < 1) cantidad = 100;

    window.open('php/reportes/rep_' + tipo + '.php?limit=' + cantidad, '_blank');
};

window.imprimirReporteRapido = function(tipo) {
    let cantidad = prompt("¿Cuántos registros desea generar en el PDF?", "100");
    if (cantidad != null && cantidad.trim() !== "") {
        if (isNaN(cantidad) || cantidad <= 0) cantidad = 100;
        window.open('php/reportes/rep_' + tipo + '.php?limit=' + cantidad, '_blank');
    }
};