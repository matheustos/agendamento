const token = localStorage.getItem('token');

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
    if (btnLogoutSide) {
        btnLogoutSide.addEventListener('click', logout);
    }
});

document.addEventListener("DOMContentLoaded", () => {
    
    if (!token) {
        window.location.href = '/agendamento/view/login/index.html';
        return;
    }

    // Decodifica o payload do JWT
    let acesso = null;
    try {
        const payload = JSON.parse(atob(token.split('.')[1]));
        acesso = payload.acesso;
    } catch (e) {
        window.location.href = '/agendamento/view/login/index.html';
        return;
    }

    if (acesso !== 'admin') {
        window.location.href = '/agendamento/view/agenda/index.html';
        return;
    }
    carregarUsuarios();

    // Modal editar
    const modal = document.getElementById('modal-editar');
    const closeModal = document.getElementById('closeModal');
    closeModal.onclick = () => modal.style.display = "none";
    window.onclick = (e) => { if(e.target === modal) modal.style.display = "none"; }

    document.getElementById('form-editar').onsubmit = function(e) {
        e.preventDefault();
        const id = document.getElementById('edit-id').value;
        const nome = document.getElementById('edit-nome').value;
        const email = document.getElementById('edit-email').value;
        const telefone = document.getElementById('edit-telefone').value;

        const formData = new FormData();
        formData.append('id', id);
        formData.append('nome', nome);
        formData.append('email', email);
        formData.append('telefone', telefone);

        fetch('/agendamento/api/usuarios/atualizar/index.php', {
            method: 'POST',
            body: formData, 
            headers: {
                "Authorization": `Bearer ${token}`  
            }
        })
        .then(res => res.json())
        .then(() => {
            modal.style.display = "none";
            carregarUsuarios();
        });
    };
});

function carregarUsuarios() {
    fetch('/agendamento/api/usuarios/listar/index.php',{
            method: 'GET',
            headers: {
            "Authorization": `Bearer ${token}`
        }
    })
        .then(res => res.json())
        .then(retorno => {
            const lista = document.getElementById('usuarios-list');
            lista.innerHTML = '';
            if(retorno.status && Array.isArray(retorno.data)) {
                retorno.data.forEach(u => {
                    const card = document.createElement('div');
                    card.className = 'usuario-card';
                    card.innerHTML = `
                        <div class="usuario-info">
                            <div class="usuario-header">
                                <span class="label">ID</span>
                                <span class="label">Nome</span>
                                <span class="label">E-mail</span>
                                <span class="label">Telefone</span>
                            </div>
                            <div class="usuario-dados">
                                <span class="usuario-id">${u.id}</span>
                                <span class="usuario-nome">${u.nome}</span>
                                <span class="usuario-email">${u.email}</span>
                                <span class="usuario-telefone">${u.telefone}</span>
                            </div>
                        </div>
                        <div class="usuario-actions">
                            <button class="btn-editar">Editar</button>
                            <button class="btn-remover">Remover</button>
                        </div>
                    `;
                    // Editar
                    card.querySelector('.btn-editar').onclick = () => abrirModalEditar(u);
                    // Remover
                    card.querySelector('.btn-remover').onclick = () => removerUsuario(u.id);
                    lista.appendChild(card);
                });
            } else {
                lista.innerHTML = '<p>Nenhum usuário encontrado.</p>';
            }
        });
}

function abrirModalEditar(usuario) {
    document.getElementById('edit-id').value = usuario.id;
    document.getElementById('edit-nome').value = usuario.nome;
    document.getElementById('edit-email').value = usuario.email;
    document.getElementById('edit-telefone').value = usuario.telefone;
    document.getElementById('modal-editar').style.display = "flex";
}

function removerUsuario(id) {
    if(confirm("Deseja remover este usuário?")) {
        const formData = new FormData();
        formData.append('id', id);

        fetch('/agendamento/api/usuarios/remover/index.php', {
            method: 'POST',
            body: formData,
            headers: {
                "Authorization": `Bearer ${token}`
            }
        })
        .then(res => res.json())
        .then(() => carregarUsuarios());
    }
}

document.getElementById('btnLogoutSide').addEventListener('click', function() {
    // Remove o token JWT do localStorage
    localStorage.removeItem('token'); // ou sessionStorage.removeItem('token');

    // Redireciona para a página de login
    window.location.href = '../../login/index.html';
});