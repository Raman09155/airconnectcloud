document.addEventListener("DOMContentLoaded", function () {
  // --- Animation for cards ---
  document
    .querySelectorAll(".animate-up, .animate-fade")
    .forEach(function (card) {
      card.classList.add("show");
    });

  // --- Blog Pagination Logic ---
  const cards = document.querySelectorAll(".blog-card");
  const cardsPerPage = 6;
  const pagination = document.querySelector(".pagination");
  let currentPage = 1;
  const totalPages = Math.ceil(cards.length / cardsPerPage);

  function showPage(page) {
    cards.forEach((card, index) => {
      card.parentElement.style.display =
        index >= (page - 1) * cardsPerPage && index < page * cardsPerPage
          ? "block"
          : "none";
    });

    // Update pagination active state
    pagination.querySelectorAll(".page-item").forEach((item) => {
      item.classList.remove("active", "disabled");
    });

    // Disable prev/next
    if (page === 1) pagination.querySelector(".prev").classList.add("disabled");
    if (page === totalPages)
      pagination.querySelector(".next").classList.add("disabled");

    // Activate current page
    pagination.querySelectorAll(".page-item")[page].classList.add("active");
  }

  // Generate pagination items dynamically
  if (pagination) {
    let pageItems = `<li class="page-item prev disabled"><a class="page-link" href="#">&lt;</a></li>`;
    for (let i = 1; i <= totalPages; i++) {
      pageItems += `<li class="page-item"><a class="page-link" href="#">${i}</a></li>`;
    }
    pageItems += `<li class="page-item next"><a class="page-link" href="#">&gt;</a></li>`;
    pagination.innerHTML = pageItems;

    // Handle clicks
    pagination.addEventListener("click", function (e) {
      e.preventDefault();
      if (e.target.tagName !== "A") return;

      if (
        e.target.parentElement.classList.contains("prev") &&
        currentPage > 1
      ) {
        currentPage--;
      } else if (
        e.target.parentElement.classList.contains("next") &&
        currentPage < totalPages
      ) {
        currentPage++;
      } else if (!isNaN(parseInt(e.target.textContent))) {
        currentPage = parseInt(e.target.textContent);
      }
      showPage(currentPage);
    });

    // Initial load
    showPage(currentPage);
  }
});
