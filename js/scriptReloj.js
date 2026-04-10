// JavaScript Document
function actualizarReloj() {
  const opciones = {
    timeZone: 'America/Guayaquil',
    year: 'numeric',
    month: '2-digit',
    day: '2-digit',
    hour: '2-digit',
    minute: '2-digit',
    second: '2-digit'
  };

  const ahora = new Date();
  document.getElementById('reloj').innerHTML =
    ahora.toLocaleString('es-EC', opciones);
}

setInterval(actualizarReloj, 1000);
actualizarReloj();

