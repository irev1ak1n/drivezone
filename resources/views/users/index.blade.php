<h1>Пользователи</h1>
@if(session('success')) <div>{{ session('success') }}</div> @endif

<a href="{{ route('admin.users.create') }}">Создать</a>

<table border="1" cellpadding="6">
    <tr><th>Имя</th><th>Email</th><th>Роль</th><th></th></tr>
    @foreach($users as $u)
        <tr>
            <td>{{ $u->last_name }} {{ $u->first_name }}</td>
            <td>{{ $u->email }}</td>
            <td>{{ $u->role }}</td>
            <td>
                <a href="{{ route('admin.users.show', $u->id) }}">Просмотр</a> |
                <a href="{{ route('admin.users.edit', $u->id) }}">Редактировать</a> |
                <form action="{{ route('admin.users.destroy', $u->id) }}" method="POST" style="display:inline;">
                    @csrf @method('DELETE')
                    <button type="submit" onclick="return confirm('Удалить?')">Удалить</button>
                </form>
            </td>
        </tr>
    @endforeach
</table>
