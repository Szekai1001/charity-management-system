
document.addEventListener('DOMContentLoaded', function () {
    console.log("✅ JS loaded and running");

    // Sidebar toggle
    // Select all sidebars, toggles, and arrows by class
    const sidebars = document.querySelectorAll(".sidebar");
    const sidebarToggles = document.querySelectorAll(".sidebarToggle");
    const arrowExpandeds = document.querySelectorAll(".arrow-expanded");

    // Add click listener to each toggle button
    sidebarToggles.forEach((toggle) => {
        toggle.addEventListener("click", function () {
            sidebars.forEach((sidebar) => {
                sidebar.classList.toggle("collapsed");
            });

            arrowExpandeds.forEach((arrow) => {
                // Show or hide arrow depending on collapsed state
                const isCollapsed = [...sidebars].some(s => s.classList.contains("collapsed"));
                if (isCollapsed) {
                    arrow.classList.remove("d-none");
                } else {
                    arrow.classList.add("d-none");
                }
            });
        });
    });

    // Add click listener to each expanded arrow
    arrowExpandeds.forEach((arrow) => {
        arrow.addEventListener("click", function () {
            sidebars.forEach((sidebar) => sidebar.classList.remove("collapsed"));
            arrowExpandeds.forEach((a) => a.classList.add("d-none"));
        });
    });



    document.addEventListener('change', function (e) {
        console.log("🔥 SCRIPT VERSION: 2.0 (New Code Loaded)");
        // ✅ Handle dynamically loaded student checkboxes
        if (e.target.classList.contains('select-student')) {
            console.log("✅ Student select-all triggered (via delegation)");

            let table = e.target.closest('table');
            if (!table) return;

            let checkboxes = table.querySelectorAll('input[name="student_ids[]"]');
            console.log("Student checkboxes found:", checkboxes.length);

            checkboxes.forEach(cb => cb.checked = e.target.checked);
        }

        // ✅ Handle dynamically loaded teacher checkboxes
        if (e.target.classList.contains('select-teacher')) {
            console.log("✅ Teacher select-all triggered (via delegation)");

            let table = e.target.closest('table');
            if (!table) return;

            let checkboxes = table.querySelectorAll('input[name="teacher_ids[]"]');
            console.log("Teacher checkboxes found:", checkboxes.length);

            checkboxes.forEach(cb => cb.checked = e.target.checked);
        }

        if (e.target.classList.contains('select-beneficiary')) {
            console.log("✅ Beneficiary select-all triggered (via delegation)");

            let table = e.target.closest('table');
            if (!table) {
                console.error("Could not find table for beneficiary!");
                return;
            }

            let checkboxes = table.querySelectorAll('input[name="beneficiary_ids[]"]');
            console.log("Beneficiary checkboxes found:", checkboxes.length);

            checkboxes.forEach(cb => cb.checked = e.target.checked);
        }
    });

    document.addEventListener("click", function (e) {
        if (e.target && e.target.id === "openAssignModal") {
            let checked = document.querySelectorAll(".student-checkbox:checked");
            if (checked.length === 0) {
                alert("Please select at least one student before assigning a teacher.");
                return;
            }

            const modal = new bootstrap.Modal(document.getElementById('assignTeacherModal'));
            modal.show();
        }
    });


    // ✅ Print QR Button
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.print-qr-btn');
        if (!btn) return; // Not a print button, ignore

        const qrId = btn.dataset.qrId; // Get QR code element ID
        const qrElement = document.getElementById(qrId);
        if (!qrElement) return;

        // Temporarily change ID for printing
        qrElement.setAttribute("id", "print-area");

        // Trigger print
        window.print();

        // Restore original ID
        qrElement.setAttribute("id", qrId);

        // Restore modal focus
        const modalEl = document.querySelector('.modal.show');
        const modalInstance = bootstrap.Modal.getInstance(modalEl);
        setTimeout(() => {
            if (modalInstance) {
                modalInstance._element.focus();
            }
        }, 300);
    });

});
