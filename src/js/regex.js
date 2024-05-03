function showError(inputElement, message) {
    let errorMessageDiv = document.getElementById(inputElement.id + '-error') ||
        document.createElement('div');
    errorMessageDiv.id = inputElement.id + '-error';
    inputElement.parentNode.appendChild(errorMessageDiv);
    errorMessageDiv.textContent = message;
    errorMessageDiv.className = 'text-danger';
    inputElement.classList.add('is-invalid');
    inputElement.classList.remove('is-valid');
}

function showSuccess(inputElement) {
    let errorMessageDiv = document.getElementById(inputElement.id + '-error');
    if (errorMessageDiv) {
        errorMessageDiv.textContent = '';
        errorMessageDiv.className = 'text-success';
    }
    inputElement.classList.remove('is-invalid');
    inputElement.classList.add('is-valid');
}

function validateInput(inputElement, regex, emptyMessage, invalidMessage) {
    let input = inputElement.value.trim();
    if (input === '') {
        showError(inputElement, emptyMessage);
        return false;
    }
    if (!regex.test(input)) {
        showError(inputElement, invalidMessage);
        return false;
    } else {
        showSuccess(inputElement);
        return true;
    }
}

function isValidPassword(inputElement) {
    const regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)\w{8,}$/;
    return validateInput(
        inputElement,
        regex,
        'Password cannot be empty.',
        'Password must contain at least one uppercase letter, one lowercase letter, and one number.'
    );
}

function isValidInput(inputElement) {
    const regex = /^[\p{L}\d\s,']+$/u;
    return validateInput(
        inputElement,
        regex,
        'Input cannot be empty.',
        'Please enter a valid string.'
    );
}

function checkForm(e) {
    e.preventDefault();
    let formIsValid = true;
    const inputs = e.target.querySelectorAll('input');
    inputs.forEach(input => {
        if (!input.value.trim() || input.classList.contains('is-invalid')) {
            formIsValid = false;
        }
    });
    if (formIsValid) {
        e.target.submit();
    } else {
        toastr.error('Please fill in the form correctly.');
    }
}
