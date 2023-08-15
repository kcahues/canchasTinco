

  // Mostrar u ocultar el botón de subir según el desplazamiento vertical
  window.addEventListener('scroll', function() {
    if (window.scrollY > 100) {
      document.getElementById('btnSubir').classList.add('show');
    } else {
      document.getElementById('btnSubir').classList.remove('show');
    }
  });

  // Volver al inicio de la página al hacer clic en el botón de subir
  document.getElementById('btnSubir').addEventListener('click', function() {
    window.scrollTo({ top: 0, behavior: 'smooth' });
  });



