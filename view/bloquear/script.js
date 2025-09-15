/*const form = document.getElementById("agenda");
const bloqDiv = document.getElementById("bloq");
const token = localStorage.getItem('token');

if(!token){
    window.location.href = "../login/index.html";
}

try {
    // Decodifica o payload do JWT (parte do meio)
    const payloadBase64 = token.split('.')[1];
    const payload = JSON.parse(atob(payloadBase64));

    // Verifica expiração
    const agora = Math.floor(Date.now() / 1000); // em segundos
    if (!payload.exp || payload.exp < agora) {
        // Token expirado ou sem exp -> limpa e redireciona
        localStorage.removeItem('token');
        window.location.href = "../login/index.html";
    }
} catch (e) {
    // Se der erro na decodificação -> token inválido
    localStorage.removeItem('token');
    window.location.href = "../login/index.html";
}
// Função para exibir bloqueios na tela
function exibirBloqueios() {
    fetch("/agendamento/api/bloquear/buscar/index.php", {
            method: 'GET',
            headers: {
                'Authorization': `Bearer ${token}`
            }
        })
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
    const token = localStorage.getItem('token');

    const formData = new FormData(form);

    fetch("/agendamento/api/bloquear/index.php", {
        method: "POST",
        body: formData,
        headers: {
            'Authorization': `Bearer ${token}` // o token vai no header
        }
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
exibirBloqueios();*/
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

const form = document.getElementById("agenda");
const bloqDiv = document.getElementById("bloq");
const dataInput = document.getElementById("data");
const selectHorario = document.getElementById("horario");
const token = localStorage.getItem('token');

let disponibilidade = {}; // guarda os horários vindos do back-end

// ---------------------
// AUTENTICAÇÃO
// ---------------------
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
    }else{
        userAccess = payload.acesso;  // "admin" ou "cliente"
    }
} catch (e) {
    localStorage.removeItem('token');
    window.location.href = "../login/index.html";
}

if(userAccess != "admin") {
    window.location.href = "../agenda/index.html"; //redirecionar para a agenda pois não tem permissão para bloquear agenda
}

// ---------------------
// BUSCA DISPONIBILIDADE
// ---------------------
function carregarDisponibilidade() {
    fetch("/agendamento/api/agenda/horarios.php", {
        method: "GET",
        headers: { "Authorization": `Bearer ${token}` }
    })
    .then(res => res.json())
    .then(json => {
        disponibilidade = json;
    })
    .catch(err => console.error("Erro ao buscar disponibilidade:", err));
}

// ---------------------
// ATUALIZA SELECT QUANDO DATA MUDA
// ---------------------
dataInput.addEventListener("change", () => {
    const dataEscolhida = dataInput.value;
    atualizarHorarios(dataEscolhida);
});

function atualizarHorarios(dataEscolhida) {
    selectHorario.innerHTML = "";

    const horarios = disponibilidade[dataEscolhida] || [];

    if (horarios.length === 0) {
        const option = document.createElement("option");
        option.value = "";
        option.textContent = "Nenhum horário disponível";
        selectHorario.appendChild(option);
        return;
    }

    horarios.forEach(h => {
        const option = document.createElement("option");
        option.value = h;
        option.textContent = h;
        selectHorario.appendChild(option);
    });
}

// ---------------------
// BLOQUEIOS
// ---------------------
function exibirBloqueios() {
    fetch("/agendamento/api/bloquear/buscar/index.php", {
        method: "GET",
        headers: { "Authorization": `Bearer ${token}` }
    })
    .then(res => res.json())
    .then(bloqueios => {
        bloqDiv.innerHTML = `
            <h2><span>🕓</span>Períodos Bloqueados</h2>
            <p>Lista de todos os bloqueios ativos na agenda do mês</p>
        `;

        bloqueios.forEach(b => {
            const card = document.createElement("div");
            card.classList.add("card-bloqueio");
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

// ---------------------
// SUBMIT FORM
// ---------------------
form.addEventListener("submit", (e) => {
    e.preventDefault();

    const formData = new FormData(form);

    fetch("/agendamento/api/bloquear/index.php", {
        method: "POST",
        body: formData,
        headers: { "Authorization": `Bearer ${token}` }
    })
    .then(res => res.json())
    .then(res => {
        form.reset();

        if (res.status === true) {
            alert(res.message);
            exibirBloqueios();
        } else {
            alert("Erro: " + res.message);
        }
        window.location.reload();
    })
    .catch(err => console.error("Erro ao criar bloqueio:", err));
});

document.getElementById('btnLogoutSide').addEventListener('click', function() {
    // Remove o token JWT do localStorage
    localStorage.removeItem('token'); // ou sessionStorage.removeItem('token');

    // Redireciona para a página de login
    window.location.href = '../login/index.html';
});

// ---------------------
// INICIALIZAÇÃO
// ---------------------
carregarDisponibilidade();
exibirBloqueios();
