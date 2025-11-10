/* ============================
   DriveZone Layout JS
   ============================ */

// Этот файл подключается глобально через layout.blade.php,
// функции экспортируются в window для inline-вызовов

// ==================== PRESS TIMER (УДЕРЖАНИЕ АВАТАРА) ====================
let pressTimer;

function startPressTimer() {
    pressTimer = setTimeout(() => {
        openProfileModal();
    }, 700);
}

function cancelPressTimer() {
    clearTimeout(pressTimer);
}

// ==================== МОДАЛКА ПРОФИЛЯ ====================
function openProfileModal() {
    const modal = document.getElementById('profileModal');
    if (modal) {
        modal.classList.remove('hidden');
        document.body.classList.add('blurred');
    }
}

function closeProfileModal() {
    const modal = document.getElementById('profileModal');
    if (modal) {
        modal.classList.add('hidden');
        document.body.classList.remove('blurred');
    }
}

// ==================== КАСТОМНОЕ ПОДТВЕРЖДЕНИЕ ====================
function openConfirmModal(message, onYes, title = 'Подтверждение') {
    const modal = document.getElementById('confirmModal');
    if (!modal) return;

    document.getElementById('confirmMessage').textContent = message || 'Вы уверены?';
    document.getElementById('confirmTitle').textContent = title;

    const yesBtn = document.getElementById('confirmYes');
    yesBtn.replaceWith(yesBtn.cloneNode(true));
    const newYesBtn = document.getElementById('confirmYes');

    newYesBtn.addEventListener('click', () => {
        closeConfirmModal();
        if (typeof onYes === 'function') onYes();
    });

    modal.classList.remove('hidden');
}

function closeConfirmModal() {
    const modal = document.getElementById('confirmModal');
    if (modal) modal.classList.add('hidden');
}

// ==================== АВАТАР: ВЫБОР ФАЙЛА ====================
function triggerFileInput() {
    document.getElementById('avatarInput')?.click();
}

document.getElementById('avatarInput')?.addEventListener('change', function (event) {
    const file = event.target.files?.[0];
    if (!file) return;

    openConfirmModal('Вы уверены, что хотите изменить аватар?', () => {
        const reader = new FileReader();
        reader.onload = (e) => {
            const preview = document.querySelector('.dz-avatar-preview');
            if (preview) preview.src = e.target.result;
        };
        reader.readAsDataURL(file);

        uploadAvatar(file);
    });
});

// ==================== ОБНОВЛЕНИЕ ВСЕХ АВАТАРОВ ====================
function updateAllAvatars(url) {
    const cacheBustUrl = url + '?t=' + Date.now();

    // 1) Модалка
    const preview = document.querySelector('.dz-avatar-preview');
    if (preview) preview.src = cacheBustUrl;

    // 2) Аватарки в шапке/меню
    document.querySelectorAll('.navbar-avatar').forEach(el => {
        if (el.tagName.toLowerCase() === 'img') {
            el.src = cacheBustUrl;
        } else {
            const img = document.createElement('img');
            img.src = cacheBustUrl;
            img.alt = 'avatar';
            img.className = 'dz-avatar-img navbar-avatar';
            img.onmousedown = startPressTimer;
            img.onmouseup = cancelPressTimer;
            img.onmouseleave = cancelPressTimer;
            el.replaceWith(img);
        }
    });

    console.log("Аватары обновлены:", cacheBustUrl);
}

// ==================== АВАТАР: ЗАГРУЗКА ====================
function uploadAvatar(file) {
    const formData = new FormData();
    formData.append('avatar', file);

    fetch('/profile/avatar', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
        body: formData
    })
        .then(res => {
            if (!res.ok) throw new Error("Ошибка HTTP " + res.status);
            return res.json();
        })
        .then(data => {
            if (!data.success) {
                alert("Ошибка: " + (data.message || "Не удалось обновить аватар."));
                return;
            }
            updateAllAvatars(data.url);
        })
        .catch(err => {
            console.error('Ошибка загрузки:', err);
            alert('Не удалось загрузить аватар. Попробуйте ещё раз.');
        });
}

// ==================== АВАТАР: УДАЛЕНИЕ ====================
document.querySelector('.btn-delete')?.addEventListener('click', function () {
    openConfirmModal('Удалить аватар?', () => {
        fetch('/profile/avatar', {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
        })
            .then(res => res.json())
            .then(data => {
                if (!data.success) throw new Error('Ошибка при удалении');
                updateAllAvatars(data.url);
            })
            .catch(err => {
                console.error('Ошибка при удалении:', err);
                alert('Не удалось удалить аватар. Попробуйте ещё раз.');
            });
    });
});

// ==================== TOOLTIP (имя + email) ====================
const avatarBtn = document.querySelector('.dz-avatar-btn');
const tooltip = document.getElementById('userTooltip');
let tooltipTimer;

if (avatarBtn && tooltip) {
    avatarBtn.addEventListener('mouseenter', () => {
        clearTimeout(tooltipTimer);
        tooltip.classList.add('show');
    });

    avatarBtn.addEventListener('mouseleave', () => {
        tooltipTimer = setTimeout(() => tooltip.classList.remove('show'), 120);
    });

    tooltip.addEventListener('mouseenter', () => {
        clearTimeout(tooltipTimer);
    });

    tooltip.addEventListener('mouseleave', () => {
        tooltipTimer = setTimeout(() => tooltip.classList.remove('show'), 120);
    });
}

// ==================== ALERT АВТО-УДАЛЕНИЕ ====================
document.addEventListener('DOMContentLoaded', () => {
    const alert = document.querySelector('.dz-alert');
    if (alert) {
        setTimeout(() => {
            alert.classList.add('fade-out');
            setTimeout(() => alert.remove(), 1000);
        }, 3000);
    }
});

// ==================== TOAST LOGOUT ====================
document.addEventListener('DOMContentLoaded', () => {
    const toast = document.getElementById('dz-toast');
    if (toast) {
        setTimeout(() => toast.classList.remove('show'), 3000);
    }
});

// ==================== LOGIN SUCCESS LOADER ====================
document.addEventListener('DOMContentLoaded', () => {
    const loader = document.getElementById('dz-loader');
    if (loader) {
        loader.classList.add('show');
        setTimeout(() => loader.remove(), 1200);
    }
});

// ==================== МОДАЛКА "О МАГАЗИНЕ" ====================
function openStoreModal() {
    const modal = document.getElementById('storeInfoModal');
    if (modal) {
        modal.classList.remove('hidden');
    }
}

function closeStoreModal() {
    const modal = document.getElementById('storeInfoModal');
    if (modal) {
        modal.classList.add('hidden');
    }
}


document.getElementById('btn-store-info')?.addEventListener('click', openStoreModal);


document.addEventListener('DOMContentLoaded', () => {
    const cartBadge = document.querySelector('#cart-count-badge');

    // === Загружаем количество товаров при загрузке страницы ===
    if (cartBadge) {
        fetch('/cart/count', {
            headers: { 'Accept': 'application/json' }
        })
            .then(res => res.json())
            .then(data => {
                cartBadge.textContent = data.count;
            })
            .catch(err => console.error('Ошибка при получении количества товаров', err));
    }

    // === (дальше твой существующий код добавления в корзину и т.п.) ===
});

// ==================== ДОБАВЛЕНИЕ В КОРЗИНУ (AJAX) ====================
document.addEventListener('DOMContentLoaded', () => {
    const cartBadge = document.querySelector('#cart-count-badge');

    // === Загружаем количество товаров при загрузке страницы ===
    if (cartBadge) {
        fetch('/cart/count', {
            headers: { 'Accept': 'application/json' }
        })
            .then(res => res.json())
            .then(data => {
                cartBadge.textContent = data.count;
            })
            .catch(err => console.error('Ошибка при получении количества товаров', err));
    }

    // === Обработка добавления товара в корзину ===
    document.querySelectorAll('form[action^="/cart/add"]').forEach(form => {
        form.addEventListener('submit', async e => {
            e.preventDefault();

            const url = form.action;
            const formData = new FormData(form);

            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                if (!response.ok) throw new Error('Ошибка при добавлении');

                // обновляем бейдж из сервера (сумма quantity)
                if (cartBadge) {
                    fetch('/cart/count', { headers: { 'Accept': 'application/json' } })
                        .then(res => res.json())
                        .then(data => {
                            cartBadge.textContent = data.count;
                            cartBadge.style.transition = 'transform 0.2s ease';
                            cartBadge.style.transform = 'scale(1.3)';
                            setTimeout(() => cartBadge.style.transform = 'scale(1)', 200);
                        })
                        .catch(err => console.error('Ошибка при обновлении бейджа корзины', err));
                }

                //  всплывающее уведомление
                const toast = document.createElement('div');
                toast.className = 'dz-toast show';
                toast.innerHTML = `<i class="bi bi-check-circle me-2"></i>Товар добавлен в корзину`;
                document.body.appendChild(toast);
                setTimeout(() => toast.classList.remove('show'), 2500);
                setTimeout(() => toast.remove(), 3000);

            } catch (err) {
                console.error(err);
                alert('Не удалось добавить товар. Попробуйте позже.');
            }
        });
    });
});

// === ГЛОБАЛЬНЫЙ ПОИСК ===
document.addEventListener("DOMContentLoaded", () => {
    const searchForm = document.querySelector(".dz-searchbar");
    const searchInput = searchForm?.querySelector("input[name='q']");
    const searchButton = searchForm?.querySelector("button");

    if (searchForm && searchInput) {
        // При нажатии Enter
        searchInput.addEventListener("keypress", (e) => {
            if (e.key === "Enter") {
                e.preventDefault();
                const query = searchInput.value.trim();
                if (query.length > 0) {
                    window.location.href = `/catalog/search?q=${encodeURIComponent(query)}`;
                }
            }
        });

        // При клике на кнопку 🔍
        searchButton?.addEventListener("click", (e) => {
            e.preventDefault();
            const query = searchInput.value.trim();
            if (query.length > 0) {
                window.location.href = `/catalog/search?q=${encodeURIComponent(query)}`;
            }
        });
    }
});

const storeModal = document.getElementById('storeInfoModal');
const openBtn = document.getElementById('btn-store-info');
const closeBtn = document.getElementById('closeStoreModal');

openBtn?.addEventListener('click', () => storeModal.classList.add('show'));
closeBtn?.addEventListener('click', () => storeModal.classList.remove('show'));
storeModal?.addEventListener('click', (e) => {
    if (e.target === storeModal) storeModal.classList.remove('show');
});


// ==================== УНИВЕРСАЛЬНОЕ УВЕДОМЛЕНИЕ (TOAST) ====================
function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `dz-toast show dz-toast-${type}`;
    toast.innerHTML = message;

    document.body.appendChild(toast);

    setTimeout(() => toast.classList.remove('show'), 3000);
    setTimeout(() => toast.remove(), 3500);
}


// ======== КНОПКА ДОБАВИТЬ АВТО ========
document.getElementById('btn-select-car')?.addEventListener('click', () => {
    if (!document.body.classList.contains('authenticated')) {
        showToast('Пожалуйста, войдите в аккаунт, чтобы добавить автомобиль.', 'warning');
        return;
    }

    // Временное уведомление о разработке
    showToast('Раздел "Мой автомобиль" находится в разработке. <br>Скоро можно будет выбрать модель и фильтровать товары!', 'info');
});


const vehicleForm = document.getElementById('vehicleForm');
vehicleForm?.addEventListener('submit', async e => {
    e.preventDefault();
    const formData = Object.fromEntries(new FormData(vehicleForm).entries());
    const csrf = document.querySelector('meta[name="csrf-token"]').content;

    const res = await fetch('/vehicles/add', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrf,
        },
        body: JSON.stringify(formData)
    });

    const result = await res.json();
    if (result.success) {
        alert(result.message);
        closeAddVehicleModal();
        vehicleForm.reset();
    } else {
        alert('Ошибка при добавлении автомобиля.');
    }
});

// === Закрытие модалки ===
function closeAddVehicleModal() {
    const modal = document.getElementById('addVehicleModal');
    if (modal) modal.classList.add('hidden');
}

// === Клик по фону для закрытия ===
document.getElementById('addVehicleModal')?.addEventListener('click', e => {
    if (e.target.id === 'addVehicleModal') closeAddVehicleModal();
});


/* ============================
   Экспорт функций в window
   ============================ */
window.startPressTimer = startPressTimer;
window.cancelPressTimer = cancelPressTimer;
window.openProfileModal = openProfileModal;
window.closeProfileModal = closeProfileModal;
window.openConfirmModal = openConfirmModal;
window.closeConfirmModal = closeConfirmModal;
window.triggerFileInput = triggerFileInput;
window.openStoreModal = openStoreModal;
window.closeStoreModal = closeStoreModal;
window.closeAddVehicleModal = closeAddVehicleModal;
