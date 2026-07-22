function script() {
  document.addEventListener("DOMContentLoaded", () => {
    const faceIdInputs = [...document.querySelectorAll(".add-face")];

    faceIdInputs.forEach((container) => {
      const idInput = container.querySelector('input[type="text"]');
      const addBtn = container.querySelector("button");

      const facesGrid = container.nextElementSibling;
      const faceCards = [...facesGrid.querySelectorAll(".face-card")];

      // Remove avatar logic
      faceCards.forEach((card) => {
        card.addEventListener("click", (e) => {
          if (e.target.classList.contains("remove-face")) {
            e.target.closest(".faces-grid > div").remove();
          }
        });
      });

      // Add avatar

      addBtn.addEventListener("click", (e) => {
        const track = e.target.getAttribute("data-track");
        if (idInput.value === "") {
          alert("Please enter a valid face id");
          return;
        }

        const faceCard = `
            <div class="face-card">
              <input type="hidden" name="tavus_face_settings[face_ids][${track}][]" value="${idInput.value}" />
              <div class="video-wrapper">
                <div class="temp"></div>
                <button type="button" class="remove-face">X</button>
              </div>
              <span>${idInput.value}</span>
            </div>
          `;

        facesGrid.insertAdjacentHTML("beforeend", faceCard);

        idInput.value = "";
      });
    });
  });
}

script();
