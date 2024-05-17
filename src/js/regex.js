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

function removeSuccess(success) {
    let inputElement=document.getElementById(success);
    let errorMessageDiv = document.getElementById(inputElement.id + '-error');
    if (errorMessageDiv) {
        errorMessageDiv.textContent = '';
        errorMessageDiv.className = '';
    }
    inputElement.classList.remove('is-valid');
}

function removeError(remove) {
    let inputElement=document.getElementById(remove);
    let errorMessageDiv = document.getElementById(inputElement.id + '-error');
    if (errorMessageDiv) {
        errorMessageDiv.textContent = '';
        errorMessageDiv.className = '';
    }
    inputElement.classList.remove('is-invalid');

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

function isValidText(inputElement) {
    const regex = /^\p{Lu}[\p{L}\d\s,']*$/u;
    return validateInput(
        inputElement,
        regex,
        'Input cannot be empty.',
        'Please enter a valid string beginning with an Uppercase letter.'
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

function isValidQuestion(inputElement) {
    const regex = /^\p{Lu}[\p{L}\d\s,'?]*\?$/u;
    return validateInput(
        inputElement,
        regex,
        'Question cannot be empty.',
        'Please enter a valid question ending with "?".'
    );

}

function isValidKey(inputElement) {
    const regex = /^[a-zA-Z\d]{5}$/;
    return validateInput(
        inputElement,
        regex,
        'Key cannot be empty.',
        'Key must be exactly 5 characters long.'
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
        return false;
    }
}

function checkFormAdd() {
    let formIsValid = true;
    let type = document.getElementById('questionType');
    let question = document.getElementById('questionText');
    let category = document.getElementById('questionCategory');
    if (!isValidQuestion(question)) {
        formIsValid = false;
    }
    if (!isValidText(category)) {
        formIsValid = false;
    }
    if (type.value === '2') {
        let a = document.getElementById('optionA');
        let b = document.getElementById('optionB');
        let c = document.getElementById('optionC');
        if (!isValidText(a)) {
            formIsValid = false;
        }
        if (!isValidText(b)) {
            formIsValid = false;
        }
        if (!isValidText(c)) {
            formIsValid = false;
        }
        let aSwitch = document.getElementById('optionASwitch');
        let bSwitch = document.getElementById('optionBSwitch');
        let cSwitch = document.getElementById('optionCSwitch');
        if (!aSwitch.checked && !bSwitch.checked && !cSwitch.checked) {
            toastr.error('Please select the correct answer.');
            return false;
        }
    }

    if (!formIsValid) {
        toastr.error('Please fill in the form correctly.');
        return false;
    }
    return true;
}

function checkFormChange() {
    let formIsValid = true;
    let type = document.getElementById('changeQuestionType');
    let question = document.getElementById('changeQuestionText');
    let category = document.getElementById('changeQuestionCategory');
    if (!isValidQuestion(question)) {
        formIsValid = false;
    }
    if (!isValidText(category)) {
        formIsValid = false;
    }
    if (type.value === '2') {
        let a = document.getElementById('changeOptionA');
        let b = document.getElementById('changeOptionB');
        let c = document.getElementById('changeOptionC');
        if (!isValidText(a)) {
            formIsValid = false;
        }
        if (!isValidText(b)) {
            formIsValid = false;
        }
        if (!isValidText(c)) {
            formIsValid = false;
        }
        let aSwitch = document.getElementById('changeOptionASwitch');
        let bSwitch = document.getElementById('changeOptionBSwitch');
        let cSwitch = document.getElementById('changeOptionCSwitch');
        if (!aSwitch.checked && !bSwitch.checked && !cSwitch.checked) {
            toastr.error('Please select the correct answer.');
            return false;
        }
    }

    if (!formIsValid) {
        toastr.error('Please fill in the form correctly.');
        return false;
    }
    return true;
}

function checkChangeUser() {
    let formIsValid = true;
    let username = document.getElementById('changeUsername');
    let password = document.getElementById('changeUserPassword');
    if (!isValidInput(username)) {
        formIsValid = false;
    }
    if (!isValidPassword(password)) {
        formIsValid = false;
    }
    if (!formIsValid) {
        toastr.error('Please fill in the form correctly.');
        return false;
    }
    return true;
}

function checkAddUser() {
    let formIsValid = true;
    let username = document.getElementById('username');
    let password = document.getElementById('userPassword');
    if (!isValidInput(username)) {
        formIsValid = false;
    }
    if (!isValidPassword(password)) {
        formIsValid = false;
    }
    if (!formIsValid) {
        toastr.error('Please fill in the form correctly.');
        return false;
    }
    return true;
}

