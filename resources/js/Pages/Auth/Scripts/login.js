const phoneInputField = document.querySelector("#phone");
const emailInputField = document.querySelector("#email");
const hiddenLogin = document.querySelector("#login-hidden");
const form = document.querySelector("form");

// intl-tel-input init
const iti = window.intlTelInput(phoneInputField, {
    initialCountry: "ru",
    preferredCountries: ["ru", "us", "ua", "kz", "by"],
    utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@18.1.1/build/js/utils.js",
    separateDialCode: true,
    nationalMode: false,
    allowDropdown: true,
    showSelectedDialCode: true,
    dropdownContainer: document.body,
    autoPlaceholder: "off"
});

// Cleave init только при включённой вкладке Phone
let cleave = null;
function enableCleave(region = 'RU') {
    if (cleave) cleave.destroy();
    cleave = new Cleave(phoneInputField, {
        phone: true,
        phoneRegionCode: region
    });
}

// Очистка перед сабмитом
form.addEventListener("submit", function() {
    if (document.querySelector('.login-tab[data-type="phone"].active')) {
        // Возьмем полный номер с кодом страны (+79901234567)
        let phoneNumber = iti.getNumber();
        hiddenLogin.value = phoneNumber;
    } else {
        hiddenLogin.value = emailInputField.value;
    }
});


// Tabs переключение
const tabs = document.querySelectorAll('.login-tab');
const emailField = document.querySelector('.email-field');
const phoneField = document.querySelector('.phone-field');

tabs.forEach(tab => {
    tab.addEventListener('click', () => {
        tabs.forEach(t => t.classList.remove('active'));
        tab.classList.add('active');

        if (tab.dataset.type === 'email') {
            emailField.style.display = 'block';
            phoneField.style.display = 'none';
        } else {
            emailField.style.display = 'none';
            phoneField.style.display = 'block';
            iti.setCountry('ru');
            enableCleave('RU'); // включаем Cleave только при переключении на Phone
        }
    });
});

// Toggle password
// === Глобальная функция для inline вызова ===
window.togglePassword = function () {
    const input = document.getElementById('password');
    const icon = document.querySelector('.toggle-password');
    if (!input || !icon) return;

    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('bi-eye', 'bi-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.replace('bi-eye-slash', 'bi-eye');
    }
};

document.addEventListener('DOMContentLoaded', () => {
    const referrer = document.referrer;

    // Если пользователь пришёл с другой auth-страницы — заменяем историю,
    // чтобы "Назад" возвращал в каталог, а не зацикливал на /login ↔ /register
    if (referrer.includes('/login') || referrer.includes('/register')) {
        history.replaceState(null, '', '/catalog/products');
    }
});

document.addEventListener('DOMContentLoaded', () => {
    const toast = document.getElementById('dzToast');
    if (toast) {
        // небольшая задержка для плавного входа
        setTimeout(() => toast.classList.add('show'), 100);

        // автозакрытие
        setTimeout(() => {
            toast.classList.remove('show');
            toast.classList.add('hide');
        }, 4000);
    }
});
