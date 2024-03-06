function checkAllNumbers(input) {
    let expression = /^[0-9]+$/;
    let value = input.value.trim();
    let isPhoneNumber = input.getAttribute('name') === 'phone-number';

    if (value !== "" && expression.test(value) && (!isPhoneNumber || (isPhoneNumber && (value.length >= 10 && value.length <=13)))) {
        input.classList.remove('error');
        removeErrorMessage(input);
    } else {
        input.classList.add('error');
        if (!input.parentNode.querySelector('.error-message')) {
            if (isPhoneNumber) {
                createErrorMessage(input, 'Not a valid phone number (should be 10-13 digits)');
            } else {
                createErrorMessage(input, 'Not a valid number');
            }
        }
    }
}

function checkAllLetters(input) {
    let expression = /^[a-zA-ZÀ-ÿ\s]+$/;
    let value = input.value.trim();

    if (value !== "" && expression.test(value)) {
        input.classList.remove('error');
        removeErrorMessage(input);
    } else {
        input.classList.add('error');
        if (!input.parentNode.querySelector('.error-message')) {
            createErrorMessage(input, 'Not valid');
        }
    }
}

function checkBirthDate(input, dateString) {
    var parts = dateString.split("-");
    var year = parseInt(parts[0], 10);

    var isValid = true;

    if (year < 1900) {
        isValid = false;
    } else {
        var eighteenYearsAgo = new Date();
        eighteenYearsAgo.setFullYear(eighteenYearsAgo.getFullYear() - 18);

        if (year > eighteenYearsAgo.getFullYear()) {
            isValid = false;
        }
    }

    if (!isValid) {
        input.classList.add('error');
        if (!input.parentNode.querySelector('.error-message')) {
            createErrorMessage(input, 'You must be 18');
        }
    } else {
        input.classList.remove('error');
        removeErrorMessage(input);
    }
}

function createErrorMessage(input, message) {
    const errorMessage = document.createElement('div');
    errorMessage.classList.add('error-message');
    errorMessage.textContent = message;
    input.parentNode.insertBefore(errorMessage, input.nextSibling);

    document.getElementById("submit").disabled = true;
}

function removeErrorMessage(input) {
    const errorMessage = input.parentNode.querySelector('.error-message');
    if (errorMessage) {
        errorMessage.remove();

        document.getElementById("submit").disabled = false;
    }
}

function toggleInputsVisibility(event) {
    event.preventDefault();
    var form = document.getElementById("signupForm");
    var inputs = form.querySelectorAll(".input-box");
    var submitButton = document.getElementById("submit");
    var isValid = true;

    inputs.forEach(function(input) {
        var inputField = input.querySelector("input");
        if (input.classList.contains("hidden")) {
            inputField.removeAttribute("required");
            return;
        }
        
        if (inputField.value.trim() === "") {
            isValid = false;
            return;
        }
        if (inputField.classList.contains("error")) {
            isValid = false;
        }
    });

    if (isValid) {
        inputs.forEach(function(input) {
            input.classList.toggle("hidden");
            var inputField = input.querySelector("input");
            inputField.setAttribute("required", "required");
        });

        submitButton.textContent = "Sign up";
        submitButton.removeAttribute("onclick");
        submitButton.setAttribute("type", "submit");
    } else {
        alert("Please fill in all fields correctly.");
    }
}

function checkPasswordMatch(input) {
    var password = document.getElementById("password").value.trim();
    var confirmPassword = document.getElementById("confirm-password").value.trim();

    if (password !== confirmPassword) {
        input.classList.add('error');
        createErrorMessage(input, 'Passwords do not match');
    } else {
        input.classList.remove('error');
        removeErrorMessage(input);
    }
}