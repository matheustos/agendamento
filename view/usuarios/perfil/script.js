const token = localStorage.getItem("token");

if (!token) {
  window.location.href = '/agendamento/view/login/index.html';
}


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

// Decodifica o token para pegar o acesso
function getUserAccessFromToken() {
    if (!token) return null;
    try {
        const payload = JSON.parse(atob(token.split('.')[1]));
        return payload.acesso;
    } catch (e) {
        return null;
    }
}

document.addEventListener("DOMContentLoaded", () => {
    // Esconde menus para cliente
    const acesso = getUserAccessFromToken();
    if (acesso === "cliente") {
        const menuBloquear = document.getElementById('menu-bloquear');
        const menuUsuarios = document.getElementById('menu-usuarios');
        if (menuBloquear) menuBloquear.style.display = "none";
        if (menuUsuarios) menuUsuarios.style.display = "none";
    }
});

const apiUrl = "/agendamento/api/usuarios/listar/id/index.php";

// Buscar dados do usuário
fetch(apiUrl, {
  method: "GET",
  headers: {
    "Authorization": `Bearer ${token}`
  }
})
  .then(response => {
    if (!response.ok) throw new Error("Erro ao buscar dados");
    return response.json();
  })
  .then(userArray => {
    if (!Array.isArray(userArray) || userArray.length === 0) {
      throw new Error("Nenhum usuário encontrado.");
    }

    const userData = userArray[0]; // Pega o primeiro usuário
    preencherPerfil(userData);
    configurarModalEdicao(userData, apiUrl, token);
  })
  .catch(error => {
    console.error("Erro ao carregar perfil:", error);
    alert("Não foi possível carregar os dados do usuário.");
  });

function preencherPerfil(userData) {
  document.getElementById("user-id").textContent = userData.id;
  document.getElementById("user-name").textContent = userData.nome;
  document.getElementById("user-email").textContent = userData.email;
  document.getElementById("user-phone").textContent = userData.telefone;
  document.getElementById("user").textContent = userData.nome;
}

function configurarModalEdicao(userData, apiUrl, token) {
  const modal = document.getElementById("edit-modal");
  modal.innerHTML = `
    <div class="modal-content">
      <span class="close">&times;</span>
      <h3>Editar Perfil</h3>
      <form id="edit-form">
        <label>ID:</label>
        <input type="text" name="id" value="${userData.id}" />
        <label>Nome:</label>
        <input type="text" name="nome" value="${userData.nome}" />
        <label>Email:</label>
        <input type="email" name="email" value="${userData.email}" />
        <label>Telefone:</label>
        <input type="text" name="telefone" value="${userData.telefone}" />
        <button type="submit">Salvar</button>
      </form>
    </div>
  `;
  modal.style.display = "none";

  const editBtn = document.getElementById("edit-btn");
  const closeBtn = modal.querySelector(".close");
  const editForm = modal.querySelector("#edit-form");

  editBtn.onclick = () => {
    modal.style.display = "flex";
  };

  closeBtn.onclick = () => {
    modal.style.display = "none";
  };

  window.onclick = (event) => {
    if (event.target === modal) {
      modal.style.display = "none";
    }
  };

  editForm.addEventListener("submit", (e) => {
    e.preventDefault();

    const formData = new FormData(editForm);
    
    fetch('/agendamento/api/usuarios/atualizar/index.php', {
      method: "POST",
      headers: {
        "Authorization": `Bearer ${token}`
      },
      body: formData
    })
      .then(response => {
        if (!response.ok) throw new Error("Erro ao salvar dados");
        return response.json();
      })
      .then(data => {
        preencherPerfil(data);
        modal.style.display = "none";
        location.reload();
      })
      .catch(error => {
        console.error("Erro ao enviar dados:", error);
        alert("Não foi possível salvar as alterações.");
      });
  });

  function configurarModalSenha(token) {
  const modalSenha = document.getElementById("password-modal");
  modalSenha.innerHTML = `
    <div class="modal-content">
      <span class="close">&times;</span>
      <h3>Atualizar Senha</h3>
      <form id="password-form">
        <label>Email:</label><br>
        <input type="email" name="email" required /><br>

        <label>Nova Senha:</label><br>
        <input type="password" name="nova_senha" required /><br>

        <label>Confirmar Senha:</label><br>
        <input type="password" name="confirmar_senha" required /><br>

        <button type="submit">Atualizar</button>
      </form>
    </div>
  `;
  modalSenha.style.display = "none";

  const passwordBtn = document.getElementById("password-btn"); // botão "Alterar Senha"
  const closeBtn = modalSenha.querySelector(".close");
  const passwordForm = modalSenha.querySelector("#password-form");

  // Abrir modal
  passwordBtn.onclick = () => {
    modalSenha.style.display = "flex";
  };

  // Fechar modal
  closeBtn.onclick = () => {
    modalSenha.style.display = "none";
  };

  window.onclick = (event) => {
    if (event.target === modalSenha) {
      modalSenha.style.display = "none";
    }
  };

  // Enviar formulário
  passwordForm.addEventListener("submit", (e) => {
    e.preventDefault();

    const formData = new FormData(passwordForm);
    const novaSenha = formData.get("nova_senha");
    const confirmarSenha = formData.get("confirmar_senha");

    if (novaSenha !== confirmarSenha) {
      alert("As senhas não coincidem!");
      return;
    }

    fetch('/agendamento/api/usuarios/atualizar_senha/index.php', { // tua rota nova
      method: "POST",
      headers: {
        "Authorization": `Bearer ${token}`
      },
      body: formData
    })
      .then(response => {
        if (!response.ok) throw new Error("Erro ao atualizar senha");
        return response.json();
      })
      .then(data => {
        alert("Senha atualizada com sucesso!");
        modalSenha.style.display = "none";
      })
      .catch(error => {
        console.error("Erro ao atualizar senha:", error);
        alert("Não foi possível atualizar a senha.");
      });
  });
}
configurarModalSenha(token);

  document.getElementById('btnLogoutSide').addEventListener('click', function() {
    // Remove o token JWT do localStorage
    localStorage.removeItem('token'); // ou sessionStorage.removeItem('token');

    // Redireciona para a página de login
    window.location.href = '../../login/index.html';
});
}