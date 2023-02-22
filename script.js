function validarFormulario() {
    let nombre = document.getElementById('nombre').value;
    let email = document.getElementById('email').value;
    let mensaje = document.getElementById('mensaje').value;
  
    if (nombre === '' || email === '' || mensaje === '') {
      alert('Por favor, completa todos los campos antes de enviar el formulario.');
      return false;
    }
    return true;
  }
