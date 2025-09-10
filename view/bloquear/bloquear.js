const form = document.getElementById("agenda");
const bloqDiv = document.getElementById("bloq");

// Função para exibir bloqueios na tela
function exibirBloqueios() {
    fetch("/agendamento/api/bloquear/buscar/index.php")
        .then(res => res.json())
        .then(bloqueios => {
            // Limpa a div antes de renderizar
            bloqDiv.innerHTML = `
                <h2><span>🕓</span>Períodos Bloqueados</h2>
                <p>Lista de todos os bloqueios ativos na agenda do mês</p>
            `;

            // Cria cards para cada bloqueio
            bloqueios.forEach(b => {
                const card = document.createElement("div");
                card.classList.add("card-bloqueio"); // estilize no CSS
                card.innerHTML = `
                    <p><strong>Data:</strong> ${b.data}</p>
                    <p><strong>Hora:</strong> ${b.horario}</p>
                    <p><strong>Motivo:</strong> ${b.obs || "Sem observação"}</p>
                `;
                bloqDiv.appendChild(card);
            });
        })
        .catch(err => console.error("Erro ao buscar bloqueios:", err));
}

// Listener do formulário
form.addEventListener("submit", (e) => {
    e.preventDefault();
    const formData = new FormData(form);

    fetch("/agendamento/api/bloquear/index.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.json())
    .then(res => {
        form.reset();

        if (res.status === true) {
            alert(res.message); // exibe mensagem de sucesso do backend
            exibirBloqueios();  // atualiza os cards na tela
        } else {
            alert("Erro: " + res.message); // exibe mensagem de erro do backend
        }
    })
    .catch(err => console.error("Erro ao criar bloqueio:", err));
});

// Carrega os bloqueios ao abrir a página
exibirBloqueios();



    // FUNÇÃO 4: Remover bloqueio
    async function removerBloqueio(id) {
        try {
            const response = await fetch(`/api/removerBloqueio.php?id=${id}`, { method: "DELETE" });
            if (!response.ok) throw new Error("Erro ao remover bloqueio");
            exibirBloqueios();
        } catch (error) {
            console.error(error);
        }
    }


