
function cargarTab(ruta) {
    const contenedorDinamico = document.getElementById('contenido-dinamico');
    const seccionInicio = document.getElementById('seccion-inicio');
    const loader = '<div class="w3-center w3-padding-32"><i class="fa fa-spinner fa-spin w3-xxlarge"></i><br>Cargando...</div>';

  
    seccionInicio.style.display = 'none';
    contenedorDinamico.style.display = 'block';
    contenedorDinamico.innerHTML = loader;

 
    fetch(ruta)
        .then(response => {
            if (!response.ok) throw new Error('Error en la red');
            return response.text();
        })
        .then(html => {
        
            const htmlProcesado = procesarHTML(html);
            contenedorDinamico.innerHTML = htmlProcesado;
      
            ejecutarScripts(contenedorDinamico);

       
            window.scrollTo(0, 0);
        })
        .catch(error => {
            console.error('Error:', error);
            contenedorDinamico.innerHTML = '<div class="w3-panel w3-red"><h3>Error</h3><p>No se pudo cargar la sección.</p></div>';
        });
}


function mostrarInicio() {
    document.getElementById('contenido-dinamico').style.display = 'none';
    document.getElementById('contenido-dinamico').innerHTML = '';
    document.getElementById('seccion-inicio').style.display = 'block';
}



function procesarHTML(html) {
    const tempDiv = document.createElement('div');
    tempDiv.innerHTML = html;

    const menus = tempDiv.querySelectorAll('nav.menu, .w3-top'); 
    menus.forEach(el => el.remove());


    let contenido = tempDiv.querySelector('#contenido-spa') || 
                    tempDiv.querySelector('.contenedor') || 
                    tempDiv.querySelector('.w3-container') || 
                    tempDiv.querySelector('.w3-content') ||
                    tempDiv.querySelector('table');

    let htmlFinal = contenido ? (contenido.outerHTML || contenido.innerHTML) : tempDiv.innerHTML;
    
    if(contenido && contenido.tagName === 'TABLE') {
        htmlFinal = '<div class="w3-container w3-padding-32">' + htmlFinal + '</div>';
    }

    htmlFinal = htmlFinal.replace(/src="\.\.\//g, 'src="');
    htmlFinal = htmlFinal.replace(/href="\.\.\//g, 'href="');
    htmlFinal = htmlFinal.replace(/action="\.\.\//g, 'action="');
    
    return htmlFinal;
}


function ejecutarScripts(contenedor) {
    const scripts = contenedor.querySelectorAll('script');
    scripts.forEach(oldScript => {
        const newScript = document.createElement('script');
        Array.from(oldScript.attributes).forEach(attr => newScript.setAttribute(attr.name, attr.value));
        newScript.appendChild(document.createTextNode(oldScript.innerHTML));
        oldScript.parentNode.replaceChild(newScript, oldScript);
    });
}


window.editarRol = function(id, nombre, desc, permisos) {
    console.log("Editando rol:", id);
    
    const campoId = document.getElementById('id_rol_edit');
    if (!campoId) {
        console.error("No se encontró el formulario de roles");
        return;
    }

    campoId.value = id;
    document.getElementById('nombre_rol').value = nombre;
    document.getElementById('descripcion_rol').value = desc;

    const checkboxes = document.querySelectorAll('input[type=checkbox]');
    checkboxes.forEach(cb => cb.checked = false);
    if (permisos && Array.isArray(permisos)) {
        permisos.forEach(pId => {
            const chk = document.getElementById('perm_' + pId);
            if (chk) chk.checked = true;
        });
    }
    
    const contenedor = document.getElementById('contenido-dinamico');
    if(contenedor) contenedor.scrollIntoView({ behavior: 'smooth' });
};

window.limpiarFormRol = function() {
    const campoId = document.getElementById('id_rol_edit');
    if (campoId) {
        campoId.value = '';
        document.getElementById('nombre_rol').value = '';
        document.getElementById('descripcion_rol').value = '';
        document.querySelectorAll('input[type=checkbox]').forEach(cb => cb.checked = false);
    }
};

window.calcularEdad = function() {
    let fechaNac = document.getElementById("fecha_nacimiento").value;
    let edadInput = document.getElementById("edad");
    if(fechaNac) {
        let hoy = new Date();
        let cumpleanos = new Date(fechaNac);
        let edad = hoy.getFullYear() - cumpleanos.getFullYear();
        let m = hoy.getMonth() - cumpleanos.getMonth();
        if (m < 0 || (m === 0 && hoy.getDate() < cumpleanos.getDate())) {
            edad--;
        }
        edadInput.value = edad;
    }
}

function realizarBusqueda(event) {
    event.preventDefault();

    const form = document.getElementById('formBusqueda');
    const formData = new FormData(form);
    const tbody = document.querySelector('#tablaUsuarios tbody');

    tbody.innerHTML = '<tr><td colspan="9" class="w3-center"><i class="fa fa-spinner fa-spin"></i> Buscando...</td></tr>';

    fetch('php/buscar_usuario_ajax.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(html => {
        tbody.innerHTML = html;
    })
    .catch(error => {
        console.error('Error:', error);
        tbody.innerHTML = '<tr><td colspan="9" class="w3-center w3-text-red">Error al realizar la búsqueda.</td></tr>';
    });
}

window.calcularPromedio = function() {
    let n1 = parseFloat(document.getElementById('nota1').value) || 0;
    let n2 = parseFloat(document.getElementById('nota2').value) || 0;
    let n3 = parseFloat(document.getElementById('nota3').value) || 0;
    
    let promedio = (n1 + n2 + n3) / 3;
    document.getElementById('promedio_vista').value = promedio.toFixed(2);

    let divSupletorio = document.getElementById('campo_supletorio');
    let inputSupletorio = document.getElementById('supletorio');

    if (promedio < 14) {
        divSupletorio.style.display = 'block';
        inputSupletorio.required = false;
        document.getElementById('mensaje_estado').innerHTML = "<span style='color:orange'>Requiere Supletorio</span>";
    } else {
        divSupletorio.style.display = 'none';
        inputSupletorio.value = '';
        document.getElementById('mensaje_estado').innerHTML = "<span style='color:green'>Aprobado</span>";
    }
}

window.editarRol = function(id, nombre, desc, permisos, estado) {
    const campoId = document.getElementById('id_rol_edit');
    if (!campoId) return;

    campoId.value = id;
    document.getElementById('nombre_rol').value = nombre;
    document.getElementById('descripcion_rol').value = desc;


    const checkboxes = document.querySelectorAll('input[type=checkbox]');
    checkboxes.forEach(cb => cb.checked = false);
    if (permisos && Array.isArray(permisos)) {
        permisos.forEach(pId => {
            const chk = document.getElementById('perm_' + pId);
            if (chk) chk.checked = true;
        });
    }

    const areaEstado = document.getElementById('area_estado_rol');
    const btnActivar = document.getElementById('btn_activar_rol');
    const btnDesactivar = document.getElementById('btn_desactivar_rol');

    if (areaEstado && btnActivar && btnDesactivar) {
        areaEstado.style.display = 'block';

        if (estado === 'activo') {
            btnActivar.style.display = 'none';
            btnDesactivar.style.display = 'inline-block';
        } else {
            btnActivar.style.display = 'inline-block';
            btnDesactivar.style.display = 'none';
        }
    }

    const contenedor = document.getElementById('contenido-dinamico');
    if(contenedor) contenedor.scrollIntoView({ behavior: 'smooth' });
};


window.limpiarFormRol = function() {
    const campoId = document.getElementById('id_rol_edit');
    if (campoId) {
        campoId.value = '';
        document.getElementById('nombre_rol').value = '';
        document.getElementById('descripcion_rol').value = '';
        document.querySelectorAll('input[type=checkbox]').forEach(cb => cb.checked = false);

        const areaEstado = document.getElementById('area_estado_rol');
        if(areaEstado) areaEstado.style.display = 'none';
    }
};

window.cambiarEstadoRol = function(accion) {
    const idRol = document.getElementById('id_rol_edit').value;
    
    if(!idRol) return;
    if(!confirm('¿Estás seguro de que deseas ' + accion + ' este rol?')) return;

    const formData = new FormData();
    formData.append('id', idRol);
    formData.append('accion', accion);

    fetch('php/roles/cambiar_estado.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.text())
    .then(resp => {
        if(resp.trim() === 'exito') {
            alert('Estado actualizado correctamente.');
            cargarTab('paginas/gestionarRoles.php'); 
        } else {
            alert(resp);
        }
    })
    .catch(err => console.error(err));
};

window.guardarRolAjax = function(event) {
    event.preventDefault();
    const form = event.target;
    
    fetch(form.action, {
        method: 'POST',
        body: new FormData(form)
    })
    .then(res => res.text()) 
    .then(text => {

        alert('Rol guardado/actualizado');
        cargarTab('paginas/gestionarRoles.php');
    });
};

window.abrirModalNotas = function(id, nombre, n1, n2, n3, suple) {
    document.getElementById('m_id_nota').value = id;
    document.getElementById('m_nombre_alumno').innerText = nombre;
    document.getElementById('m_nota1').value = n1;
    document.getElementById('m_nota2').value = n2;
    document.getElementById('m_nota3').value = n3;
    document.getElementById('m_supletorio').value = suple;
    
    window.calcularPromedioModal();
    
    document.getElementById('modalNotas').style.display = 'block';
};

window.calcularPromedioModal = function() {
    let n1 = parseFloat(document.getElementById('m_nota1').value) || 0;
    let n2 = parseFloat(document.getElementById('m_nota2').value) || 0;
    let n3 = parseFloat(document.getElementById('m_nota3').value) || 0;
    
    let promedio = (n1 + n2 + n3) / 3;
    document.getElementById('m_promedio').innerText = promedio.toFixed(2);
    
    let divSuple = document.getElementById('m_div_supletorio');
    let msgEstado = document.getElementById('m_mensaje_estado');
    
    if (promedio < 14) {
        divSuple.style.display = 'block';
        msgEstado.innerHTML = "<span style='color:orange'>Promedio bajo. Se requiere supletorio.</span>";
    } else {
        divSuple.style.display = 'none';
        document.getElementById('m_supletorio').value = ''; 
        msgEstado.innerHTML = "<span style='color:green'>Aprobado Directamente.</span>";
    }
};

window.guardarNotaAjax = function(event) {
    event.preventDefault();
    const form = event.target;
    
    fetch(form.action, {
        method: 'POST',
        body: new FormData(form)
    })
    .then(res => res.text())
    .then(resp => {
        if(resp.trim() === 'exito') {
            alert('Calificación guardada correctamente.');
            document.getElementById('modalNotas').style.display = 'none';
            cargarTab('paginas/gestionNotas.php'); 
        } else {
            alert(resp);
        }
    })
    .catch(err => console.error(err));
};

window.registrarUsuarioAjax = function(event) {
    event.preventDefault();
    
    const form = event.target;
    const formData = new FormData(form);

    fetch(form.action, {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(respuesta => {
        if (respuesta.trim() === 'exito') {
            alert("Usuario registrado correctamente.");
            cargarTab('paginas/visualizacionUs.php'); 
        } else {
            alert("Hubo un error: " + respuesta);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert("Error de conexión con el servidor.");
    });
};