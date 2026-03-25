function validateSignup() {
    const fields = [
        { input: "username", error: "username-error", message: "Username është i detyrueshëm" },
        { input: "email", error: "email-error", message: "Email duhet të ketë @ dhe domain (.com)" },
        { input: "phone", error: "phone-error", message: "Numri duhet të ketë vetëm numra (8–15)" },
        { input: "password", error: "password-error", message: "Fjalëkalimi duhet të ketë 1 shkronjë të madhe dhe 1 simbol" },
        { input: "confirm-password", error: "confirm-password-error", message: "Fjalëkalimet nuk përputhen" }
    ];

    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    const phoneRegex = /^[0-9]{8,15}$/;
    const passwordRegex = /^(?=.*[A-Z])(?=.*[!@#$%^&*]).{6,}$/;

    let valid = true;

    fields.forEach(f => {
        document.getElementById(f.error).textContent = "";
        document.getElementById(f.input).classList.remove("error-input");
    });

    const username = document.getElementById("username");
    const email = document.getElementById("email");
    const phone = document.getElementById("phone");
    const password = document.getElementById("password");
    const confirmPassword = document.getElementById("confirm-password");

    if (username.value.trim() === "") {
        showError("username", "username-error", "Username është i detyrueshëm");
        valid = false;
    }

    if (!emailRegex.test(email.value)) {
        showError("email", "email-error", "Email duhet të ketë @ dhe domain (.com)");
        valid = false;
    }

    if (!phoneRegex.test(phone.value)) {
        showError("phone", "phone-error", "Numri duhet të ketë vetëm numra (8–15)");
        valid = false;
    }

    if (!passwordRegex.test(password.value)) {
        showError("password", "password-error",
            "Fjalëkalimi duhet të ketë 1 shkronjë të madhe dhe 1 simbol");
        valid = false;
    }

    if (password.value !== confirmPassword.value) {
        showError("confirm-password", "confirm-password-error", "Fjalëkalimet nuk përputhen");
        valid = false;
    }

    if (valid) {
        alert("Regjistrimi u krye me sukses ✅");
    }
}

function showError(inputId, errorId, message) {
    document.getElementById(errorId).textContent = message;
    document.getElementById(inputId).classList.add("error-input");
}
