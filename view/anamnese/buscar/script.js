// Menu lateral responsivo
document.addEventListener("DOMContentLoaded", () => {
    const sideMenu = document.querySelector('.side-menu');
    const sideMenuToggle = document.getElementById('sideMenuToggle');

    if (sideMenuToggle) {
        sideMenuToggle.addEventListener('click', () => {
            sideMenu.classList.toggle('open');
        });
    }
});

const token = localStorage.getItem('token');

if (!token) {
    window.location.href = "../../login/index.html";
}

try {
    const payloadBase64 = token.split('.')[1];
    const payload = JSON.parse(atob(payloadBase64));
    const agora = Math.floor(Date.now() / 1000);

    if (!payload.exp || payload.exp < agora) {
        localStorage.removeItem('token');
        window.location.href = "../../login/index.html";
    } else {
        userAccess = payload.acesso; // "admin" ou "cliente"
    }
} catch (e) {
    localStorage.removeItem('token');
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

    editButton.addEventListener("click", async () => {
        if (editButton.textContent === "Editar") {
            // Ativar modo edição
            card.querySelectorAll("p").forEach((p) => {
                // não permitir edição
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
            // Salvar alterações
            const inputs = card.querySelectorAll("input");
            const dadosParaEnviar = {};
            inputs.forEach(input => {
                dadosParaEnviar[input.name] = input.value ?? "";
            });

            // Garantir que o id sempre vá junto (mesmo não editável)
            dadosParaEnviar.id = data.id;

            // Validação campos obrigatórios
            const obrigatorios = ["nome","data_nascimento","telefone","email","sexo","endereco","queixa"];
            for (let campo of obrigatorios) {
                if (!dadosParaEnviar[campo] || dadosParaEnviar[campo].trim() === "") {
                    alert(`O campo "${campo}" é obrigatório!`);
                    return;
                }
            }

            const formData = new FormData();
            for (let key in dadosParaEnviar) {
                formData.append(key, dadosParaEnviar[key]);
            }

            try {
                const response = await fetch("/agendamento/api/anamnese/atualizar/index.php", {
                    method: "POST",
                    headers: {
                        "Authorization": `Bearer ${token}`
                    },
                    body: formData
                });

                const result = await response.json();
                if (result.status) {
                    // Atualiza os <p> com valores novos
                    inputs.forEach(input => {
                        const p = document.createElement("p");

                        if (input.dataset.key === "data_nascimento" || input.dataset.key === "data") {
                            const partes = input.value.split("-");
                            p.textContent = partes.length === 3 ? `${partes[2]}/${partes[1]}/${partes[0]}` : input.value;
                        } else {
                            p.textContent = input.value;
                        }

                        p.dataset.key = input.dataset.key;
                        input.replaceWith(p);

                        data[input.dataset.key] = p.textContent;
                    });

                    Object.assign(originalData, data);
                    alert(result.message);
                    editButton.textContent = "Editar";
                    cancelButton.style.display = "none";
                } else {
                    alert("Erro ao atualizar: " + result.message);
                }

            } catch (error) {
                console.error("Erro na requisição:", error);
                alert("Erro ao atualizar os dados. Veja o console.");
            }
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
