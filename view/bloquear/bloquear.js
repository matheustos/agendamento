const form = document.getElementById("agenda");
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
exibirBloqueios();


