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

    // -----------------------
    // DECODIFICAR TOKEN
    // -----------------------
    function getUserAccessFromToken() {
        try {
            const payload = JSON.parse(atob(token.split('.')[1]));
            return payload.acesso;
        } catch (e) {
            return null;
        }
    }

    // Esconde menus para clientes
    const acesso = getUserAccessFromToken();
    if (acesso === "cliente") {
        window.location.href="../agenda/index.html";
    }
});

document.addEventListener("DOMContentLoaded", () => {
    const container = document.getElementById("products-list");
    const modal = document.getElementById("modal");
    const modalTitle = document.getElementById("modal-title");

    let editMode = false; // controla se estamos editando ou cadastrando

    // === ABRIR MODAL ===
    window.openModal = function(produto = null) {
        modal.style.display = "flex";

        // Captura os inputs dentro da função
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

    // === FECHAR MODAL ===
    window.closeModal = function() {
        modal.style.display = "none";
    };

    // === SALVAR PRODUTO (CADASTRAR / EDITAR) ===
    window.saveProduct = function() {
        const productId = document.getElementById("product-id");
        const productName = document.getElementById("product-name");
        const productQty = document.getElementById("product-qty");
        const productPrice = document.getElementById("product-price");

        // Debug: mostra os valores antes de enviar
        console.log("Nome:", productName.value);
        console.log("Quantidade:", productQty.value);
        console.log("Preço:", productPrice.value);

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
            body: formData
        })
        .then(res => res.json())
        .then(response => {
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
            formData.append("id", id); // envia o ID via FormData

            fetch("/agendamento/api/produtos/remover/index.php", {
                method: "POST",
                headers: { 'Authorization': `Bearer ${token}` },
                body: formData
            })
            .then(res => res.json())
            .then(response => {
                alert(response.message || "Operação concluída!");
                if(response.status) carregarProdutos();
            })
            .catch(err => console.error("Erro:", err));
        }
    }


    // === LISTAR PRODUTOS ===
    function carregarProdutos() {
        fetch("/agendamento/api/produtos/index.php", {
            method: "GET",
            headers: { 'Authorization': `Bearer ${token}` }
        })
            .then(res => res.json())
            .then(response => {
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
                        container.appendChild(card);
                    });
                } else {
                    container.innerHTML = "<p>Nenhum produto encontrado!</p>";
                }
            })
            .catch(err => console.error("Erro:", err));
    }

    // Carrega produtos ao iniciar
    carregarProdutos();
});
