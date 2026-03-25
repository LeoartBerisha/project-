function getCurrentUser() {
    const rawUser = localStorage.getItem("currentUser");
    if (!rawUser) {
        return null;
    }

    try {
        return JSON.parse(rawUser);
    } catch (error) {
        return null;
    }
}

function guardAdminPage() {
    const user = getCurrentUser();
    if (!user || user.role !== "admin") {
        alert("Qasja e ndaluar. Vetem admini i kycur mund te hyje ne dashboard.");
        window.location.href = "login.html";
    }
}

guardAdminPage();
