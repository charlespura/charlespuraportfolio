
  // Function to handle drag-swap disabling and hiding paragraph
  function handleResponsiveDragSwap() {
    const infoParagraph = document.querySelector("#projects p.text-md.text-gray-500");
    const projectCards = document.querySelectorAll("#project-grid > div");

    if (window.innerWidth <= 640) { // e.g., 640px = Tailwind's sm breakpoint
      // Hide the paragraph
      if (infoParagraph) {
        infoParagraph.style.display = "none";
      }

      // Remove drag-swap classes (example: 'cursor-move')
      projectCards.forEach(card => {
        card.classList.remove("cursor-move");
      });
    } else {
      // Show the paragraph
      if (infoParagraph) {
        infoParagraph.style.display = "block";
      }

      // Re-add drag-swap classes
      projectCards.forEach(card => {
        if (!card.classList.contains("cursor-move")) {
          card.classList.add("cursor-move");
        }
      });
    }
  }

  // Run on page load
  window.addEventListener("load", handleResponsiveDragSwap);

  // Run on window resize
  window.addEventListener("resize", handleResponsiveDragSwap);

