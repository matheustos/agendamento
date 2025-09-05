const form = document.getElementById("agenda");

form.addEventListener("submit", async function(event) {
  event.preventDefault();

  const dados = new FormData(form);

  try {
    const res = await fetch("/agendamento/api/novo/index.php", {
      method: "POST",
      body: dados
    });

    const json = await res.json();

    // Alerta nativo do navegador
    alert(json.message);

  } catch (err) {
    alert("Erro: " + err.message);
  }
});
