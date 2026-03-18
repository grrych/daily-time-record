function closeFlash() {
    const flash = document.getElementById("flash-message");
    if (flash) {
        flash.style.opacity = "0";
        setTimeout(() => flash.remove(), 500);
    }
}

function flashAutoHide() {
    const flash = document.getElementById("flash-message");
    if (flash) {
        setTimeout(() => {
            closeFlash();
        }, 2000);
    }
}

document.addEventListener("DOMContentLoaded", flashAutoHide);

const userBtn = document.getElementById("userMenuBtn");
const dropdown = document.getElementById("userDropdown");

userBtn.addEventListener("click", () => {
    dropdown.classList.toggle("hidden");
});

document.addEventListener("click", (e) => {
    if (!userBtn.contains(e.target) && !dropdown.contains(e.target)) {
        dropdown.classList.add("hidden");
    }
});

// ----------- Modal ----------
document.querySelectorAll("[data-modal-open]").forEach(button => {

    button.addEventListener("click", () => {

        const modalId = button.dataset.modalOpen;
        const modal = document.getElementById(modalId);
        const modalContent = modal.querySelector("[data-modal-content]");

        modal.classList.remove("hidden");

        // Fill modal fields automatically
        Object.keys(button.dataset).forEach(key => {

            if (key.startsWith("fill")) {

                const field = key.replace("fill", "").toLowerCase();
                const value = button.dataset[key];

                const input = modal.querySelector(`[name="${field}"], #${field}`);

                if (input) {
                    input.value = value;

                    if (input.tagName === "P" || input.tagName === "SPAN" || input.tagName === "DIV") {
                        input.textContent = value;
                    }
                }

            }

        });

        requestAnimationFrame(() => {
            modalContent.classList.remove("scale-90", "opacity-0");
            modalContent.classList.add("scale-100", "opacity-100");
        });

    });

});

// CLOSE MODAL
document.addEventListener("click", (e) => {

    if (e.target.matches("[data-modal-close]")) {

        const modal = e.target.closest("[data-modal]");
        closeModal(modal);

    }

});

// CLOSE WHEN CLICK OUTSIDE
document.querySelectorAll("[data-modal]").forEach(modal => {

    modal.addEventListener("click", (e) => {

        if (e.target === modal) {
            closeModal(modal);
        }

    });

});

function closeModal(modal) {

    const modalContent = modal.querySelector("[data-modal-content]");

    modalContent.classList.add("scale-90", "opacity-0");
    modalContent.classList.remove("scale-100", "opacity-100");

    setTimeout(() => modal.classList.add("hidden"), 150);
}