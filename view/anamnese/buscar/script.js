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

// ========================
// TOKEN E VALIDAÇÃO
// ========================
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

// ========================
// LABELS
// ========================
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

// ========================
// FUNÇÃO CRIAR CARD
// ========================
function criarCard(data) {
    const card = document.createElement("div");
    card.className = "card";

    const fieldsDiv = document.createElement("div");
    fieldsDiv.className = "fields";

    const originalData = { ...data };

    const ordemCampos = [
        "id","nome","data_nascimento","telefone","email","sexo","profissao","endereco",
        "condicoes","alergias","medicamentos","cirurgias","marcapasso","gestante",
        "queixa","podologico","calcados","higiene","exame","conduta","obs",
        "data","profissional"
    ];

    ordemCampos.forEach((key) => {
        if (!(key in data)) return;

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

    // BOTÕES
    const editButton = document.createElement("button");
    editButton.textContent = "Editar";
    editButton.className = "save-btn";

    const cancelButton = document.createElement("button");
    cancelButton.textContent = "Cancelar";
    cancelButton.className = "cancel-btn";
    cancelButton.style.display = "none";

    const printButton = document.createElement("button");
    printButton.textContent = "Imprimir Ficha";
    printButton.className = "save-btn";
    printButton.style.background = "#28a745";
    printButton.addEventListener("click", () => {
        imprimirFicha(card);
    });

    buttonsDiv.appendChild(editButton);
    buttonsDiv.appendChild(cancelButton);
    buttonsDiv.appendChild(printButton);
    card.appendChild(buttonsDiv);

    // ========================
    // EDITAR / SALVAR
    // ========================
    editButton.addEventListener("click", async () => {
        if (editButton.textContent === "Editar") {
            card.classList.add("editando");
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
            card.classList.remove("editando");
            editButton.textContent = "Editar";
            cancelButton.style.display = "none";
        }
    });

    cancelButton.addEventListener("click", () => {
        card.classList.remove("editando");
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

// ========================
// FUNÇÃO IMPRIMIR FICHA
// ========================
function imprimirFicha(cardElement) {
    const printWindow = window.open('', '', 'width=800,height=600');
    const fieldsContent = Array.from(cardElement.querySelectorAll(".field")).map(f => {
        const label = f.querySelector("label").textContent;
        const value = f.querySelector("p") ? f.querySelector("p").textContent : f.querySelector("input").value;
        return `<div class="ficha-field"><label>${label}</label><span>${value}</span></div>`;
    }).join("");

    const style = `
        <style>
            body { font-family: Arial, sans-serif; margin: 20px; }
            .ficha { display: flex; flex-direction: column; gap: 20px; max-width: 900px; margin: 0 auto; }
            .ficha-field { display: flex; flex-direction: column; flex: 1 1 45%; min-width: 200px; margin-bottom:10px; }
            .ficha-field label { font-weight: bold; margin-bottom: 5px; font-size: 16px; }
            .ficha-field span { padding: 6px 8px; border: 1px solid #ddd; border-radius: 5px; background-color: #f9f9f9; }
            h1 { text-align: center; }
        </style>
    `;

    printWindow.document.write(`
        <html>
            <head><title>Ficha de Anamnese</title>${style}</head>
            <body>
                <h1>Ficha de Anamnese</h1>
                <div class="ficha">${fieldsContent}</div>
            </body>
        </html>
    `);

    printWindow.document.close();
    printWindow.focus();
    printWindow.print();
    printWindow.close();
}

// ========================
// BUSCAR DADOS DA API
// ========================
async function buscarDados() {
    try {
        const response = await fetch("/agendamento/api/anamnese/buscar/index.php", {
            headers: { "Authorization": `Bearer ${token}` }
        });
        const json = await response.json();
        allData = json.data;
        exibirCards(allData);
    } catch (error) {
        console.error("Erro ao buscar dados:", error);
    }
}

// ========================
// EXIBIR CARDS
// ========================
function exibirCards(dataArray) {
    const container = document.getElementById("cards-container");
    container.innerHTML = "";
    dataArray.forEach(item => container.appendChild(criarCard(item)));
}

// ========================
// PESQUISA POR NOME
// ========================
const searchInput = document.getElementById("search");
searchInput.addEventListener("input", () => {
    const filtro = searchInput.value.toLowerCase();
    const filtrados = allData.filter(item => (item.nome ?? "").toLowerCase().includes(filtro));
    exibirCards(filtrados);
});

// ========================
// INICIALIZA
// ========================
buscarDados();
