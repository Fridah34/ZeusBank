//admin_dashboard page
// JavaScript for Delete Confirmation
document.addEventListener('DOMContentLoaded', function () {
    // Handle the confirmation for delete actions
    const deleteButtons = document.querySelectorAll('button[type="submit"]');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function (e) {
            const confirmation = confirm('Are you sure you want to delete this customer?');
            if (!confirmation) {
                e.preventDefault(); // Prevent form submission if not confirmed
            }
        });
    });

    // Optional: Highlight rows on hover for better user experience
    const tableRows = document.querySelectorAll('table tbody tr');
    tableRows.forEach(row => {
        row.addEventListener('mouseenter', function() {
            row.style.backgroundColor = '#f0f0f0';
        });
        row.addEventListener('mouseleave', function() {
            row.style.backgroundColor = '';
        });
    });
});

//create_account page
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const accountTypeSelect = document.getElementById('account_type');
    const accountBalanceInput = document.getElementById('account_balance');
    const errorMessageDiv = document.querySelector('.error');
    const successMessageDiv = document.querySelector('.success');

    // Hide messages initially
    if (errorMessageDiv) {
        errorMessageDiv.style.display = 'none';
    }
    if (successMessageDiv) {
        successMessageDiv.style.display = 'none';
    }

    // Form submit event listener
    form.addEventListener('submit', function(event) {
        // Clear previous error message
        if (errorMessageDiv) {
            errorMessageDiv.style.display = 'none';
        }

        // Check if account type and initial balance are valid
        const accountType = accountTypeSelect.value;
        const accountBalance = accountBalanceInput.value;

        if (!accountType || !accountBalance || isNaN(accountBalance) || accountBalance <= 0) {
            event.preventDefault(); // Prevent form submission
            if (errorMessageDiv) {
                errorMessageDiv.textContent = "Please select a valid account type and provide a valid initial deposit.";
                errorMessageDiv.style.display = 'block';
            }
        } else {
            if (successMessageDiv) {
                successMessageDiv.style.display = 'none'; // Hide success message if re-submitting
            }
        }
    });

    // Optional: Real-time validation feedback for account balance input
    accountBalanceInput.addEventListener('input', function() {
        if (accountBalanceInput.value <= 0) {
            accountBalanceInput.setCustomValidity('Initial deposit must be greater than 0');
        } else {
            accountBalanceInput.setCustomValidity('');
        }
    });
});

//customer_dashboard page
document.addEventListener('DOMContentLoaded', function () {
    // Handle the logout confirmation
    const logoutButton = document.querySelector('.card a[href="logout.php"]');
    if (logoutButton) {
        logoutButton.addEventListener('click', function (e) {
            const confirmation = confirm('Are you sure you want to log out?');
            if (!confirmation) {
                e.preventDefault(); // Prevent the logout if the user cancels
            }
        });
    }

    // Add hover effect on cards for better user interaction
    const cards = document.querySelectorAll('.card');
    cards.forEach(card => {
        card.addEventListener('mouseenter', function () {
            card.style.transform = 'scale(1.05)';
            card.style.transition = 'transform 0.3s ease';
        });
        card.addEventListener('mouseleave', function () {
            card.style.transform = 'scale(1)';
        });
    });
});

//transaction_history
document.addEventListener('DOMContentLoaded', function () {
    // Handle the logout confirmation
    const logoutButton = document.querySelector('a[href="logout.php"]');
    if (logoutButton) {
        logoutButton.addEventListener('click', function (e) {
            const confirmation = confirm('Are you sure you want to log out?');
            if (!confirmation) {
                e.preventDefault(); // Prevent the logout if the user cancels
            }
        });
    }

    // Function to sort the table by a specific column
    function sortTable(n) {
        const table = document.querySelector('table');
        const rows = Array.from(table.rows).slice(1); // Skip the header row
        const isAscending = table.getAttribute('data-sort-direction') === 'asc';

        rows.sort((rowA, rowB) => {
            const cellA = rowA.cells[n].innerText;
            const cellB = rowB.cells[n].innerText;

            let comparison = 0;
            if (n === 4) { // Amount column: sort by numeric value
                comparison = parseFloat(cellA.replace('$', '').replace(',', '')) - parseFloat(cellB.replace('$', '').replace(',', ''));
            } else if (n === 5) { // Transaction Date column: sort by date
                comparison = new Date(cellA) - new Date(cellB);
            } else {
                comparison = cellA.localeCompare(cellB);
            }

            return isAscending ? comparison : -comparison;
        });

        // Clear the table and append the sorted rows
        rows.forEach(row => table.appendChild(row));
        table.setAttribute('data-sort-direction', isAscending ? 'desc' : 'asc');
    }

    // Add event listeners for each table header to enable sorting
    const headers = document.querySelectorAll('th');
    headers.forEach((header, index) => {
        header.addEventListener('click', () => sortTable(index));
    });
});

//transfer_funds page
document.addEventListener('DOMContentLoaded', function () {
    const transferForm = document.querySelector('form');
    const senderAccountSelect = document.querySelector('#sender_account');
    const recipientAccountInput = document.querySelector('#recipient_account');
    const transferAmountInput = document.querySelector('#transfer_amount');
    const errorDiv = document.querySelector('.error');
    const successDiv = document.querySelector('.success');

    // Clear previous success or error messages
    function clearMessages() {
        if (errorDiv) errorDiv.textContent = '';
        if (successDiv) successDiv.textContent = '';
    }

    // Validate form inputs
    function validateForm() {
        clearMessages(); // Clear any previous messages

        const senderAccount = senderAccountSelect.value.trim();
        const recipientAccount = recipientAccountInput.value.trim();
        const transferAmount = parseFloat(transferAmountInput.value.trim());

        if (!senderAccount || !recipientAccount || !transferAmount) {
            return "All fields are required.";
        }

        if (isNaN(transferAmount) || transferAmount <= 0) {
            return "Please enter a valid transfer amount.";
        }

        if (senderAccount === recipientAccount) {
            return "You cannot transfer funds to the same account.";
        }

        return null; // All validations passed
    }

    // Listen for form submission
    transferForm.addEventListener('submit', function (e) {
        const errorMessage = validateForm();

        if (errorMessage) {
            // Prevent form submission if validation fails
            e.preventDefault();

            // Display the error message
            if (errorDiv) errorDiv.textContent = errorMessage;
        } else {
            // Optionally, show a confirmation before submitting
            const confirmation = confirm('Are you sure you want to transfer the funds?');
            if (!confirmation) {
                e.preventDefault();
            }
        }
    });

    // Add event listeners to form inputs for real-time validation (optional)
    senderAccountSelect.addEventListener('change', clearMessages);
    recipientAccountInput.addEventListener('input', clearMessages);
    transferAmountInput.addEventListener('input', clearMessages);
});

//view_account page
document.addEventListener('DOMContentLoaded', function () {
    const tableRows = document.querySelectorAll('.view-account-table tbody tr');
    const logoutLink = document.querySelector('a[href="logout.php"]');
    const backLink = document.querySelector('a[href="customer_dashboard.php"]');
    
    // Highlight table rows on hover
    tableRows.forEach(row => {
        row.addEventListener('mouseover', function() {
            row.style.backgroundColor = '#f1f1f1';
        });
        row.addEventListener('mouseout', function() {
            row.style.backgroundColor = '';
        });
    });

    // Confirmation before logout
    if (logoutLink) {
        logoutLink.addEventListener('click', function (e) {
            const confirmation = confirm("Are you sure you want to log out?");
            if (!confirmation) {
                e.preventDefault();
            }
        });
    }

    // Confirmation before going back to the dashboard
    if (backLink) {
        backLink.addEventListener('click', function (e) {
            const confirmation = confirm("Are you sure you want to go back to the dashboard?");
            if (!confirmation) {
                e.preventDefault();
            }
        });
    }
});

//index page
document.addEventListener('DOMContentLoaded', function () {

    // Smooth scrolling when clicking on buttons or links
    const scrollToLinks = document.querySelectorAll('a[href^="#"]');
    scrollToLinks.forEach(link => {
        link.addEventListener('click', function (event) {
            event.preventDefault();

            const targetId = this.getAttribute('href').substring(1);
            const targetElement = document.getElementById(targetId);

            if (targetElement) {
                window.scrollTo({
                    top: targetElement.offsetTop - 60, // Adjust for header height if needed
                    behavior: 'smooth'
                });
            }
        });
    });

    // Button hover effect for primary button
    const primaryButton = document.querySelector('.zb-btn-primary');
    if (primaryButton) {
        primaryButton.addEventListener('mouseover', function () {
            primaryButton.style.backgroundColor = '#0056b3'; // Change color on hover
            primaryButton.style.transition = 'background-color 0.3s ease';
        });

        primaryButton.addEventListener('mouseout', function () {
            primaryButton.style.backgroundColor = '#007bff'; // Revert color
        });
    }

    // Toggling navigation for mobile responsiveness (Optional)
    const navToggleButton = document.querySelector('.zb-nav-toggle');
    const navMenu = document.querySelector('.zb-nav-list');
    if (navToggleButton && navMenu) {
        navToggleButton.addEventListener('click', function () {
            navMenu.classList.toggle('active');
        });
    }

});

//login page
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('.zb-login-form');
    const usernameInput = document.querySelector('#username');
    const passwordInput = document.querySelector('#password');
    const submitButton = document.querySelector('.zb-form-submit');
    
    // Function to show error message
    function showError(message) {
        const errorMessageElement = document.createElement('p');
        errorMessageElement.classList.add('zb-error-message');
        errorMessageElement.textContent = message;
        form.insertBefore(errorMessageElement, submitButton);
    }

    // Input field focus and blur effects for better UX
    usernameInput.addEventListener('focus', function() {
        this.style.borderColor = '#007bff'; // Blue border when focused
    });

    usernameInput.addEventListener('blur', function() {
        this.style.borderColor = ''; // Reset border color on blur
    });

    passwordInput.addEventListener('focus', function() {
        this.style.borderColor = '#007bff'; // Blue border when focused
    });

    passwordInput.addEventListener('blur', function() {
        this.style.borderColor = ''; // Reset border color on blur
    });

    // Form validation before submission
    form.addEventListener('submit', function(event) {
        let isValid = true;
        // Remove any previous error messages
        const existingErrorMessages = form.querySelectorAll('.zb-error-message');
        existingErrorMessages.forEach(message => message.remove());

        // Check if both fields are filled
        if (!usernameInput.value.trim()) {
            isValid = false;
            showError('Please enter your username.');
        }
        if (!passwordInput.value.trim()) {
            isValid = false;
            showError('Please enter your password.');
        }

        if (!isValid) {
            event.preventDefault(); // Prevent form submission if validation fails
        }
    });
});

//register page
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('.zb-register-form');
    const fullnameInput = document.querySelector('input[name="full_name"]');
    const usernameInput = document.querySelector('input[name="username"]');
    const emailInput = document.querySelector('input[name="email"]');
    const passwordInput = document.querySelector('input[name="password"]');
    const confirmPasswordInput = document.querySelector('input[name="cpass"]');
    const phoneInput = document.querySelector('input[name="phone_number"]');
    const addressInput = document.querySelector('input[name="address"]');
    const submitButton = document.querySelector('.zb-submit-btn');
    const errorMessages = [];

    // Function to show error message
    function showError(message) {
        const errorMessageElement = document.createElement('p');
        errorMessageElement.classList.add('zb-error-message');
        errorMessageElement.textContent = message;
        form.insertBefore(errorMessageElement, submitButton);
    }

    // Input field focus and blur effects for better UX
    fullnameInput.addEventListener('focus', function() {
        this.style.borderColor = '#007bff'; // Blue border when focused
    });
    fullnameInput.addEventListener('blur', function() {
        this.style.borderColor = ''; // Reset border color on blur
    });

    // Validate email format
    emailInput.addEventListener('blur', function() {
        const email = this.value.trim();
        if (!email || !validateEmail(email)) {
            showError('Invalid email format!');
        }
    });

    // Validate password match
    confirmPasswordInput.addEventListener('blur', function() {
        const password = passwordInput.value.trim();
        const confirmPassword = this.value.trim();
        if (confirmPassword !== password) {
            showError('Passwords do not match!');
        }
    });

    // Function to validate email format using regex
    function validateEmail(email) {
        const regex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;
        return regex.test(email);
    }

    // Form validation before submission
    form.addEventListener('submit', function(event) {
        let isValid = true;

        // Remove any previous error messages
        const existingErrorMessages = form.querySelectorAll('.zb-error-message');
        existingErrorMessages.forEach(message => message.remove());

        // Check if all fields are filled
        if (!fullnameInput.value.trim()) {
            isValid = false;
            showError('Please enter your full name.');
        }
        if (!usernameInput.value.trim()) {
            isValid = false;
            showError('Please enter your username.');
        }
        if (!emailInput.value.trim()) {
            isValid = false;
            showError('Please enter your email.');
        }
        if (!passwordInput.value.trim()) {
            isValid = false;
            showError('Please enter your password.');
        }
        if (!confirmPasswordInput.value.trim()) {
            isValid = false;
            showError('Please confirm your password.');
        }
        if (!phoneInput.value.trim()) {
            isValid = false;
            showError('Please enter your phone number.');
        }
        if (!addressInput.value.trim()) {
            isValid = false;
            showError('Please enter your address.');
        }

        // If validation fails, prevent form submission
        if (!isValid) {
            event.preventDefault(); // Prevent form submission if validation fails
        }
    });
});




