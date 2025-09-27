// =====================
// Mostrar/Esconder formulário de despesa
// =====================
const btnCadastrarDespesa = document.getElementById("btnCadastrarDespesa");
const formDespesa = document.getElementById("formDespesa");
const btnCancelarDespesa = document.getElementById("btnCancelarDespesa");

btnCadastrarDespesa.addEventListener("click", () => {
  formDespesa.classList.remove("hidden");
});

btnCancelarDespesa.addEventListener("click", () => {
  formDespesa.classList.add("hidden");
  limparFormDespesa();
});

function limparFormDespesa() {
  document.getElementById("nomeDespesa").value = "";
  document.getElementById("quantidadeDespesa").value = 1;
  document.getElementById("precoDespesa").value = 0;
  document.getElementById("data").value = "";
}

// =====================
// Salvar despesa via requisição POST
// =====================
document.getElementById("btnSalvarDespesa").addEventListener("click", async () => {
  const nome = document.getElementById("nomeDespesa").value.trim();
  const quantidade = Number(document.getElementById("quantidadeDespesa").value);
  const preco = Number(document.getElementById("precoDespesa").value);
  const data = document.getElementById("data").value;
  const mes = document.getElementById("filtroMes").value;
  const ano = Number(document.getElementById("filtroAno").value);

  if (!nome || quantidade <= 0 || preco < 0 || !data || !ano) {
    alert("Preencha todos os campos corretamente.");
    return;
  }

  const formData = new FormData();
  formData.append("nome", nome);
  formData.append("quantidade", quantidade);
  formData.append("preco", preco);
  formData.append("data", data);
  formData.append("ano", ano);
  formData.append("mes", mes);

  try {
    const response = await fetch("/agendamento/api/contabilidade/cadastrar_despesa/index.php", {
      method: "POST",
      body: formData
    });

    const result = await response.json();

    if (result.status) {
      alert("Despesa cadastrada com sucesso!");
      formDespesa.classList.add("hidden");
      limparFormDespesa();
      await filtrarDados(mes, ano); // atualiza resumo e movimentações
    } else {
      alert(result.message);
    }
  } catch (error) {
    console.error(error);
    alert("Erro ao cadastrar despesa.");
  }
});

// =====================
// Função central para filtrar dados (mês e ano)
// =====================
async function filtrarDados(mes, ano) {
  try {
    const formData = new FormData();
    formData.append("ano", ano);
    formData.append("mes", mes || ""); // envia "" se 'Todos' estiver selecionado

    // 1️⃣ Carregar resumo
    const resResumo = await fetch("/agendamento/api/contabilidade/filtro/index.php", {
      method: "POST",
      body: formData
    });

    const resumo = await resResumo.json();
    const faturamento = Number(resumo[0] || 0);
    const despesas = Number(resumo.DespesasMes || 0);
    const saldo = faturamento - despesas;

    // Atualiza cards
    document.getElementById("faturamento").textContent =
      faturamento.toLocaleString("pt-BR", { style: "currency", currency: "BRL" });
    document.getElementById("despesas").textContent =
      despesas.toLocaleString("pt-BR", { style: "currency", currency: "BRL" });
    document.getElementById("saldo").textContent =
      saldo.toLocaleString("pt-BR", { style: "currency", currency: "BRL" });

    // 2️⃣ Carregar movimentações (vendas + despesas + serviços)
    const urlVendas = mes ? "/agendamento/api/contabilidade/index.php" : "/agendamento/api/contabilidade/vendas_ano/index.php";
    const urlDespesas = mes ? "/agendamento/api/contabilidade/despesas/index.php" : "/agendamento/api/contabilidade/despesas_ano/index.php";
    const urlServicos = "/agendamento/api/contabilidade/servicos/index.php";

    const [resVendas, resDespesas, resServicos] = await Promise.all([
      fetch(urlVendas, { method: "POST", body: formData }),
      fetch(urlDespesas, { method: "POST", body: formData }),
      fetch(urlServicos, { method: "POST", body: formData })
    ]);

    // ✅ Chama .json() apenas uma vez
    const dataVendas = await resVendas.json();
    const dataDespesas = await resDespesas.json();
    const dataServicos = await resServicos.json();

    const vendas = Array.isArray(dataVendas) ? dataVendas : [];
    const despesasList = Array.isArray(dataDespesas) ? dataDespesas : [];
    const servicosList = Array.isArray(dataServicos) ? dataServicos : [];

    const todasMovimentacoes = [
      ...vendas.map(v => ({ ...v, tipo: "venda" })),
      ...despesasList.map(d => ({ ...d, tipo: "despesa" })),
      ...servicosList.map(s => ({ ...s, tipo: "servico" }))
    ];

    // Ordena por data (mais recente primeiro)
    todasMovimentacoes.sort((a, b) => {
      const d1 = a.data.split("/").reverse().join("-");
      const d2 = b.data.split("/").reverse().join("-");
      return new Date(d2) - new Date(d1);
    });

    // Renderiza lista
    const listaMovimentacoes = document.getElementById("lista-movimentacoes");
    listaMovimentacoes.innerHTML = "";

    if (!todasMovimentacoes.length) {
      listaMovimentacoes.innerHTML = "<li>Nenhuma movimentação encontrada.</li>";
      return;
    }

    todasMovimentacoes.forEach(mov => {
      const quantidade = Number(mov.quantidade || 0);
      const valorOriginal = Number(mov.valor || 0);
      const nomeExibido = mov.nome || "-";
      const descricao = mov.descricao || (mov.tipo === "servico" ? "Serviço" : "-");

      let valorExibido = valorOriginal;
      let classeValor = "valorPositivo";

      if (mov.tipo === "despesa") {
        valorExibido = -Math.abs(valorOriginal);
        classeValor = "valorNegativo";
      } else if (mov.tipo === "venda") {
        classeValor = valorOriginal > 0 ? "valorPositivo" : "valorNegativo";
      } else if (mov.tipo === "servico") {
        classeValor = "valorNeutro";
      }

      const li = document.createElement("li");
      li.innerHTML = `
        <div>
          <strong>${descricao}</strong>
          <small>${nomeExibido}</small>
          <small>${mov.data || "-"}</small>
          <small>Qtd: ${quantidade}</small>
        </div>
        <span class="${classeValor}">
          ${valorExibido.toLocaleString("pt-BR", { style: "currency", currency: "BRL" })}
        </span>
      `;
      listaMovimentacoes.appendChild(li);
    });

  } catch (error) {
    console.error("Erro ao filtrar dados:", error);
    alert("Erro ao carregar dados filtrados.");
  }
}

// =====================
// Filtro por mês e ano (botão)
// =====================
document.getElementById("btnFiltrar").addEventListener("click", () => {
  const mes = document.getElementById("filtroMes").value;
  const ano = Number(document.getElementById("filtroAno").value);
  if (!ano) return;

  filtrarDados(mes, ano);
});

// =====================
// Carregamento inicial (mês atual)
// =====================
const hoje = new Date();
const mesAtual = hoje.getMonth() + 1;
const anoAtual = hoje.getFullYear();

document.getElementById("filtroMes").value = mesAtual;
document.getElementById("filtroAno").value = anoAtual;

// Carregar dados iniciais
filtrarDados(mesAtual, anoAtual);
