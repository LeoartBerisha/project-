function validateContact() {
    const name = document.getElementById("name");
    const email = document.getElementById("email");
    const subject = document.getElementById("subject");
    const message = document.getElementById("message");

    const nameError = document.getElementById("name-error");
    const emailError = document.getElementById("email-error");
    const subjectError = document.getElementById("subject-error");
    const messageError = document.getElementById("message-error");

    nameError.textContent = "";
    emailError.textContent = "";
    subjectError.textContent = "";
    messageError.textContent = "";

    name.classList.remove("error-input");
    email.classList.remove("error-input");
    subject.classList.remove("error-input");
    message.classList.remove("error-input");

    let valid = true;

    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if (name.value.trim() === "") {
        showError(name, nameError, "Emri është i detyrueshëm");
        valid = false;
    }

    if (!emailRegex.test(email.value.trim())) {
        showError(email, emailError, "Email duhet të ketë @ dhe domain");
        valid = false;
    }

    if (subject.value.trim() === "") {
        showError(subject, subjectError, "Subjekti është i detyrueshëm");
        valid = false;
    }

    if (message.value.trim().length < 10) {
        showError(message, messageError, "Mesazhi duhet të ketë së paku 10 karaktere");
        valid = false;
    }

    if (valid) {
        alert("Mesazhi u dërgua me sukses ✅");

        name.value = "";
        email.value = "";
        subject.value = "";
        message.value = "";
    }
}

function showError(input, errorElement, message) {
    errorElement.textContent = message;
    input.classList.add("error-input");
}
