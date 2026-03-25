document.getElementById("terminForm").addEventListener("submit", function (e) {
    e.preventDefault(); 

    const emri = document.getElementById("emri");
    const telefoni = document.getElementById("telefoni");
    const ora = document.getElementById("ora");
    const arsyeja = document.getElementById("arsyeja");

    const emriError = document.getElementById("emri-error");
    const telefoniError = document.getElementById("telefoni-error");
    const oraError = document.getElementById("ora-error");
    const arsyejaError = document.getElementById("arsyeja-error");

   
    emriError.textContent = "";
    telefoniError.textContent = "";
    oraError.textContent = "";
    arsyejaError.textContent = "";

    let valid = true;

   
    if (emri.value.trim().length < 4) {
        emriError.textContent = "Emri duhet të ketë minimum 4 shkronja";
        valid = false;
    }

    
    const phoneRegex = /^\+383\d{8}$/;
    if (!phoneRegex.test(telefoni.value.trim())) {
        telefoniError.textContent =
            "Numri duhet të fillojë me +383 dhe të ketë 8 numra";
        valid = false;
    }

  
    if (ora.value === "") {
        oraError.textContent = "Zgjidh orën e terminit";
        valid = false;
    }

    if (arsyeja.value.trim().length < 10) {
        arsyejaError.textContent =
            "Arsyeja duhet të ketë minimum 10 shkronja";
        valid = false;
    }

    if (valid) {
        alert("Termini u rezervua me sukses ✅");
      
    }
});
