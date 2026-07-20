function script() {
  document.addEventListener("DOMContentLoaded", () => {
    faceSettings();
    cacheSettings();
  });
}

// Implement adding and removing face ids to settings
// All changes here are reflected in front end

function faceSettings() {
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
              <span class="face-name">${faceId}</span>
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

          card.querySelector(".face-name").textContent = faceData.face_name;
        }
      } catch {}
    });
  });
}

function cacheSettings() {
  const cacheBtn = document.querySelector(".clear-cache");
  const cacheStatus = document.querySelector(".cache-status");

  if (!cacheBtn) return;

  cacheBtn.addEventListener("click", async () => {
    cacheBtn.disabled = true;
    cacheBtn.textContent = "Clearing Cache...";

    try {
      const res = await fetch("/wp-json/tavus/v1/invalidate");
      const data = await res.json();

      cacheStatus.textContent = data.message || "Cache cleared";
      cacheBtn.disabled = false;
      cacheBtn.textContent = "Clear Cache";
    } catch {
      cacheStatus.textContent = "Failed to clear cache";
      cacheBtn.disabled = false;
      cacheBtn.textContent = "Clear Cache";
    }
  });
}

script();
