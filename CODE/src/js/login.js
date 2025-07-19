// Global Variables
let tempVar;

// Get Main Components
const signUpButton = document.getElementById('signUp');
const signInButton = document.getElementById('signIn');
const container = document.getElementById('container');
const customerContainer = document.getElementById('customerDiv');
const vendorContainer = document.getElementById('vendorDiv');

// Validation
function validatePanel(panel) {
    let flag = true;

    for (const child of panel.getElementsByTagName("*")) {
        if (child.tagName === "INPUT" && child.type !== "radio") {
            flag &= validateField(child);
        }
    }

    return flag;
}
function validateField(field) {
    const fieldType = field.type;

    switch (fieldType) {
        case "text":
            return validateText(field);
        case "email":
            return validateEmail(field);
        case "password":
            return validatePassword(field);
        case "tel":
            return validatePhone(field);
    }
}
function validateText(field) {
    if (field.value.trim() === "") {
        setError(field, "Field is required");
        return false;
    }
    setSuccess(field);
    return true;
}
function validateEmail(field) {
    const value = field.value.trim();

    if (value === "") {
        setError(field, 'Email is required');
        return false;
    }
    if (!isValidEmail(value)) {
        setError(field, 'Provide a valid email address');
        return false;
    }
    setSuccess(field);
    return true;
}
function validatePassword(field) {
    const value = field.value.trim();

    if (value === "") {
        setError(field, 'Password is required');
        return false;
    }
    if (value.length < 8) {
        setError(field, 'Password must be at least 8 characters.');
        return false;
    }
    if (field.id !== "conPassword") {
        tempVar = value;
        setSuccess(field);
        return true;
    }
    if (value !== tempVar) {
        setError(field, "Passwords don't match");
        return false;
    }
    setSuccess(field);
    return true;
}
function validatePhone(field) {
    const value = field.value.trim();

    if (value === "") {
        setError(field, 'Phone number is required');
        return false;
    }
    if (!isValidPhone(value)) {
        setError(field, 'Provide a valid 10-digit phone number');
        return false;
    }

    setSuccess(field);
    return true;
}

// Helper Functions
function setError(element, message) {
    const inputControl = element.parentElement;
    const errorDisplay = inputControl.querySelector('.error');
    errorDisplay.innerText = message;
    inputControl.classList.add('error');
    inputControl.classList.remove('success');
}
function setSuccess(element) {
    const inputControl = element.parentElement;
    const errorDisplay = inputControl.querySelector('.error');
    errorDisplay.innerText = '';
    inputControl.classList.add('success');
    inputControl.classList.remove('error');
}
function getActivePanel() {
    const signUpPanel = document.getElementById("sign-up-container").firstElementChild;
    const signInPanel = document.getElementById("sign-in-container").firstElementChild;

    return container.classList.contains("right-panel-active") ? signUpPanel : signInPanel;
}
function isValidEmail(email) {
    const re = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(String(email).toLowerCase());
}
function isValidPhone(phone) {
    const re = /^\+?([0-9]{1,3})?[-. ]?(\(?[0-9]{1,4}\)?)?[-. ]?([0-9]{1,4})[-. ]?([0-9]{1,4})[-. ]?([0-9]{1,9})$/;
    return re.test(phone);
}

// Dynamic Customer and Vendor Fields
let vendorFieldFill = `<div class="input-group">
                        <input id="companyName" type="text" placeholder="Company Name" name="companyName"/>
                        <div class="error"></div>
                    </div>
                    <div class="input-group">
                        <input id="companyAddress" type="text" placeholder="Company Address" name="companyAddress"/>
                        <div class="error"></div>
                    </div>`;
let customerFieldFill = `<div class="input-group">
                        <input id="customerAddress" type="text" placeholder="Address" name="customerAddress"/>
                        <div class="error"></div>
                    </div>
                    <div class="input-group">
                        <input id="customerPhone" type="tel" placeholder="Phone Number" name="customerPhone"/>
                        <div class="error"></div>
                    </div>`;
function toggleField() {
    const vendorRadio = document.getElementById('vendor');
    const customerRadio = document.getElementById('customer');

    if (customerRadio.checked) {
        vendorContainer.innerHTML = '';
        customerContainer.innerHTML = customerFieldFill;
    } else if (vendorRadio.checked) {
        customerContainer.innerHTML = '';
        vendorContainer.innerHTML = vendorFieldFill;
    }
}

// Submission
function submit(e) {
    const panel = getActivePanel();
    if (!validatePanel(panel)) e.preventDefault();
}

// Assign Listeners
signUpButton.addEventListener('click', () => {
    container.classList.add("right-panel-active");
});
signInButton.addEventListener('click', () => {
    container.classList.remove("right-panel-active");
});
const forms = document.getElementsByTagName('form');
for (const form of forms) {
    form.addEventListener('submit', submit);
}
document.getElementById('customer').addEventListener('change', toggleField);
document.getElementById('vendor').addEventListener('change', toggleField);

// Switch to Customer Panel Initially
toggleField();
