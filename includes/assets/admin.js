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

      addBtn.addEventListener("click", async (e) => {
        const track = e.target.getAttribute("data-track");
        const faceId = idInput.value.trim();
        if (faceId === "") {
          alert("Please enter a valid face id");
          return;
        }

        const faceCard = `
            <div class="face-card" data-face-id="${faceId}">
              <input type="hidden" name="tavus_face_settings[face_ids][${track}][]" value="${faceId}" />
              <div class="video-wrapper">
                <div class="temp"></div>
                <button type="button" class="remove-face">X</button>
              </div>
              <span>${faceId}</span>
            </div>
          `;

        facesGrid.insertAdjacentHTML("beforeend", faceCard);

        idInput.value = "";

        // Fetch face data
        try {
          const res = await fetch(`/wp-json/tavus/v1/faces?face_id=${faceId}`);
          const data = await res.json();
          const faceData = data[0];

          if (faceData?.thumbnail_video_url) {
            const card = facesGrid.querySelector(`[data-face-id="${faceId}"]`);
            if (!card) return;

            card.querySelector(".temp").outerHTML = `
                <video src="${faceData.thumbnail_video_url}"></video>
              `;

            card.querySelector("span").textContent = faceData.face_name;
          }
        } catch (e) {}
      });
    });
  });
}

script();
