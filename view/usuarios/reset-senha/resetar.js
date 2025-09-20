const form = document.getElementById("resetar");

form.addEventListener("submit", async function(event) {
  event.preventDefault();

  const dados = new FormData(form);

  // Mostrar loading
  document.getElementById("loading-overlay").style.display = "flex";

  try {
    const res = await fetch("/agendamento/api/usuarios/trocar-senha/index.php", {
      method: "POST",
      body: dados
    });

    const json = await res.json();

    // Alerta nativo do navegador
    alert(json.message);

  } catch (err) {
    alert("Erro: " + err.message);
  } finally {
    // Esconde loading sempre
    document.getElementById("loading-overlay").style.display = "none";
  }
});
