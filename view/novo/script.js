// -----------------------
// VALIDAÇÃO DO TOKEN
// -----------------------
const token = localStorage.getItem('token');

// Menu lateral responsivo
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


// Decodifique o token para pegar o acesso
function getUserAccessFromToken() {
    if (!token) {
    window.location.href = "../login/index.html";
}else{
    try {
        const payload = JSON.parse(atob(token.split('.')[1]));
        return payload.acesso;
    } catch (e) {
        return null;
    }
}
}

document.addEventListener("DOMContentLoaded", () => {
    // ...código do menu lateral...

    // Esconde menus para cliente
    const acesso = getUserAccessFromToken();
    if (acesso === "cliente") {;
        const menuBloquear = document.getElementById('menu-bloquear');
        const menuUsuarios = document.getElementById('menu-usuarios');
        if (menuBloquear) menuBloquear.style.display = "none";
        if (menuUsuarios) menuUsuarios.style.display = "none";
    }
});

if (!token) {
    window.location.href = "../login/index.html";
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
// FILTRAR HORÁRIOS NO SELECT
// -----------------------
inputData.addEventListener('change', () => {
    const dataEscolhida = inputData.value;

    // Limpa o select antes de preencher
    selectHorario.innerHTML = '<option value="">Selecione um horário</option>';

    // Pega somente os horários disponíveis da data selecionada
    const horarios = agendaDisponivel[dataEscolhida] || [];

    // Adiciona os horários disponíveis
    horarios.forEach(hora => {
        const option = document.createElement('option');
        option.value = hora;
        option.textContent = hora;
        selectHorario.appendChild(option);
    });
});

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
// ENVIO DO FORMULÁRIO COM TELA DE "CARREGANDO"
// -----------------------
form.addEventListener("submit", async function(event) {
    event.preventDefault();

    const token = localStorage.getItem('token');
    const dados = new FormData(form);

    // =======================
    // TELA DE CARREGANDO
    // =======================
    const loadingOverlay = document.createElement("div");
    loadingOverlay.id = "loadingOverlay";
    loadingOverlay.style = `
        position: fixed;
        top: 0; left: 0;
        width: 100%; height: 100%;
        background: rgba(0,0,0,0.5);
        color: white;
        font-size: 24px;
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 9999;
    `;
    loadingOverlay.textContent = "⏳ Processando, aguarde...";
    document.body.appendChild(loadingOverlay);

    try {
        const res = await fetch("/agendamento/api/novo/index.php", {
            method: "POST",
            body: dados,
            headers: {
                'Authorization': `Bearer ${token}`
            }
        });

        const json = await res.json();

        // =======================
        // REMOVER TELA DE CARREGANDO
        // =======================
        loadingOverlay.remove();

        alert(json.message);
        location.reload();

    } catch (err) {
        loadingOverlay.remove();
        alert("Erro: " + err.message);
    }
});

document.getElementById('btnLogoutSide').addEventListener('click', function() {
    // Remove o token JWT do localStorage
    localStorage.removeItem('token'); // ou sessionStorage.removeItem('token');

    // Redireciona para a página de login
    window.location.href = '../login/index.html';
});
