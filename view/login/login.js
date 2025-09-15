document.getElementById("login").addEventListener("submit", async function(e){
    e.preventDefault(); // evita que o formulário seja enviado normalmente

    const email = document.getElementById("email").value;
    const senha = document.getElementById("senha").value;

    try {
        const response = await fetch("/agendamento/api/login/index.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({ email, senha })
        });

        const data = await response.json();

        if(data.status){
            // Salva o token no localStorage ou sessionStorage
            localStorage.setItem("token", data.token);

            // Redireciona para o dashboard
            window.location.href = "../agenda/index.html";
        } else {
            document.getElementById("message").innerText = data.message;
        }

    } catch (error) {
        console.error("Erro no login:", error);
        document.getElementById("message").innerText = "Erro ao conectar com o servidor.";
    }
});