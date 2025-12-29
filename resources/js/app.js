
// --- 1. File Validation Functions (Global Scope) ---
const allowedTypes = ['application/pdf', 'image/jpeg', 'image/png'];

function validateFile(input) {
    const file = input.files[0];
    if (!file) return;

    if (!allowedTypes.includes(file.type)) {
        alert(`${file.name} is invalid. Allowed: PDF, JPG, JPEG, PNG`);
        input.value = "";
        return;
    }

    if (file.size > 2 * 1024 * 1024) {
        alert('File size must be less than 2MB');
        input.value = "";
    }
}

function attachFileValidation(container) {
    if (!container) return;
    container.querySelectorAll("input[type='file']").forEach(input => {
        // Remove old listeners to avoid duplicates, then add new one
        input.removeEventListener("change", () => validateFile(input));
        input.addEventListener("change", () => validateFile(input));
    });
}

// --- 2. Main DOM Logic ---
document.addEventListener('DOMContentLoaded', function () {

    const form = document.querySelector("form[data-multi-step]") || document.querySelector("form");
    const sections = form.querySelectorAll("[id^='page']");
    let currentPage = 1;
    const totalPages = sections.length;

    // --- Helper: Toggle "Other" Input Visibility ---
    // 'shouldClear' = true ONLY when user interacts (change event).
    // 'shouldClear' = false when page loads (preserves Laravel old values).
    function toggleOtherInput(triggerElement, shouldClear = false) {
        const container = triggerElement.closest('[class^="col-"]');
        if (!container) return;

        const otherContainer = container.querySelector('.other-container');
        if (!otherContainer) return;

        const inputField = otherContainer.querySelector('input');
        if (!inputField) return;

        // Determine if "Other" (or "Yes") is selected
        let isSelected = false;
        if (triggerElement.tagName === 'SELECT') {
            isSelected = triggerElement.value === 'Other';
        } else if (triggerElement.type === 'radio') {
            isSelected = triggerElement.checked && triggerElement.value === 'yes';
        }

        if (isSelected) {
            otherContainer.classList.remove('d-none');
            inputField.disabled = false;
            inputField.required = true; // Make it required
        } else {
            otherContainer.classList.add('d-none');
            inputField.disabled = true; // Disable so it doesn't submit
            inputField.required = false; // Remove required so form doesn't block

            // Only clear the value if the USER changed it.
            // If page is just loading, we keep the old value.
            if (shouldClear) {
                inputField.value = '';
            }
        }
    }

    // --- Helper: Show/Hide Pages ---
    function showPage(page) {
        sections.forEach((section, index) => {
            section.style.display = (index + 1 === page) ? "block" : "none";
        });
    }

    // --- Helper: Navigation Validation ---
    function validateStep(sectionId) {
        const currentSection = document.getElementById(sectionId);
        const requiredInputs = currentSection.querySelectorAll('[required]');
        let stepValid = true;

        requiredInputs.forEach(input => {
            // Skip hidden/disabled inputs
            if (input.offsetParent === null || input.disabled) return;

            let isEmpty = false;

            if (input.type === 'radio' || input.type === 'checkbox') {
                const group = currentSection.querySelectorAll(`input[name="${input.name}"]:checked`);
                if (group.length === 0) isEmpty = true;
            } else if (input.tagName === 'SELECT') {
                if (!input.value) isEmpty = true;
            } else {
                if (!input.value.trim()) isEmpty = true;
            }

            if (isEmpty) {
                stepValid = false;
                input.classList.add('is-invalid'); // Use Bootstrap class
            } else {
                input.classList.remove('is-invalid');
            }
        });
        return stepValid;
    }

    // --- Navigation Functions ---
    function nextPage() {
        if (currentPage < totalPages) {
            if (!validateStep('page' + currentPage)) {
                alert("Please fill in all required fields.");
                return;
            }
            currentPage++;
            showPage(currentPage);
            window.scrollTo(0, 0); // Scroll to top
        }
    }

    function prevPage() {
        if (currentPage > 1) {
            currentPage--;
            showPage(currentPage);
            window.scrollTo(0, 0);
        }
    }

    // --- Dynamic Row Functions ---
    function addOption(button) {
        const container = button.closest('.extra-option');
        const original = container.querySelector(".extra-options"); // This usually grabs the first one

        // 1. Calculate the New Index
        // We count how many rows currently exist to determine the next number
        const allRows = container.querySelectorAll(".extra-options");
        const newIndex = allRows.length; // If there are 2 rows (0, 1), new index should be 2.

        // 2. Deep clone
        const clone = original.cloneNode(true);

        // 3. Clean up inputs AND update 'name' attributes
        clone.querySelectorAll("input, select").forEach(input => {
            // Reset Values
            if (input.tagName === 'SELECT') input.selectedIndex = 0;
            else input.value = '';

            // Reset Validation
            input.classList.remove('is-invalid');

            // --- CRITICAL FIX START ---
            // Update the 'name' attribute to use the new index
            // Example: changes "memberName[0]" to "memberName[2]"
            let name = input.getAttribute('name');
            if (name) {
                // This regex replaces the number inside [ ] with the newIndex
                const newName = name.replace(/\[\d+\]/, `[${newIndex}]`);
                input.setAttribute('name', newName);
            }

            // Update 'id' if it exists (to keep labels working)
            let id = input.getAttribute('id');
            if (id) {
                // Assumes IDs look like "memberName_0"
                const newId = id.replace(/_\d+$/, `_${newIndex}`);
                input.setAttribute('id', newId);

                // Also find the label for this input and update its 'for' attribute
                const label = clone.querySelector(`label[for="${id}"]`);
                if (label) {
                    label.setAttribute('for', newId);
                }
            }
            // --- CRITICAL FIX END ---

            // Reset state
            input.required = false;
            input.disabled = false;
        });

        // Handle the "Other" container inside the clone
        const otherContainer = clone.querySelector('.other-container');
        if (otherContainer) {
            otherContainer.classList.add('d-none');
            // Ensure the ID of the container doesn't conflict if you used IDs there
            const otherInput = otherContainer.querySelector('input');
            if (otherInput) {
                otherInput.required = false;
                otherInput.disabled = true;
            }
        }

        // Insert into DOM
        const addButton = container.querySelector('.insert-before');
        container.insertBefore(clone, addButton);

        // Re-attach file validation listeners if needed
        if (typeof attachFileValidation === "function") {
            attachFileValidation(clone);
        }
    }
    function removeOption(button) {
        const container = button.closest('.extra-option');
        const cards = container.querySelectorAll(".extra-options");
        if (cards.length > 1) {
            button.closest(".extra-options").remove();
        } else {
            alert("At least one item is required.");
        }
    }

    // --- Initialization ---

    // 1. Initialize Page View
    // Auto-jump to error page if Laravel returned errors
    const firstError = document.querySelector('.is-invalid');
    if (firstError) {
        const errorPage = firstError.closest('[id^="page"]');
        if (errorPage) {
            currentPage = parseInt(errorPage.id.replace('page', ''));
        }
    }
    showPage(currentPage);

    // 2. Initialize "Other" Fields
    // Run logic for ALL trigger fields (selects/radios) to show/hide text boxes correctly.
    // pass 'false' to preserve old() values.
    document.querySelectorAll('.other').forEach(el => toggleOtherInput(el, false));

    // 3. Initialize File Validation
    attachFileValidation(document);

    // --- Event Delegation (One listener for everything) ---
    document.addEventListener('click', function (e) {
        if (e.target.closest('.insert-before')) {
            addOption(e.target.closest('.insert-before'));
        } else if (e.target.closest('.next-page')) {
            nextPage();
        } else if (e.target.closest('.prev-page')) {
            prevPage();
        } else if (e.target.closest('.remove-btn')) {
            removeOption(e.target.closest('.remove-btn'));
        }
    });

    document.addEventListener('change', function (e) {
        // Handle "Other" toggles
        if (e.target.classList.contains('other')) {
            // Pass 'true' because user changed it manually
            toggleOtherInput(e.target, true);
        }
    });

});
