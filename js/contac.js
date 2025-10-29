document.querySelector("form").addEventListener("submit", function (event) {
  event.preventDefault();

  const formData = new FormData(this);
  const submitButton = document.querySelector('[type="submit"]');
  const responseDiv = document.getElementById("form-response");

  submitButton.disabled = true;
  submitButton.innerHTML = "Sending...";
  responseDiv.innerHTML = ""; // Clear previous message

  fetch("contact.php", {
    method: "POST",
    body: formData,
  })
    .then((response) => {
      if (!response.ok) {
        throw new Error("Server error. Please try again later.");
      }
      return response.json();
    })
    .then((data) => {
      // Style and display message on same page
      responseDiv.innerHTML = `<div class="${
        data.status === "success" ? "text-success" : "text-danger"
      }">${data.message}</div>`;

      if (data.status === "success") {
        document.querySelector("form").reset();
        
        // Google Analytics Success Event
        try {
          if (typeof gtag !== 'undefined' && data.ga_track) {
            gtag('event', 'form_success', {
              'event_category': 'contact_form',
              'event_label': 'successful_submission',
              'value': 1
            });
          }
        } catch (error) {
          console.log('GA success tracking error:', error);
        }
      }
    })
    .catch((error) => {
      responseDiv.innerHTML = `<div class="text-danger">${error.message}</div>`;
    })
    .finally(() => {
      submitButton.disabled = false;
      submitButton.innerHTML = "Submit";
    });
});
