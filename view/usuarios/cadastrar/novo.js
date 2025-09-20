const form = document.getElementById("cadastro");

form.addEventListener("submit", async function(event) {
  event.preventDefault();

  const dados = new FormData(form);

  // Mostrar loading
  document.getElementById("loading-overlay").style.display = "flex";

  try {
    const res = await fetch("/agendamento/api/usuarios/cadastrar/index.php", {
      method: "POST",
      body: dados
    });

    const json = await res.json();

    // Alerta nativo do navegador
    alert(json.message);
    location.reload();

  } catch (err) {
    alert("Erro: " + err.message);
  }finally {
    // Esconde loading sempre
    document.getElementById("loading-overlay").style.display = "none";
  }
});