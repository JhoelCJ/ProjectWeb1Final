/* Archivo: js/funcionalidad_usuario.js */

function calcularEdad() {
    const inputFecha = document.getElementById("fecha_nacimiento");
    const inputEdad = document.getElementById("edad");

    if (inputFecha && inputEdad && inputFecha.value) {
        const hoy = new Date();
        const cumpleanos = new Date(inputFecha.value);
        let edad = hoy.getFullYear() - cumpleanos.getFullYear();
        const m = hoy.getMonth() - cumpleanos.getMonth();

        if (m < 0 || (m === 0 && hoy.getDate() < cumpleanos.getDate())) {
            edad--;
        }
        inputEdad.value = (edad >= 0) ? edad : 0;
    }
}

function validarCedula(cedula) {
    if (cedula.length !== 10) return false;
    const digito_region = parseInt(cedula.substring(0, 2));
    if (digito_region >= 1 && digito_region <= 24) {
        const ultimo_digito = parseInt(cedula.substring(9, 10));
        const pares = parseInt(cedula.substring(1, 2)) + parseInt(cedula.substring(3, 4)) + parseInt(cedula.substring(5, 6)) + parseInt(cedula.substring(7, 8));
        
        let numero1 = parseInt(cedula.substring(0, 1)) * 2; if (numero1 > 9) numero1 -= 9;
        let numero3 = parseInt(cedula.substring(2, 3)) * 2; if (numero3 > 9) numero3 -= 9;
        let numero5 = parseInt(cedula.substring(4, 5)) * 2; if (numero5 > 9) numero5 -= 9;
        let numero7 = parseInt(cedula.substring(6, 7)) * 2; if (numero7 > 9) numero7 -= 9;
        let numero9 = parseInt(cedula.substring(8, 9)) * 2; if (numero9 > 9) numero9 -= 9;
        
        const impares = numero1 + numero3 + numero5 + numero7 + numero9;
        const suma_total = pares + impares;
        const primer_digito_suma = String(suma_total).substring(0, 1);
        const decena = (parseInt(primer_digito_suma) + 1) * 10;
        let digito_validador = decena - suma_total;
        if (digito_validador === 10) digito_validador = 0;
        
        return digito_validador === ultimo_digito;
    }
    return false;
}

function validarYEnviarVertical(event) {
    event.preventDefault();
    const inputCedula = document.getElementsByName("cedula")[0];
    if (inputCedula && !validarCedula(inputCedula.value)) {
        alert("La cédula ingresada NO es válida.");
        return false;
    }
    if (typeof window.registrarUsuarioAjax === 'function') {
        window.registrarUsuarioAjax(event);
    }
}

function recargarVistaCorrecta() {
    const formBusqueda = document.getElementById('formBusqueda');
    
    if (formBusqueda) {
        window.buscarUsuariosAjax(null, 1);
    } else {
        cargarTab('paginas/visualizacionUs.php');
    }
}


window.eliminarUsuarioAjax = function(id) {
    if(confirm("¿Estás seguro de desactivar este usuario?")) {
        const formData = new FormData();
        formData.append('id', id);

        fetch('php/usuario/eliminar.php', { method: 'POST', body: formData })
        .then(res => res.text())
        .then(resp => {
            if(resp.trim() === 'exito') {
                alert("Usuario desactivado correctamente.");
                recargarVistaCorrecta();
            } else {
                alert("Error: " + resp);
            }
        });
    }
};

window.activarUsuarioAjax = function(id) {
    if(confirm("¿Deseas reactivar este usuario?")) {
        const formData = new FormData();
        formData.append('id', id);

        fetch('php/usuario/activar.php', { method: 'POST', body: formData })
        .then(res => res.text())
        .then(resp => {
            if(resp.trim() === 'exito') {
                alert("Usuario activado correctamente.");
                recargarVistaCorrecta();
            } else {
                alert("Error: " + resp);
            }
        });
    }
};

window.abrirModalEditarUsuario = function(id, nombre, apellido, usuario, cedula, idRol) {
    document.getElementById('e_id_user').value = id;
    document.getElementById('e_nombre').value = nombre;
    document.getElementById('e_apellido').value = apellido;
    document.getElementById('e_usuario').value = usuario;
    document.getElementById('e_cedula').value = cedula;
    
    const selectRol = document.getElementById('e_rol');
    if(idRol && idRol != 0) {
        selectRol.value = idRol; 
    } else {
        selectRol.value = ""; 
    }
    const inpNueva = document.querySelector('input[name="clave_nueva"]');
    const inpConfirm = document.querySelector('input[name="clave_confirm"]');
    const inpAdmin = document.querySelector('input[name="admin_password"]');

    if(inpNueva) inpNueva.value = "";
    if(inpConfirm) inpConfirm.value = "";
    if(inpAdmin) inpAdmin.value = "";

    document.getElementById('modalEditarUs').style.display = 'block';
};

window.actualizarUsuarioAjax = function(event) {
    event.preventDefault();
    
    const cedula = document.getElementById('e_cedula').value;
    if(typeof validarCedula === 'function' && !validarCedula(cedula)){
        alert("La cédula no es válida.");
        return;
    }

    const form = event.target;
    fetch(form.action, {
        method: 'POST',
        body: new FormData(form)
    })
    .then(res => res.text())
    .then(resp => {
        if(resp.trim() === 'exito') {
            alert("Usuario actualizado correctamente.");
            document.getElementById('modalEditarUs').style.display = 'none';
            
            if(typeof cargarTab === 'function') {
                cargarTab('paginas/visualizacionUs.php');
            } else {
                location.reload();
            }
        } else {
            alert(resp);
        }
    });
};

window.buscarUsuariosAjax = function(event, pagina = 1) {
    if(event) event.preventDefault();
    
    const criterioInput = document.getElementById('inputCriterio');
    const tbody = document.getElementById('tablaResultados');
    
    if (!criterioInput || !tbody) return;

    const criterio = criterioInput.value;

    tbody.innerHTML = '<tr><td colspan="6" class="w3-center" style="padding:20px"><i class="fa fa-spinner fa-spin w3-large"></i> Buscando página ' + pagina + '...</td></tr>';

    const formData = new FormData();
    formData.append('criterio', criterio);
    formData.append('pagina', pagina); 

    fetch('php/buscar_usuario_ajax.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.text())
    .then(html => {
        tbody.innerHTML = html;
    })
    .catch(err => {
        console.error(err);
        tbody.innerHTML = '<tr><td colspan="6" class="w3-center w3-text-red">Error en la conexión.</td></tr>';
    });
};