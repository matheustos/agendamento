const form = document.getElementById("agenda");
const alerta = document.getElementById("alerta");

form.addEventListener("submit", async function(event) {
event.preventDefault();

const dados = new FormData(form);

try {
    const res = await fetch("../../api/novo/index.php", {
    method: "POST",
    body: dados
    });

    const json = await res.json();

    // Define a classe do alerta (sucesso ou erro)
    alerta.className = "alerta " + (json.status ? "sucesso" : "erro");
    alerta.textContent = json.message;

    // Mostra a barra
    alerta.classList.add("show");

    // Esconde automaticamente depois de 5 segundos
    setTimeout(() => {
    alerta.classList.remove("show");
    }, 5000);

} catch (err) {
    alerta.className = "alerta erro show";
    alerta.textContent = "Erro: " + err.message;
    setTimeout(() => {
    alerta.classList.remove("show");
    }, 5000);
}
});