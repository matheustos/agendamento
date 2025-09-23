document.addEventListener("DOMContentLoaded", () => {
    const sideMenu = document.querySelector('.side-menu');
    const sideMenuToggle = document.getElementById('sideMenuToggle');

    if (sideMenuToggle) {
        sideMenuToggle.addEventListener('click', () => {
            sideMenu.classList.toggle('open');
        });
    }
    if (btnLogoutSide) {
        btnLogoutSide.addEventListener('click', logout);
    }
});

const token = localStorage.getItem('token');

if (!token) {
    window.location.href = "../../login/index.html";
}

let userAccess = null;

try {
    const payloadBase64 = token.split('.')[1];
    const payload = JSON.parse(atob(payloadBase64));
    const agora = Math.floor(Date.now() / 1000);

    if (!payload.exp || payload.exp < agora) {
        localStorage.removeItem('token');
        window.location.href = "../../login/index.html";
    } else {
        userAccess = payload.acesso;
    }
} catch (e) {
    localStorage.removeItem('token');
    window.location.href = "../../login/index.html";
}

if(userAccess === "cliente"){
    window.location.href = "../../agenda/index.html"
}

function logout() {
    localStorage.removeItem("token");
    sessionStorage.removeItem("token");
    window.location.href = "../../login/index.html";
}

const labels = {
    id: "ID do cliente",
    nome: "Nome completo",
    email: "E-mail",
    telefone: "Telefone",
    endereco: "Endereço",
    data_nascimento: "Data de Nascimento",
    sexo: "Sexo",
    profissao: "Profissão",
    condicoes: "Histórico de Saúde",
    alergias: "Alergias",
    medicamentos: "Medicamentos",
    cirurgias: "Cirurgias",
    marcapasso: "Marcapasso",
    gestante: "Gestante",
    queixa: "Queixa",
    podologico: "Histórico Podológico",
    calcados: "Calçados",
    higiene: "Higiene",
    exame: "Exame",
    conduta: "Conduta",
    obs: "Observações",
    data: "Data de Preenchimento",
    profissional: "Assinatura do Profissional"  
};

let allData = [];

function criarCard(data) {
    const card = document.createElement("div");
    card.className = "card";

    const fieldsDiv = document.createElement("div");
    fieldsDiv.className = "fields";

    const originalData = { ...data };

    // Ordem personalizada dos campos
    const ordemCampos = [
        "id","nome","data_nascimento","telefone","email","sexo","profissao","endereco",
        "condicoes","alergias","medicamentos","cirurgias","marcapasso","gestante",
        "queixa","podologico","calcados","higiene","exame","conduta","obs",
        "data",        // <- movi "data" antes de "profissional"
        "profissional" // <- agora "profissional" vem depois
    ];

    ordemCampos.forEach((key) => {
        if (!(key in data)) return; // ignora campos que não existem

        const field = document.createElement("div");
        field.className = "field";

        const label = document.createElement("label");
        label.textContent = labels[key] || key;

        const value = document.createElement("p");
        value.textContent = data[key] ?? "";
        value.dataset.key = key;

        field.appendChild(label);
        field.appendChild(value);
        fieldsDiv.appendChild(field);
    });

    card.appendChild(fieldsDiv);

    const buttonsDiv = document.createElement("div");
    buttonsDiv.className = "buttons";

    const editButton = document.createElement("button");
    editButton.textContent = "Editar";
    editButton.className = "save-btn";

    const cancelButton = document.createElement("button");
    cancelButton.textContent = "Cancelar";
    cancelButton.className = "cancel-btn";
    cancelButton.style.display = "none";

    buttonsDiv.appendChild(editButton);
    buttonsDiv.appendChild(cancelButton);
    card.appendChild(buttonsDiv);

    // Evento Editar / Salvar
    editButton.addEventListener("click", async () => {
        if (editButton.textContent === "Editar") {
            card.classList.add("editando"); // adiciona destaque visual
            card.querySelectorAll("p").forEach((p) => {
                if (p.dataset.key === "id") return;
                if (p.dataset.key === "data") return;

                const input = document.createElement("input");
                input.dataset.key = p.dataset.key;
                input.name = p.dataset.key;

                if (p.dataset.key === "data_nascimento" || p.dataset.key === "data") {
                    input.type = "date";
                    const partes = p.textContent.split("/");
                    if (partes.length === 3) {
                        input.value = `${partes[2]}-${partes[1].padStart(2,"0")}-${partes[0].padStart(2,"0")}`;
                    } else {
                        input.value = p.textContent;
                    }
                } else {
                    input.type = "text";
                    input.value = p.textContent;
                }

                p.replaceWith(input);
            });
            editButton.textContent = "Salvar";
            cancelButton.style.display = "inline-block";
        } else {
            // Aqui você mantém seu fetch para salvar os dados
            // Após salvar com sucesso:
            card.classList.remove("editando"); // remove destaque visual
            editButton.textContent = "Editar";
            cancelButton.style.display = "none";
        }
    });

    cancelButton.addEventListener("click", () => {
        card.classList.remove("editando"); // remove destaque visual
        card.querySelectorAll("input").forEach((input) => {
            const p = document.createElement("p");
            p.textContent = originalData[input.dataset.key] ?? "";
            p.dataset.key = input.dataset.key;
            input.replaceWith(p);
        });
        editButton.textContent = "Editar";
        cancelButton.style.display = "none";
        Object.assign(data, originalData);
    });

    return card;
}

async function buscarDados() {
    try {
        const response = await fetch("/agendamento/api/anamnese/buscar/index.php", {
            headers: {
                "Authorization": `Bearer ${token}`
            }
        });
        const json = await response.json();
        allData = json.data;
        exibirCards(allData);
    } catch (error) {
        console.error("Erro ao buscar dados:", error);
    }
}

function exibirCards(dataArray) {
    const container = document.getElementById("cards-container");
    container.innerHTML = "";
    dataArray.forEach(item => container.appendChild(criarCard(item)));
}

// Pesquisa por nome
const searchInput = document.getElementById("search");
searchInput.addEventListener("input", () => {
    const filtro = searchInput.value.toLowerCase();
    const filtrados = allData.filter(item => (item.nome ?? "").toLowerCase().includes(filtro));
    exibirCards(filtrados);
});

buscarDados();
