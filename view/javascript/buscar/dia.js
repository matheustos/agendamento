// buscar e listar agendamentos
fetch("/agendamento/api/buscar/hoje/index.php")
.then(res => res.json())
.then(res => {
    const agendamentos = res.data; // pega a lista real
    console.log(res.data);
    let html = "";
    agendamentos.forEach(a => {
        html += `
            <tr>
                <td>${a.nome}</td>
                <td>${a.data}</td>
                <td>${a.horario}</td>
                <td>${a.servico}</td>
                <td>
                    <button class="button" onclick="editar(${a.id}, '${a.nome}', '${a.data}', '${a.horario}')">
                        Editar
                    </button>
                </td>
            </tr>
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
}

function salvar() {
    console.log("Salvar clicado!");
    let formData = new FormData();
    formData.append("id", document.getElementById("id").value);
    formData.append("nome", document.getElementById("nome").value);
    formData.append("data", document.getElementById("data").value);
    formData.append("horario", document.getElementById("horario").value);


    fetch("/agendamento/api/atualizar/index.php", {
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