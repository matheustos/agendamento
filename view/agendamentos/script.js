let agendamentos = [];

// Função para criar Date local a partir de "YYYY-MM-DD"
function parseDataLocal(dataStr) {
    const [ano, mes, dia] = dataStr.split('-').map(Number);
    return new Date(ano, mes - 1, dia, 0, 0, 0, 0); // hora local
}

// Função para buscar agendamentos do servidor
function buscarAgendamentos() {
    fetch("/agendamento/api/buscar/mes/index.php")
        .then(res => res.json())
        .then(res => {
            if(res.status) {
                agendamentos = res.data;
                gerarCards();
            } else {
                console.warn(res.message);
            }
        })
        .catch(err => console.error("Erro ao buscar agendamentos:", err));
}

// Função para formatar data
function formatarData(dataStr) {
    const data = parseDataLocal(dataStr);
    const options = { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' };
    return data.toLocaleDateString('pt-BR', options);
}

// Filtrar por período
function filtrarPorPeriodo(periodo, agendamento) {
    const hoje = new Date();
    const dataAgendamento = parseDataLocal(agendamento.data);

    switch(periodo) {
        case "day":
            return dataAgendamento.toDateString() === hoje.toDateString();
        case "week":
            const primeiroDiaSemana = new Date(hoje);
            primeiroDiaSemana.setDate(hoje.getDate() - hoje.getDay());
            const ultimoDiaSemana = new Date(primeiroDiaSemana);
            ultimoDiaSemana.setDate(primeiroDiaSemana.getDate() + 6);
            return dataAgendamento >= primeiroDiaSemana && dataAgendamento <= ultimoDiaSemana;
        case "month":
            return dataAgendamento.getMonth() === hoje.getMonth() &&
                    dataAgendamento.getFullYear() === hoje.getFullYear();
        default:
            return true;
    }
}

// Gerar cards
function gerarCards(busca = "", periodo = "all") {
    const container = document.getElementById("agendamentos");
    container.innerHTML = "";

    const filtrados = agendamentos.filter(a =>
        filtrarPorPeriodo(periodo, a) &&
        (a.nome.toLowerCase().includes(busca.toLowerCase()) || a.servico.toLowerCase().includes(busca.toLowerCase()))
    );

    filtrados.forEach(a => {
        const card = document.createElement("div");
        card.className = "agendamento-card";

        card.innerHTML = `
            <h3>${a.nome}</h3>
            <div class="info">
                <span>💇 ${a.servico}</span>
                <span>📅 ${formatarData(a.data)}</span>
                <span>⏰ ${a.horario}</span>
            </div>
            ${a.obs ? `<div class="observacoes"><strong>Observações:</strong> ${a.obs}</div>` : ""}
            <div class="actions">
                <button class="edit" onclick="editar(${a.id}, '${a.nome}', '${a.data}', '${a.horario}', '${a.obs || ''}')">Editar</button>
                <button class="cancel" onclick="btnCancelar('${a.data}', '${a.horario}')">Cancelar</button>
            </div>
        `;

        container.appendChild(card);
    });
}

// Funções de editar e cancelar
function editar(id, nome, data, hora, obs) {
    document.getElementById("form-editar").style.display = "block";
    document.getElementById("id").value = id;
    document.getElementById("nome").value = nome;
    document.getElementById("data").value = data;
    document.getElementById("horario").value = hora;
    document.getElementById("obs").value = obs || ""; // obs pode ser vazio
}

function cancelar() {
    document.getElementById("form-editar").style.display = "none";
    limparFormulario();
}

function limparFormulario() {
    document.getElementById("id").value = "";
    document.getElementById("nome").value = "";
    document.getElementById("data").value = "";
    document.getElementById("horario").value = "";
    document.getElementById("obs").value = "";
}

function salvar() {
    const formData = new FormData();
    formData.append("id", document.getElementById("id").value);
    formData.append("nome", document.getElementById("nome").value);
    formData.append("data", document.getElementById("data").value);
    formData.append("horario", document.getElementById("horario").value);
    formData.append("obs", document.getElementById("obs").value);

    fetch("/agendamento/api/atualizar/index.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.json())
    .then(res => {
        if(res.status === "success") {
            alert("Agendamento atualizado!");
            cancelar();
            buscarAgendamentos();
        } else {
            alert("Erro: " + res.message);
        }
    });
}

function btnCancelar(data, horario) {
    if (!confirm("Deseja realmente cancelar este agendamento?")) return;

    const formData = new FormData();
    formData.append("data", data);
    formData.append("horario", horario);

    fetch("/agendamento/api/cancelar/index.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.json())
    .then(res => {
        if (res.status === "success") {
            alert("Agendamento cancelado!");
            buscarAgendamentos(); // atualiza a lista
        } else {
            alert("Erro: " + res.message);
            console.log("Resposta do servidor:", res);
        }
    })
    .catch(err => console.error("Erro ao cancelar agendamento:", err));
}


// Eventos de busca e filtro
document.getElementById("search").addEventListener("input", (e) => {
    gerarCards(e.target.value, document.getElementById("periodFilter").value);
});

document.getElementById("periodFilter").addEventListener("change", (e) => {
    gerarCards(document.getElementById("search").value, e.target.value);
});

// Inicialização
buscarAgendamentos();