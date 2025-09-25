// -----------------------
// VALIDAÇÃO DO TOKEN
// -----------------------
const token = localStorage.getItem('token');

if (!token) {
    window.location.href = "../login/index.html";
}

// -----------------------
// FUNÇÃO LOGOUT
// -----------------------
function logout() {
    localStorage.removeItem('token');
    sessionStorage.removeItem('token');
    window.location.href = "../login/index.html";
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

    // Esconde menus para clientes
    const acesso = getUserAccessFromToken();
    if (acesso === "cliente") {
        const menuBloquear = document.getElementById('menu-bloquear');
        const menuUsuarios = document.getElementById('menu-usuarios');
        const menuAnamnese = document.getElementById('menu-anamnese');
        const menuFicha = document.getElementById('menu-ficha');
        const menuProdutos = document.getElementById('menu-produtos');
        const menuFinanceiro = document.getElementById('menu-financeiro');
        if (menuBloquear) menuBloquear.style.display = "none";
        if (menuUsuarios) menuUsuarios.style.display = "none";
        if (menuProdutos) menuProdutos.style.display = "none";
        if (menuAnamnese) menuAnamnese.style.display = "none";
        if (menuFicha) menuFicha.style.display = "none";
        if (menuFinanceiro) menuFinanceiro.style.display = "none";
    }
});

// -----------------------
// DECODIFICAR TOKEN
// -----------------------
function getUserAccessFromToken() {
    try {
        const payload = JSON.parse(atob(token.split('.')[1]));
        return payload.acesso;
    } catch (e) {
        return null;
    }
}

// -----------------------
// VARIÁVEIS GLOBAIS
// -----------------------
let agendaDisponivel = {}; // JSON com todos os horários disponíveis
const inputData = document.getElementById('dataAgendamento');
const selectHorario = document.getElementById('horariosSelect');
const form = document.getElementById("agenda");

// -----------------------
// CARREGAR HORÁRIOS DO BACK-END
// -----------------------
fetch('/agendamento/api/agenda/horarios.php', {
    method: "GET",
    headers: {
        "Authorization": `Bearer ${token}`,
        "Content-Type": "application/json"
    }
})
.then(res => res.json())
.then(data => {
    agendaDisponivel = data;
    habilitarDatasDisponiveis();
})
.catch(err => console.error('Erro ao carregar agenda:', err));

// -----------------------
// HABILITAR DATAS VÁLIDAS
// -----------------------
function habilitarDatasDisponiveis() {
    const datas = Object.keys(agendaDisponivel);
    if (!datas.length) return;

    inputData.min = datas[0];
    inputData.max = datas[datas.length - 1];

    // Sugere a primeira data disponível
    inputData.value = datas[0];
    inputData.dispatchEvent(new Event('change'));
}

// -----------------------
// FILTRAR HORÁRIOS NO SELECT
// -----------------------
inputData.addEventListener('change', () => {
    const dataEscolhida = inputData.value;
    selectHorario.innerHTML = '<option value="">Selecione um horário</option>';
    const horarios = agendaDisponivel[dataEscolhida] || [];
    horarios.forEach(hora => {
        const option = document.createElement('option');
        option.value = hora;
        option.textContent = hora;
        selectHorario.appendChild(option);
    });
});

// -----------------------
// ENVIO DO FORMULÁRIO COM LOADING
// -----------------------
form.addEventListener("submit", async function(event) {
    event.preventDefault();

    const dados = new FormData(form);

    // Mostrar loading
    const loading = document.getElementById("loading-overlay");
    if (loading) loading.style.display = "flex";

    try {
        const res = await fetch("/agendamento/api/novo/index.php", {
            method: "POST",
            body: dados,
            headers: { 'Authorization': `Bearer ${token}` }
        });

        const json = await res.json();
        alert(json.message || "Agendamento realizado com sucesso!");
        location.reload();

    } catch (err) {
        alert("Erro: " + err.message);
    } finally {
        if (loading) loading.style.display = "none";
    }
});
