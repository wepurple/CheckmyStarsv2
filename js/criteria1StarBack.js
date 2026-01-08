(async () => {
  const tbody = document.getElementById("table-body");

  const res = await fetch("api/criteria_by_star.php?star=1", {
    credentials: "same-origin" // pour garder la session PHP
  });

  const data = await res.json();

  if (!res.ok) {
    tbody.innerHTML = `<tr><td colspan="4">${data.error ?? "Erreur"}</td></tr>`;
    return;
  }

  tbody.innerHTML = data.map(row => `
    <tr>
      <td>${row.id}</td>
      <td>${row.description ?? ""}</td>
      <td>${row.statut ?? ""}</td>
      <td>${row.points ?? ""}</td>
    </tr>
  `).join("");
})();
