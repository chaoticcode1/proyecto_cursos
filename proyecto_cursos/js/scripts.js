// Selección de elementos
const forgotPasswordButton = document.getElementById("forgotPasswordButton");
const loginButton = document.getElementById("loginButton");
const loginForm = document.getElementById("loginForm");
const recoverForm = document.getElementById("recoverForm");
const formContainer = document.querySelector(".form-container");

// Estado para alternar formularios
let isRecoverMode = false;

// Función para mostrar el formulario de recuperación
forgotPasswordButton.addEventListener("click", () => {
    // Mueve el formulario hacia la izquierda
    formContainer.style.transform = "translateX(-100%)"; // Desliza el formulario a la izquierda

    // Mostrar el formulario de recuperación y ocultar el de login
    loginForm.classList.add("hidden");
    recoverForm.classList.remove("hidden");

    // Ocultar el botón de "¿Olvidaste tu contraseña?"
    forgotPasswordButton.style.display = "none";

    // Mostrar el botón "Deseo iniciar sesión"
    loginButton.style.display = "block";
});

// Función para mostrar el formulario de login
loginButton.addEventListener("click", () => {
    // Vuelve el formulario al centro
    formContainer.style.transform = "translateX(0)"; // Vuelve el formulario al centro

    // Mostrar el formulario de login y ocultar el de recuperación
    recoverForm.classList.add("hidden");
    loginForm.classList.remove("hidden");

    // Ocultar el botón "Deseo iniciar sesión"
    loginButton.style.display = "none";

    // Mostrar el botón "¿Olvidaste tu contraseña?"
    forgotPasswordButton.style.display = "block";
});
