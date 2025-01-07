document.addEventListener("DOMContentLoaded", function () {
    // Select all breadcrumb buttons and headers
    const breadcrumbButtons = document.querySelectorAll(".breadcrumb-button");
    const breadcrumbHeaders = document.querySelectorAll(".breadcrumb-header");
    const breadcrumbCloses = document.querySelectorAll(".breadcrumb-close");

    // Function to open the breadcrumb header
    breadcrumbButtons.forEach((button, index) => {
        button.addEventListener("click", () => {
            const breadcrumbHeader = breadcrumbHeaders[index];
            breadcrumbHeader.classList.remove("hidden", "translate-x-full");
            breadcrumbHeader.classList.add("translate-x-0");
        });
    });

    // Function to close the breadcrumb header
    function closeBreadcrumb(breadcrumbHeader) {
        breadcrumbHeader.classList.add("translate-x-full");
        breadcrumbHeader.classList.remove("translate-x-0");
        setTimeout(() => breadcrumbHeader.classList.add("hidden"), 300); // Wait for animation to finish
    }

    // Handle close button click
    breadcrumbCloses.forEach((close, index) => {
        close.addEventListener("click", () => {
            closeBreadcrumb(breadcrumbHeaders[index]);
        });
    });

    // Handle click outside the breadcrumb header
    document.addEventListener("click", (event) => {
        breadcrumbHeaders.forEach((breadcrumbHeader, index) => {
            const isClickInside = breadcrumbHeader.contains(event.target) || breadcrumbButtons[index].contains(event.target);
            if (!isClickInside) {
                closeBreadcrumb(breadcrumbHeader);
            }
        });
    });
});
