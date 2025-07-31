// scripts.js
document.addEventListener("DOMContentLoaded", () => {
    const materialsTable = document.querySelector("#materialsTable tbody");
    const addMaterialForm = document.querySelector("#addMaterialForm");
  
    // Load initial data
    const loadMaterials = () => {
      fetch("load_materials.php")
        .then((response) => response.json())
        .then((data) => {
          materialsTable.innerHTML = "";
          data.forEach((material) => {
            const row = `
              <tr>
                <td>${material.material_id}</td>
                <td>${material.name}</td>
                <td>${material.stock_quantity}</td>
                <td>${material.threshold}</td>
                <td>${material.unit}</td>
                <td>
                  <button onclick="deleteMaterial(${material.material_id})">Delete</button>
                </td>
              </tr>
            `;
            materialsTable.innerHTML += row;
          });
        });
    };
  
    // Add new material
    addMaterialForm.addEventListener("submit", (event) => {
      event.preventDefault();
      const name = document.querySelector("#materialName").value;
      const stockQuantity = document.querySelector("#stockQuantity").value;
      const threshold = document.querySelector("#threshold").value;
      const unit = document.querySelector("#unit").value;
  
      fetch("add_material.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ name, stockQuantity, threshold, unit }),
      })
        .then((response) => response.json())
        .then((result) => {
          alert(result.message);
          loadMaterials();
        });
    });
  
    // Load materials on page load
    loadMaterials();
  });
  