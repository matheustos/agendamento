const token = localStorage.getItem('token');

if(!token){
    window.location.href= "../login/index.html";
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

function logout() {
    localStorage.removeItem("token");
    sessionStorage.removeItem("token");
    window.location.href = "../login/index.html";
}

let userAccess = null;

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

const acesso = getUserAccessFromToken();

document.addEventListener("DOMContentLoaded", () => {
    if (acesso === "cliente") {
        window.location.href="../agenda/index.html";
    }
});

// Função para formatar valores em moeda brasileira
function formatarMoeda(valor) {
    return valor.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
}

function atualizarDashboard(valor) {
    const span = document.getElementById('receita-filtrada');
    if(span) {
        span.textContent = formatarMoeda(Number(valor));
    }
}


document.addEventListener("DOMContentLoaded", () => {
  const filtroTipo = document.getElementById("filtroTipo");
  const filtroDia = document.getElementById("filtroDia");
  const filtroMes = document.getElementById("filtroMes");
  const filtroAno = document.getElementById("filtroAno");
  const btnFiltrar = document.getElementById("btnFiltrar");

  // Sempre esconde todos os inputs primeiro
  function esconderInputs() {
    filtroDia.style.display = "none";
    filtroMes.style.display = "none";
    filtroAno.style.display = "none";
  }

  // Mostrar o input certo de acordo com a seleção
  filtroTipo.addEventListener("change", () => {
    esconderInputs();
    if (filtroTipo.value === "dia") filtroDia.style.display = "block";
    if (filtroTipo.value === "mes") filtroMes.style.display = "block";
    if (filtroTipo.value === "ano") filtroAno.style.display = "block";
  });

  // Quando clicar em "Pesquisar"
  btnFiltrar.addEventListener("click", () => {
    let tipo = "";
    let valor = "";

    if (filtroTipo.value === "dia" && filtroDia.value) {
        tipo = "dia";
        valor = filtroDia.value;
    } else if (filtroTipo.value === "mes" && filtroMes.value) {
        tipo = "mes";
        const [ano, mes] = filtroMes.value.split("-");
        // aqui enviamos separados
        valor = { ano: parseInt(ano), mes: parseInt(mes) };
    } else if (filtroTipo.value === "ano" && filtroAno.value) {
        tipo = "ano";
        valor = filtroAno.value;
    } else {
        alert("Selecione um valor válido.");
        return;
    }

    const formData = new FormData();
    formData.append("tipo", tipo);

    if (tipo === "mes") {
        formData.append("ano", valor.ano);
        formData.append("mes", valor.mes);
    } else {
        formData.append("valor", valor);
    }

    fetch("/agendamento/api/financeiro/filtro/index.php", {
        method: "POST",
        headers: { "Authorization": `Bearer ${token}` },
        body: formData
    })
    .then(res => res.text())
    .then(data => {
        console.log("Resposta:", data);
        const valorNum = Number(data);
        atualizarDashboard(valorNum);
    })
    .catch(err => console.error("Erro:", err));
});

});


// Função para atualizar receitas
async function atualizarReceitas() {
    try {
        const resposta = await fetch('/agendamento/api/financeiro/index.php', {
            method: "GET", 
            headers: {"Authorization": `Bearer ${token}`}
    });
        const dados = await resposta.json();

        document.getElementById('receita-hoje').textContent   = formatarMoeda(dados.receitaHoje);
        document.getElementById('receita-semana').textContent = formatarMoeda(dados.receitaSemana);
        document.getElementById('receita-mes').textContent    = formatarMoeda(dados.receitaMes);
        document.getElementById('receita-ano').textContent    = formatarMoeda(dados.receitaAno);

    } catch (error) {
        console.error("Erro ao atualizar receitas:", error);
    }
}

// Função para atualizar status de serviços
async function atualizarStatus() {
    try {
        const resposta = await fetch('/agendamento/api/status/index.php',{
            method: "GET",
            headers: {"Authorization": `Bearer ${token}`}
        });
        const dados = await resposta.json();

        document.getElementById('concluidos').textContent = dados.concluidos;
        document.getElementById('confirmados').textContent = dados.confirmados;
        document.getElementById('agendados').textContent = dados.agendados;

    } catch (error) {
        console.error("Erro ao atualizar status:", error);
    }
}

// Atualiza ao carregar a página
atualizarReceitas();
atualizarStatus();

// Atualiza a cada 60 segundos
setInterval(atualizarReceitas, 60000);
setInterval(atualizarStatus, 60000);
