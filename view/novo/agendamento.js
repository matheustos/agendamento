const form = document.getElementById("agenda");

form.addEventListener("submit", async function(event) {
  event.preventDefault();

  const token = localStorage.getItem('token');
  const dados = new FormData(form);

  try {
    const res = await fetch("/agendamento/api/novo/index.php", {
      method: "POST",
      body: dados,
      headers: {
        'Authorization': `Bearer ${token}` // o token vai no header
      }
    });

    const json = await res.json();

    // Alerta nativo do navegador
    alert(json.message);
    location.reload();

  } catch (err) {
    alert("Erro: " + err.message);
  }
});
