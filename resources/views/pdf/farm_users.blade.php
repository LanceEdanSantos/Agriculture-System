<!DOCTYPE html>
<html>
<head>
    <title>Users for {{ $farm->name }}</title>
    <style>
        body { font-family: sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background-color: #f5f5f5; }
    </style>
</head>
<body>
    <h2>Farm: {{ $farm->name }}</h2>
    <p>Exported on: {{ now()->format('M j, Y g:i A') }}</p>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>User Name</th>
                <th>Email</th>
                <th>Created At</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $index => $user)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $user->first_name }} {{ $user->middle_name }} {{ $user->last_name }} {{ $user->suffix }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->created_at->format('M j, Y') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
