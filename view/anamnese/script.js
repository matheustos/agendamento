const form = document.getElementById("anamnese");

  // Limpar
  document.getElementById("limpar").addEventListener("click", (e) => {
    e.preventDefault();
    if (confirm("Limpar todos os campos?")) form.reset();
  });

  // Imprimir
  document.getElementById("imprimir").addEventListener("click", (e) => {
    e.preventDefault();
    window.print();
  });

  // Salvar via fetch, mas como formulário normal
  document.getElementById("salvar").addEventListener("click", async () => {
    if (!form.checkValidity()) {
      alert("Preencha todos os campos obrigatórios.");
      return;
    }

    const formData = new FormData(form); // pega dados do form

    try {
      const response = await fetch("/agendamento/api/anamnese/index.php", {
        method: "POST",
        body: formData // manda igual formulário
      });

      if (!response.ok) throw new Error("Erro no servidor");

      const result = await response.json();
      alert("Ficha enviada com sucesso!");
      form.reset();
    } catch (err) {
      alert("Falha ao enviar: " + err.message);
    }
  });