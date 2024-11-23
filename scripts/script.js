function showToast(message, type = "success") {
  const toastContainer = document.getElementById("toast-container");
  const toastId = Date.now(); // Unique ID for each toast

  // Create toast element (using template literals for cleaner formatting)
  let toastHTML = `
    <div class="toast" role="alert" aria-live="assertive" aria-atomic="true" 
         data-toast-id="${toastId}" style="width: 300px; margin: 10px">
      <div class="toast-header text-${type} text-white">
        <strong class="mr-auto">${type}</strong>
        <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
          <span aria-hidden="true">×</span>
        </button>
      </div>
      <div class="toast-body">
        ${message}
      </div>
    </div>
  `;

  // Add the toast to the container (using createElement)
  const toastElement = document.createElement('div');
  toastElement.innerHTML = toastHTML;
  toastContainer.appendChild(toastElement); // Append to the container

  // Initialize and show the toast
  $(`[data-toast-id="${toastId}"]`).toast({
    autohide: true,
    delay: 5000,
  });
  $(`[data-toast-id="${toastId}"]`).toast("show");

  // Remove toast from DOM after it fades out
  $(`[data-toast-id="${toastId}"]`).on("hidden.bs.toast", function () {
    $(this).remove();
  });
}

function showLoadingButton(button) {
  button.innerHTML =
    '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...';
  button.disabled = true;
}

function resetButton(button) {
  button.innerHTML = button.dataset.originalText;
  button.disabled = false;
}

// Function to display flash messages (reusing the showToast function)
function displayFlashMessage() {
    if (typeof window.sessionStorage !== 'undefined') { 
        let flashMessage = sessionStorage.getItem('flash_message');

        if (flashMessage) {
            console.log(flashMessage);
            flashMessage = JSON.parse(flashMessage);
            showToast(flashMessage.message, flashMessage.type);
            sessionStorage.removeItem('flash_message'); // Remove after display
        }
    }
}

// Call the function on page load to display the message
displayFlashMessage();