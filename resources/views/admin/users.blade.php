@extends('layouts.layout')

@section('content')
    <div class="container my-5">
        <h3 class="fw-bold text-uppercase mb-3">
            <i class="bi bi-people-fill text-orange"></i> Пользователи
        </h3>
        <div class="catalog-divider mb-4"></div>

        {{-- Всплывающее уведомление --}}
        @if(session('success'))
            <div class="dz-toast show">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            </div>
        @endif

        <div class="card shadow-sm border-0 p-4">
            <div class="table-responsive">
                <table class="table align-middle table-hover">
                    <thead class="table-dark text-uppercase">
                    <tr>
                        <th>Имя</th>
                        <th>Email</th>
                        <th>Роль</th>
                        <th class="text-center">Действия</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($users as $u)
                        <tr>
                            {{-- === Форма обновления === --}}
                            <form action="{{ route('admin.users.update', $u->id) }}" method="POST">
                                @csrf
                                <td style="width: 25%">
                                    <div class="d-flex gap-2">
                                        <input type="text" name="first_name" value="{{ $u->first_name }}" class="form-control form-control-sm" placeholder="Имя">
                                        <input type="text" name="last_name" value="{{ $u->last_name }}" class="form-control form-control-sm" placeholder="Фамилия">
                                    </div>
                                </td>

                                <td style="width: 25%">
                                    <input type="email" name="email" value="{{ $u->email }}" class="form-control form-control-sm" placeholder="Email">
                                </td>

                                <td style="width: 20%">
                                    <select name="role" class="form-select form-select-sm">
                                        <option value="customer" {{ $u->role === 'customer' ? 'selected' : '' }}>Пользователь</option>
                                        <option value="employee" {{ $u->role === 'employee' ? 'selected' : '' }}>Сотрудник</option>
                                        <option value="manager" {{ $u->role === 'manager' ? 'selected' : '' }}>Менеджер</option>
                                        <option value="admin" {{ $u->role === 'admin' ? 'selected' : '' }}>Администратор</option>
                                    </select>
                                </td>

                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        <button class="btn btn-orange btn-sm fw-semibold px-3" type="submit">
                                            <i class="bi bi-check-lg"></i> Сохранить
                                        </button>
                                    </div>
                            </form>

                            {{-- === Форма удаления === --}}
                            <form action="{{ route('admin.users.destroy', $u->id) }}" method="POST"
                                  data-ajax-delete
                                  onsubmit="return confirm('Удалить пользователя {{ $u->first_name }} {{ $u->last_name }}?')"
                                  class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-dark btn-sm fw-semibold px-3" type="submit">
                                    <i class="bi bi-trash3"></i> Удалить
                                </button>
                            </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Пагинация --}}
            <div class="mt-4 d-flex justify-content-center">
                {{ $users->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Обработчик всех кнопок "Удалить"
            document.querySelectorAll('form[data-ajax-delete]').forEach(form => {
                form.addEventListener('submit', async (e) => {
                    e.preventDefault();

                    const confirmed = confirm('Удалить пользователя?');
                    if (!confirmed) return;

                    const url = form.action;
                    const token = document.querySelector('meta[name="csrf-token"]').content;

                    try {
                        const response = await fetch(url, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': token,
                                'Accept': 'application/json'
                            }
                        });

                        if (response.ok) {
                            // Удаляем строку пользователя из таблицы
                            const row = form.closest('tr');
                            row.style.transition = 'opacity 0.4s';
                            row.style.opacity = 0;
                            setTimeout(() => row.remove(), 400);

                            // Показываем красивое уведомление
                            showToast('Пользователь успешно удалён ✅');
                        } else {
                            showToast('Ошибка при удалении пользователя ⚠️');
                        }
                    } catch (err) {
                        console.error(err);
                        showToast('Ошибка соединения с сервером');
                    }
                });
            });

            // Функция для показа всплывающего тоста
            function showToast(message) {
                const toast = document.createElement('div');
                toast.className = 'dz-toast show';
                toast.innerHTML = `<i class="bi bi-check-circle me-2"></i>${message}`;
                document.body.appendChild(toast);

                setTimeout(() => {
                    toast.classList.remove('show');
                    setTimeout(() => toast.remove(), 400);
                }, 4000);
            }
        });
    </script>
@endsection
