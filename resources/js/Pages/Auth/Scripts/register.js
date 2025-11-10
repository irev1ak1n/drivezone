const steps = document.querySelectorAll('.register-step');
let currentStep = 1;

const progressFill = document.getElementById('progressFill');
const roleButtons = document.querySelectorAll('.role-btn');
const roleInput = document.getElementById('roleInput');
const adminField = document.getElementById('adminCodeField');

// === Показ нужного шага ===
function showStep(step) {
    steps.forEach(s => s.classList.remove('active'));
    document.querySelector(`.register-step[data-step="${step}"]`).classList.add('active');
    progressFill.style.width = (step * 25) + '%';
    // При смене шага плавно скроллим вверх
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

// === Выбор роли ===
roleButtons.forEach(btn => {
    btn.addEventListener('click', () => {
        roleButtons.forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        roleInput.value = btn.dataset.role;
        document.getElementById('nextBtn1').disabled = false;
    });
});

// === Навигация по шагам ===
document.getElementById('nextBtn1').addEventListener('click', () => {
    currentStep = 2;
    showStep(currentStep);
});

// === Проверка обязательных полей (ШАГ 2) ===
const nextBtn2 = document.getElementById('nextBtn2');
const requiredStep2 = [
    document.querySelector('input[name="first_name"]'),
    document.querySelector('input[name="last_name"]')
];

nextBtn2.addEventListener('click', () => {
    let valid = true;
    requiredStep2.forEach(field => {
        field.classList.remove('shake');
        if (!field.value.trim()) {
            field.classList.add('shake');
            field.style.borderColor = '#ff4040';
            valid = false;
        } else {
            field.style.borderColor = '#2d2d2d';
        }
    });

    if (!valid) {
        showErrorTooltip("Заполните обязательные поля!");
        return;
    }

    currentStep = 3;
    showStep(currentStep);
});

// === Автоматически убираем красную рамку при вводе (ШАГ 2) ===
requiredStep2.forEach(field => {
    field.addEventListener('input', () => {
        if (field.value.trim()) field.style.borderColor = '#2d2d2d';
    });
});

// === Проверка обязательных полей (ШАГ 3) ===
const nextBtn3 = document.getElementById('nextBtn3');
const emailField = document.querySelector('input[name="email"]');
const phoneField = document.querySelector('input[name="phone_number"]');

nextBtn3.addEventListener('click', () => {
    emailField.classList.remove('shake');
    emailField.style.borderColor = '#2d2d2d';

    // Проверка на пустое поле
    if (!emailField.value.trim()) {
        emailField.classList.add('shake');
        emailField.style.borderColor = '#ff4040';
        showErrorTooltip("Укажите адрес электронной почты!");
        return;
    }

    // Проверка формата email
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailPattern.test(emailField.value.trim())) {
        emailField.classList.add('shake');
        emailField.style.borderColor = '#ff4040';
        showErrorTooltip("Введите корректный email!");
        return;
    }

    // Всё ок — убираем рамку
    emailField.style.borderColor = '#2d2d2d';

    // Переход на шаг 4
    currentStep = 4;
    adminField.style.display = (roleInput.value === 'admin') ? 'block' : 'none';
    showStep(currentStep);
});

// === Автоматически убираем красную рамку при вводе (ШАГ 3) ===
emailField.addEventListener('input', () => {
    emailField.style.borderColor = '#2d2d2d';
});

// === Кнопки "Назад" ===
document.getElementById('prevBtn2').addEventListener('click', () => { currentStep = 1; showStep(currentStep); });
document.getElementById('prevBtn3').addEventListener('click', () => { currentStep = 2; showStep(currentStep); });
document.getElementById('prevBtn4').addEventListener('click', () => { currentStep = 3; showStep(currentStep); });

// === Всплывающее сообщение об ошибке ===
function showErrorTooltip(message) {
    let tooltip = document.getElementById('errorTooltip');
    if (!tooltip) {
        tooltip = document.createElement('div');
        tooltip.id = 'errorTooltip';
        tooltip.textContent = message;
        document.body.appendChild(tooltip);
    }

    tooltip.classList.add('visible');
    setTimeout(() => tooltip.classList.remove('visible'), 2000);
}

// === Проверка обязательных полей ===
const passwordField = document.querySelector('input[name="password"]');
const confirmField = document.querySelector('input[name="password_confirmation"]');
const adminCodeField = document.querySelector('input[name="admin_code"]');
const submitBtn = document.getElementById('submitBtn');

submitBtn.addEventListener('click', (e) => {
    let valid = true;

    // Сброс эффектов только для своих полей
    [passwordField, confirmField, adminCodeField].forEach(f => {
        if (f) {
            f.classList.remove('shake');
            f.style.borderColor = '#2d2d2d';
        }
    });

    // Проверяем пароли
    if (!passwordField.value.trim() || !confirmField.value.trim()) {
        [passwordField, confirmField].forEach(f => {
            if (!f.value.trim()) {
                f.classList.add('shake');
                f.style.borderColor = '#ff4040';
            }
        });
        showErrorTooltip("Пожалуйста, заполните все обязательные поля!");
        e.preventDefault();
        valid = false;
    }

    // Проверяем совпадение паролей
    if (valid && passwordField.value !== confirmField.value) {
        [passwordField, confirmField].forEach(f => {
            f.classList.add('shake');
            f.style.borderColor = '#ff4040';
        });
        showErrorTooltip("Пароли не совпадают!");
        e.preventDefault();
        valid = false;
    }

    // Проверяем минимальную длину
    if (valid && passwordField.value.length < 6) {
        passwordField.classList.add('shake');
        passwordField.style.borderColor = '#ff4040';
        showErrorTooltip("Пароль должен содержать минимум 6 символов!");
        e.preventDefault();
        valid = false;
    }

    // === Проверка секретного кода (только если выбрана роль admin) ===
    if (valid && roleInput.value === 'admin') {
        if (!adminCodeField.value.trim()) {
            adminCodeField.classList.add('shake');
            adminCodeField.style.borderColor = '#ff4040';
            showErrorTooltip("Введите секретный код администратора!");
            e.preventDefault();
            valid = false;
        } else {
            adminCodeField.style.borderColor = '#2d2d2d';
        }
    }

    // Убираем рамки при вводе
    [passwordField, confirmField, adminCodeField].forEach(f => {
        if (f) {
            f.addEventListener('input', () => {
                if (f.value.trim()) f.style.borderColor = '#2d2d2d';
            });
        }
    });
});

// === Блокируем Enter на промежуточных шагах ===
document.getElementById('registerForm').addEventListener('keydown', function (e) {
    if (e.key === 'Enter') {
        const activeStep = document.querySelector('.register-step.active');
        const stepNumber = parseInt(activeStep.dataset.step);

        // предотвращаем Enter, если не на последнем шаге
        if (stepNumber !== 4) {
            e.preventDefault();

            // при Enter можно сделать то же, что и кнопка "Далее"
            if (stepNumber === 1) document.getElementById('nextBtn1').click();
            if (stepNumber === 2) document.getElementById('nextBtn2').click();
            if (stepNumber === 3) document.getElementById('nextBtn3').click();
        }
    }
});


document.addEventListener('DOMContentLoaded', () => {
    const referrer = document.referrer;

    // Если пользователь пришёл с другой auth-страницы — заменяем историю,
    // чтобы "Назад" возвращал в каталог, а не зацикливал на /login ↔ /register
    if (referrer.includes('/login') || referrer.includes('/register')) {
        history.replaceState(null, '', '/catalog/products');
    }
});

// === Показ / скрытие пароля ===
function togglePassword() {
    const passwordInput = document.getElementById('password');
    const toggleIcon = document.querySelector('.toggle-password');

    if (!passwordInput) return;

    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleIcon.classList.remove('bi-eye');
        toggleIcon.classList.add('bi-eye-slash');
    } else {
        passwordInput.type = 'password';
        toggleIcon.classList.remove('bi-eye-slash');
        toggleIcon.classList.add('bi-eye');
    }
}
