// -----------------------
// VARIÁVEIS GLOBAIS
// -----------------------
let agendamentos = [];
let agendaDisponivel = {}; // JSON com todos os horários disponíveis
const token = localStorage.getItem('token');

// -----------------------
// VALIDAÇÃO DE TOKEN
// -----------------------
if (!token) {
    window.location.href = "../login/index.html";
}

try {
    const payloadBase64 = token.split('.')[1];
    const payload = JSON.parse(atob(payloadBase64));
    const agora = Math.floor(Date.now() / 1000);
    if (!payload.exp || payload.exp < agora) {
        localStorage.removeItem('token');
        window.location.href = "../login/index.html";
    }
} catch (e) {
    localStorage.removeItem('token');
    window.location.href = "../login/index.html";
}

// -----------------------
// FUNÇÕES AUXILIARES
// -----------------------
function parseDataLocal(dataStr) {
    const [ano, mes, dia] = dataStr.split('-').map(Number);
    return new Date(ano, mes - 1, dia, 0, 0, 0, 0);
}

function formatarData(dataStr) {
    const data = parseDataLocal(dataStr);
    const options = { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' };
    return data.toLocaleDateString('pt-BR', options);
}

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

// -----------------------
// BUSCAR AGENDAMENTOS
// -----------------------
function buscarAgendamentos() {
    fetch("/agendamento/api/buscar/mes/index.php", {
        method: "GET",
        headers: {
            "Authorization": `Bearer ${token}`,
            "Content-Type": "application/json"
        }
    })
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

// -----------------------
// GERAR CARDS
// -----------------------
function gerarCards(busca = "", periodo = "all") {
    const container = document.getElementById("agendamentos");
    if (!container) return;

    container.innerHTML = "";

    const filtrados = agendamentos.filter(a =>
        filtrarPorPeriodo(periodo, a) &&
        (a.nome.toLowerCase().includes(busca.toLowerCase()) || a.servico.toLowerCase().includes(busca.toLowerCase()))
    );

    filtrados.forEach(a => {
        const card = document.createElement("div");
        card.className = "agendamento-card";
        card.id = "agendamento-" + a.id;

        card.innerHTML = `
            <h3>${a.nome}</h3>
            <div class="info">
                <span>💇 ${a.servico}</span>
                <span>📅 ${formatarData(a.data)}</span>
                <span>⏰ ${a.horario}</span>
                <span>📞 ${a.telefone}</span>
            </div>
            ${a.obs ? `<div class="observacoes"><strong>Observações:</strong> ${a.obs}</div>` : ""}
            <div class="actions">
                <button class="edit">Editar</button>
                <button class="cancel">Cancelar</button>
            </div>
        `;

        container.appendChild(card);

        // Eventos dos botões
        const btnEditar = card.querySelector(".edit");
        const btnCancelarCard = card.querySelector(".cancel");

        if(btnEditar) btnEditar.addEventListener("click", () => editar(a.id, a.nome, a.data, a.horario, a.telefone));
        if(btnCancelarCard) btnCancelarCard.addEventListener("click", () => btnCancelar(a.data, a.horario));
    });
}

// -----------------------
// EDIÇÃO DE AGENDAMENTO
// -----------------------
function editar(id, nome, data, hora, telefone) {
    const formEditar = document.getElementById("form-editar");
    if(!formEditar) return;

    const card = document.getElementById("agendamento-" + id);
    if(!card) return;

    // Insere o form depois do card
    card.insertAdjacentElement("afterend", formEditar);

    // Preenche campos
    const idInput = document.getElementById("id");
    const nomeInput = document.getElementById("nome");
    const dataInput = document.getElementById("data");
    const horarioSelect = document.getElementById("horario");
    const telefoneInput = document.getElementById("telefone");
    const statusSelect = document.getElementById("status");

    if(idInput) idInput.value = id;
    if(nomeInput) nomeInput.value = nome;
    if(dataInput) dataInput.value = data;
    if(telefoneInput) telefoneInput.value = telefone;

    formEditar.style.display = "block";
    formEditar.scrollIntoView({ behavior: "smooth", block: "center" });

    // Buscar horários disponíveis
    fetch('/agendamento/api/agenda/horarios.php',{
        method : "GET",
        headers: {
            "Authorization": `Bearer ${token}`,
            "Content-Type": "application/json"
        }
    }
    )
        .then(res => res.json())
        .then(dataBackend => {
            agendaDisponivel = dataBackend;

            function atualizarHorarios(dataEscolhida, horarioAtual = null) {
                if(!horarioSelect) return;
                horarioSelect.innerHTML = '<option value="">Selecione um horário</option>';

                const horariosDisponiveis = agendaDisponivel[dataEscolhida] ? [...agendaDisponivel[dataEscolhida]] : [];

                // Garante que o horário atual apareça mesmo se não estiver mais disponível
                if(horarioAtual && !horariosDisponiveis.includes(horarioAtual)) {
                    horariosDisponiveis.unshift(horarioAtual);
                }

                horariosDisponiveis.forEach(h => {
                    const option = document.createElement("option");
                    option.value = h;
                    option.textContent = h;
                    if(h === horarioAtual) option.selected = true;
                    horarioSelect.appendChild(option);
                });
            }

            // Inicializa horários do agendamento atual
            atualizarHorarios(data, hora);

            // Atualiza horários ao mudar a data
            if(dataInput) {
                dataInput.addEventListener('change', () => {
                    atualizarHorarios(dataInput.value, null); // horário antigo não deve aparecer
                });
            }

        })
        .catch(err => console.error("Erro ao carregar horários do back-end:", err));
}

// -----------------------
// CANCELAR EDIÇÃO
// -----------------------
function cancelar() {
    const formEditar = document.getElementById("form-editar");
    if(formEditar) formEditar.style.display = "none";
    limparFormulario();
}

// -----------------------
// LIMPAR FORMULÁRIO
// -----------------------
function limparFormulario() {
    ["id","nome","data","horario","telefone", "status"].forEach(id => {
        const el = document.getElementById(id);
        if(el) el.value = "";
    });
}

// -----------------------
// SALVAR ALTERAÇÕES
// -----------------------
function salvar() {
    const formData = new FormData();
    ["id","nome","data","horario","telefone", "status"].forEach(id => {
        const el = document.getElementById(id);
        if(el) formData.append(id, el.value);
    });

    fetch("/agendamento/api/atualizar/index.php", {
        method: "POST",
        body: formData,
        headers: { 'Authorization': `Bearer ${token}` }
    })
    .then(res => res.json())
    .then(res => {
        if(res.status === "success") {
            alert("Agendamento atualizado!");
            location.reload();
        } else {
            alert("Erro: " + res.message);
        }
    });
}

// -----------------------
// CANCELAR AGENDAMENTO
// -----------------------
function btnCancelar(data, horario) {
    if (!confirm("Deseja realmente cancelar este agendamento?")) return;

    const formData = new FormData();
    formData.append("data", data);
    formData.append("horario", horario);

    fetch("/agendamento/api/cancelar/index.php", {
        method: "POST",
        body: formData,
        headers: { 'Authorization': `Bearer ${token}` }
    })
    .then(res => res.json())
    .then(res => {
        if(res.status === "success") {
            alert("Agendamento cancelado!");
            buscarAgendamentos();
        } else {
            alert("Erro: " + res.message);
        }
    })
    .catch(err => console.error("Erro ao cancelar agendamento:", err));
}

// -----------------------
// EVENTOS DE BUSCA E FILTRO
// -----------------------
const searchInput = document.getElementById("search");
const periodFilter = document.getElementById("periodFilter");
if(searchInput) searchInput.addEventListener("input", (e) => gerarCards(e.target.value, periodFilter?.value || "all"));
if(periodFilter) periodFilter.addEventListener("change", (e) => gerarCards(searchInput?.value || "", e.target.value));

// -----------------------
// INICIALIZAÇÃO
// -----------------------
buscarAgendamentos();
