function validateLogin() {
    const email = document.getElementById("email");
    const password = document.getElementById("password");

    const emailError = document.getElementById("email-error");
    const passwordError = document.getElementById("password-error");

    emailError.textContent = "";
    passwordError.textContent = "";

    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    const passwordRegex = /^(?=.*[A-Z])(?=.*[!@#$%^&*]).{6,}$/;

    let valid = true;

    if (!emailRegex.test(email.value.trim())) {
        emailError.textContent = "Email duhet të ketë @ dhe domain (.com)";
        valid = false;
    }

    if (!passwordRegex.test(password.value.trim())) {
        passwordError.textContent =
            "Fjalëkalimi duhet të ketë 1 shkronjë të madhe dhe 1 simbol";
        valid = false;
    }

    if (valid) {
        alert("Kyçja u krye me sukses ✅");
    }
}
