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
});
  
const token = localStorage.getItem('token');

const labels = {
    id: "ID do cliente",
    nome: "Nome completo",
    email: "E-mail",
    telefone: "Telefone",
    endereco: "Endereço",
    data_nascimento: "Data de Nascimento",
    sexo: "Sexo",
    profissao: "Profissão",
    condicoes: "Condições",
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
    profissional: "Assinatura do Profissional",
    data: "Data"
};

let allData = []; // para manter todos os registros da API

function criarCard(data) {
const card = document.createElement("div");
card.className = "card";

const fieldsDiv = document.createElement("div");
fieldsDiv.className = "fields";

const originalData = { ...data };

Object.keys(data).forEach((key) => {
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

editButton.addEventListener("click", () => {
    if (editButton.textContent === "Editar") {
    card.querySelectorAll("p").forEach((p) => {
        const input = document.createElement("input");
        input.value = p.textContent;
        input.dataset.key = p.dataset.key;
        p.replaceWith(input);
    });
    editButton.textContent = "Salvar";
    cancelButton.style.display = "inline-block";
    } else {
    card.querySelectorAll("input").forEach((input) => {
        const p = document.createElement("p");
        p.textContent = input.value;
        p.dataset.key = input.dataset.key;
        input.replaceWith(p);
        data[input.dataset.key] = input.value;
    });
    console.log("Dados atualizados:", data);
    editButton.textContent = "Editar";
    cancelButton.style.display = "none";
    }
});

cancelButton.addEventListener("click", () => {
    card.querySelectorAll("input").forEach((input) => {
    const p = document.createElement("p");
    p.textContent = originalData[input.dataset.key] ?? "";
    p.dataset.key = input.dataset.key;
    input.replaceWith(p);
    });
    editButton.textContent = "Editar";
    cancelButton.style.display = "none";
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
    allData = json.data; // salva todos os registros

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
