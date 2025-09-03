// buscar e listar agendamentos
fetch("../../api/buscar/hoje")
.then(res => res.json())
.then(res => {
    const agendamentos = res.data; // pega a lista real
    let html = "";
    agendamentos.forEach(a => {
        html += `
            <div>
            <b>${a.nome}</b> - ${a.data} ${a.horario} (${a.servico})
            <button onclick="editar(${a.id}, '${a.nome}', '${a.data}', '${a.horario}')">Editar</button>
            </div>
        `;
    });
    document.getElementById("lista").innerHTML = html;
});

function editar(id, nome, data, hora) {
    document.getElementById("form-editar").style.display = "block";
    document.getElementById("id").value = id;
    document.getElementById("nome").value = nome;
    document.getElementById("data").value = data;
    document.getElementById("horario").value = hora;

    // aqui você preenche o email manualmente, ou deixa um campo para o usuário digitar
    document.getElementById("email").value = ""; // pode deixar em branco ou colocar valor padrão
}

// Cancelar edição
function cancelar() {
    document.getElementById("form-editar").style.display = "none";
    limparFormulario();
}

// Limpa o formulário
function limparFormulario() {
    document.getElementById("id").value = "";
    document.getElementById("nome").value = "";
    document.getElementById("data").value = "";
    document.getElementById("horario").value = "";
    document.getElementById("email").value = "";
}

function salvar() {
    console.log("Salvar clicado!");
    let formData = new FormData();
    formData.append("id", document.getElementById("id").value);
    formData.append("nome", document.getElementById("nome").value);
    formData.append("data", document.getElementById("data").value);
    formData.append("horario", document.getElementById("horario").value);
    formData.append("email", document.getElementById("email").value);


    fetch("../../api/buscar/hoje/index.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.json())
    .then(res => {
        if (res.status === "success") {
        alert("Agendamento atualizado!");
        location.reload();
        } else {
        alert("Erro: " + res.message);
        }
    });
}