const token = localStorage.getItem('token');

if(!token){
    window.location.href="../login/index.html";
}

// -----------------------
// FUNÇÃO LOGOUT
// -----------------------
function logout() {
    localStorage.removeItem('token');
    sessionStorage.removeItem('token');
    window.location.href = "../login/index.html";
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

    // DECODIFICAR TOKEN
    function getUserAccessFromToken() {
        try {
            const payload = JSON.parse(atob(token.split('.')[1]));
            return payload.acesso;
        } catch (e) {
            return null;
        }
    }

    const acesso = getUserAccessFromToken();
    if (acesso === "cliente") {
        window.location.href="../agenda/index.html";
    }
});

// -----------------------
// PRODUTOS E MOVIMENTAÇÃO
// -----------------------
document.addEventListener("DOMContentLoaded", () => {
    const container = document.getElementById("products-list");

    const modal = document.getElementById("modal");
    const modalTitle = document.getElementById("modal-title");
    const modalMov = document.getElementById("modal-movimentacao");

    const movProdutoId = document.getElementById("mov-produto-id");
    const movTipo = document.getElementById("mov-tipo");
    const movQuantidade = document.getElementById("mov-quantidade");

    let editMode = false;

    // === ABRIR MODAL CADASTRO/EDIÇÃO ===
    window.openModal = function(produto = null) {
        modal.style.display = "flex";
        const productId = document.getElementById("product-id");
        const productName = document.getElementById("product-name");
        const productQty = document.getElementById("product-qty");
        const productPrice = document.getElementById("product-price");

        if (produto) {
            editMode = true;
            modalTitle.innerText = "Editar Produto";
            productId.value = produto.id;
            productName.value = produto.nome;
            productQty.value = produto.quantidade;
            productPrice.value = produto.preco;
        } else {
            editMode = false;
            modalTitle.innerText = "Cadastrar Produto";
            productId.value = "";
            productName.value = "";
            productQty.value = "";
            productPrice.value = "";
        }
    };

    // === FECHAR MODAL CADASTRO/EDIÇÃO ===
    window.closeModal = function() {
        modal.style.display = "none";
    };

    // === SALVAR PRODUTO ===
    window.saveProduct = function() {
        const productId = document.getElementById("product-id");
        const productName = document.getElementById("product-name");
        const productQty = document.getElementById("product-qty");
        const productPrice = document.getElementById("product-price");

        const formData = new FormData();
        if(editMode) formData.append("id", productId.value);
        formData.append("nome", productName.value);
        formData.append("quantidade", productQty.value);
        formData.append("preco", productPrice.value);

        const url = editMode 
            ? "/agendamento/api/produtos/atualizar/index.php" 
            : "/agendamento/api/produtos/cadastrar/index.php";

        fetch(url, {
            method: "POST",
            headers: { 'Authorization': `Bearer ${token}` },
            body: formData
        })
        .then(res => res.text())
        .then(text => {
            let response;
            try {
                response = JSON.parse(text);
            } catch(e){
                console.error("Resposta inválida do servidor:", text);
                alert("Erro: resposta inválida do servidor.");
                return;
            }

            alert(response.message || "Operação concluída!");
            if(response.status) {
                closeModal();
                carregarProdutos();
            }
        })
        .catch(err => {
            console.error("Erro:", err);
            alert("Erro ao se comunicar com a API.");
        });
    };

    // === REMOVER PRODUTO ===
    function removerProduto(id) {
        if(confirm("Tem certeza que deseja remover este produto?")) {
            const formData = new FormData();
            formData.append("id", id);

            fetch("/agendamento/api/produtos/remover/index.php", {
                method: "POST",
                headers: { 'Authorization': `Bearer ${token}` },
                body: formData
            })
            .then(res => res.text())
            .then(text => {
                let response;
                try {
                    response = JSON.parse(text);
                } catch(e){
                    console.error("Resposta inválida do servidor:", text);
                    alert("Erro: resposta inválida do servidor.");
                    return;
                }

                alert(response.message || "Operação concluída!");
                if(response.status) carregarProdutos();
            })
            .catch(err => console.error("Erro:", err));
        }
    }

    // === ABRIR MODAL DE MOVIMENTAÇÃO ===
    window.abrirModalMovimentacao = function(produtoId) {
        movProdutoId.value = produtoId;
        movQuantidade.value = "";
        movTipo.value = "entrada";
        modalMov.style.display = "flex";
    };

    // === FECHAR MODAL DE MOVIMENTAÇÃO ===
    window.fecharModalMovimentacao = function() {
        modalMov.style.display = "none";
    };

    // === SALVAR MOVIMENTAÇÃO ===
    window.salvarMovimentacao = function() {
        const id = movProdutoId.value;
        const tipo = movTipo.value;
        const quantidade = parseInt(movQuantidade.value, 10);

        if(!id || !tipo || isNaN(quantidade) || quantidade <= 0){
            alert("Preencha todos os campos corretamente!");
            return;
        }

        const formData = new FormData();
        formData.append("id", id);
        formData.append("tipo", tipo);
        formData.append("quantidade", quantidade);

        fetch("/agendamento/api/produtos/movimentacao/index.php", {
            method: "POST",
            headers: { 'Authorization': `Bearer ${token}` },
            body: formData
        })
        .then(res => res.text())
        .then(text => {
            let response;
            try {
                response = JSON.parse(text);
            } catch(e){
                console.error("Resposta inválida do servidor:", text);
                alert("Erro: resposta inválida do servidor.");
                return;
            }

            alert(response.message || "Movimentação concluída!");
            if(response.status){
                fecharModalMovimentacao();
                carregarProdutos();
            }
        })
        .catch(err => {
            console.error("Erro ao se comunicar com a API:", err);
            alert("Erro ao enviar dados para o servidor.");
        });
    };

    // === CARREGAR PRODUTOS ===
    function carregarProdutos() {
        fetch("/agendamento/api/produtos/index.php", {
            method: "GET",
            headers: { 'Authorization': `Bearer ${token}` }
        })
        .then(res => res.text())
        .then(text => {
            let response;
            try {
                response = JSON.parse(text);
            } catch(e){
                console.error("Resposta inválida do servidor:", text);
                container.innerHTML = "<p>Erro ao carregar produtos.</p>";
                return;
            }

            container.innerHTML = "";
            if(response.status && response.data.length > 0) {
                response.data.forEach(produto => {
                    const card = document.createElement("div");
                    card.className = "product-card";
                    card.innerHTML = `
                        <p><strong>ID:</strong> ${produto.id}</p>
                        <p><strong>Nome:</strong> ${produto.nome}</p>
                        <p><strong>Quantidade:</strong> ${produto.quantidade}</p>
                        <p><strong>Preço:</strong> R$ ${produto.preco}</p>
                        <button class="btn-edit">Editar</button>
                        <button class="btn-remove">Remover</button>
                    `;
                    card.querySelector(".btn-edit").addEventListener("click", () => openModal(produto));
                    card.querySelector(".btn-remove").addEventListener("click", () => removerProduto(produto.id));

                    // Botão Entrada/Saída
                    const btnMov = document.createElement("button");
                    btnMov.className = "btn-movimentacao";
                    btnMov.textContent = "Entrada/Saída";
                    btnMov.addEventListener("click", () => abrirModalMovimentacao(produto.id));
                    card.appendChild(btnMov);

                    container.appendChild(card);
                });
            } else {
                container.innerHTML = "<p>Nenhum produto encontrado!</p>";
            }
        })
        .catch(err => {
            console.error("Erro:", err);
            container.innerHTML = "<p>Erro ao carregar produtos.</p>";
        });
    }

    // Carrega produtos ao iniciar
    carregarProdutos();
});
