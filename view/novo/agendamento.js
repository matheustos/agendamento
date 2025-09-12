/*const token = localStorage.getItem('token');
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

const form = document.getElementById("agenda");

form.addEventListener("submit", async function(event) {
  event.preventDefault();

  const token = localStorage.getItem('token');
  const dados = new FormData(form);

  try {
    const res = await fetch("/agendamento/api/novo/index.php", {
      method: "POST",
      body: dados,
      headers: {
        'Authorization': `Bearer ${token}` // o token vai no header
      }
    });

    const json = await res.json();

    // Alerta nativo do navegador
    alert(json.message);
    location.reload();

  } catch (err) {
    alert("Erro: " + err.message);
  }
});*/

// -----------------------
// VALIDAÇÃO DO TOKEN
// -----------------------
const token = localStorage.getItem('token');
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
    // endpoint que retorna próximos 30 dias
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

    // ===== FILTRO AQUI =====
    // Pega somente os horários disponíveis da data selecionada
    const horarios = agendaDisponivel[dataEscolhida] || [];
    // =======================

    // Adiciona os horários no select
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
// ENVIO DO FORMULÁRIO DE NOVO AGENDAMENTO
// -----------------------
form.addEventListener("submit", async function(event) {
    event.preventDefault();

    const token = localStorage.getItem('token');
    const dados = new FormData(form);

    try {
        const res = await fetch("/agendamento/api/novo/index.php", {
            method: "POST",
            body: dados,
            headers: {
                'Authorization': `Bearer ${token}`
            }
        });

        const json = await res.json();

        alert(json.message);
        location.reload();

    } catch (err) {
        alert("Erro: " + err.message);
    }
});

