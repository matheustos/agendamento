// -----------------------
// VARIÁVEIS GLOBAIS
// -----------------------
let agendamentos = [];
let agendaDisponivel = {};
const token = localStorage.getItem('token');
let userAccess = null;

// -----------------------
// FUNÇÃO DE LOADING
// -----------------------
function showLoading() {
    const overlay = document.getElementById("loading-overlay");
    if (overlay) overlay.style.display = "flex";
}

function hideLoading() {
    const overlay = document.getElementById("loading-overlay");
    if (overlay) overlay.style.display = "none";
}

// -----------------------
// MENU LATERAL RESPONSIVO
// -----------------------
document.addEventListener("DOMContentLoaded", () => {
    const sideMenu = document.querySelector('.side-menu');
    const sideMenuToggle = document.getElementById('sideMenuToggle');
    const btnLogoutSide = document.getElementById('btnLogoutSide');

    if (sideMenuToggle) {
        sideMenuToggle.addEventListener('click', () => {
            sideMenu.classList.toggle('open');
        });
    }
    if (btnLogoutSide) {
        btnLogoutSide.addEventListener('click', logout);
    }
});

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
    } else {
        userAccess = payload.acesso; // "admin" ou "cliente"
    }
} catch (e) {
    localStorage.removeItem('token');
    window.location.href = "../login/index.html";
}

// Decodifica o token para pegar o acesso
function getUserAccessFromToken() {
    if (!token) return null;
    try {
        const payload = JSON.parse(atob(token.split('.')[1]));
        return payload.acesso;
    } catch (e) {
        return null;
    }
}

document.addEventListener("DOMContentLoaded", () => {
    // Esconde menus para cliente
    const acesso = getUserAccessFromToken();
    if (acesso === "cliente") {
        const menuBloquear = document.getElementById('menu-bloquear');
        const menuUsuarios = document.getElementById('menu-usuarios');
        const menuAnamnese = document.getElementById('menu-anamnese');
        const menuFicha = document.getElementById('menu-ficha');
        const menuFinanceiro = document.getElementById('menu-financeiro');
        if (menuBloquear) menuBloquear.style.display = "none";
        if (menuUsuarios) menuUsuarios.style.display = "none";
        if (menuAnamnese) menuAnamnese.style.display = "none";
        if (menuFicha) menuFicha.style.display = "none";
        if (menuFinanceiro) menuFinanceiro.style.display = "none";
    }
});

function logout() {
    localStorage.removeItem("token");
    sessionStorage.removeItem("token");
    window.location.href = "../login/index.html";
}

// -----------------------
// FUNÇÕES AUXILIARES
// -----------------------
function parseDataLocal(dataStr) {
    const [ano, mes, dia] = dataStr.split('-').map(Number);
    return new Date(ano, mes - 1, dia);
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
    showLoading();
    fetch("/agendamento/api/buscar/index.php", {
        method: "GET",
        headers: { "Authorization": `Bearer ${token}` }
    })
    .then(res => res.json())
    .then(res => {
        hideLoading();
        if(res.status) {
            agendamentos = res.data;
            gerarCards();
        } else {
            console.warn(res.message);
        }
    })
    .catch(err => {
        hideLoading();
        console.error("Erro ao buscar agendamentos:", err);
    });
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
                <span>✅ ${a.status}</span>
            </div>
            ${a.obs ? `<div class="observacoes"><strong>Observações:</strong> ${a.obs}</div>` : ""}
            <div class="actions">
                <button class="edit">Editar</button>
                <button class="cancel">Cancelar</button>
            </div>
        `;

        container.appendChild(card);

        const btnEditar = card.querySelector(".edit");
        const btnCancelarCard = card.querySelector(".cancel");

        if(btnEditar) btnEditar.addEventListener("click", () => editar(a));
        if(btnCancelarCard) btnCancelarCard.addEventListener("click", () => btnCancelar(a.data, a.horario));
    });
}

// -----------------------
// EDITAR AGENDAMENTO
// -----------------------
function editar(a) {
    const formEditar = document.getElementById("form-editar");
    if (!formEditar) return;

    formEditar.remove();
    const card = document.getElementById("agendamento-" + a.id);
    card.insertAdjacentElement("afterend", formEditar);

    formEditar.style.display = "block";

    // Preenche campos
    ["id","nome","data","horario","telefone","email","status"].forEach(key => {
        const el = document.getElementById(key);
        if(el) el.value = a[key] || "";
    });

    // Buscar horários disponíveis
    showLoading();
    fetch('/agendamento/api/agenda/horarios.php', {
        method: "GET",
        headers: { "Authorization": `Bearer ${token}` }
    })
    .then(res => res.json())
    .then(dataBackend => {
        hideLoading();
        const horarioSelect = document.getElementById("horario");
        const statusSelect = document.getElementById("status");
        if (!horarioSelect) return;

        horarioSelect.innerHTML = '<option value="">Selecione um horário</option>';
        const horariosDisponiveis = dataBackend[a.data] ? [...dataBackend[a.data]] : [];
        statusSelect.innerHTML = `
            <option value="">Selecione um status</option>
            <option>Confirmado</option>
            <option>Concluído</option>
        `;

        if (a.horario && !horariosDisponiveis.includes(a.horario)) {
            horariosDisponiveis.unshift(a.horario);
        }

        horariosDisponiveis.forEach(h => {
            const option = document.createElement("option");
            option.value = h;
            option.textContent = h;
            if (h === a.horario) option.selected = true;
            horarioSelect.appendChild(option);
        });

        // Atualiza horários ao mudar a data
        const dataInput = document.getElementById("data");
        dataInput.addEventListener("change", () => {
            horarioSelect.innerHTML = '<option value="">Selecione um horário</option>';
            const novosHorarios = dataBackend[dataInput.value] || [];
            novosHorarios.forEach(h => {
                const option = document.createElement("option");
                option.value = h;
                option.textContent = h;
                horarioSelect.appendChild(option);
            });
        });
    })
    .catch(err => {
        hideLoading();
        console.error("Erro ao carregar horários:", err);
    });
}

// -----------------------
// SALVAR ALTERAÇÕES
// -----------------------
function salvar() {
    const formData = new FormData();
    ["id","nome","data","horario","telefone","status","email"].forEach(id => {
        const el = document.getElementById(id);
        if(el) formData.append(id, el.value);
    });

    showLoading();

    fetch("/agendamento/api/atualizar/index.php", {
        method: "POST",
        body: formData,
        headers: { 'Authorization': `Bearer ${token}` }
    })
    .then(res => res.json())
    .then(res => {
        hideLoading();
        if(res.status === "success") {
            alert("Agendamento atualizado!");
            location.reload();
        } else {
            alert("Erro: " + res.message);
        }
    })
    .catch(err => {
        hideLoading();
        console.error(err);
        alert("Erro ao atualizar agendamento.");
    });
}
// -----------------------
// CANCELAR EDIÇÃO
// -----------------------
function cancelar() {
    const formEditar = document.getElementById("form-editar");
    if(formEditar) formEditar.style.display = "none";
}

function btnCancelar(data, horario) {
    if (!confirm("Deseja realmente cancelar este agendamento?")) return;

    const formData = new FormData();
    formData.append("data", data);
    formData.append("horario", horario);

    showLoading();

    fetch("/agendamento/api/cancelar/index.php", {
        method: "POST",
        body: formData,
        headers: { 'Authorization': `Bearer ${token}` }
    })
    .then(res => res.json())
    .then(res => {
        hideLoading();
        if(res.status === "success") {
            alert("Agendamento cancelado!");
            buscarAgendamentos();
        } else {
            alert("Erro: " + res.message);
        }
        window.location.reload();
    })
    .catch(err => {
        hideLoading();
        console.error("Erro ao cancelar agendamento:", err);
        alert("Erro ao cancelar agendamento.");
    });
}

// -----------------------
// BUSCA E FILTRO
// -----------------------
const searchInput = document.getElementById("search");
const periodFilter = document.getElementById("periodFilter");
if(searchInput) searchInput.addEventListener("input", (e) => gerarCards(e.target.value, periodFilter?.value || "all"));
if(periodFilter) periodFilter.addEventListener("change", (e) => gerarCards(searchInput?.value || "", e.target.value));

// -----------------------
// INICIALIZAÇÃO
// -----------------------

buscarAgendamentos();
