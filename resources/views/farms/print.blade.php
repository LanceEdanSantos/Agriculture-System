<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Farm Report - {{ $farm->name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            color: #2c3e50;
        }
        .header .subtitle {
            color: #7f8c8d;
            margin-top: 5px;
        }
        .section {
            margin-bottom: 30px;
            page-break-inside: avoid;
        }
        .section-header {
            background-color: #2c3e50;
            color: white;
            padding: 10px 15px;
            font-size: 18px;
            font-weight: bold;
            border-radius: 5px;
            margin-bottom: 15px;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 15px;
        }
        .info-item {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            border-left: 4px solid #2c3e50;
        }
        .info-label {
            font-weight: bold;
            color: #495057;
            font-size: 14px;
            margin-bottom: 5px;
        }
        .info-value {
            color: #212529;
            font-size: 16px;
        }
        .status-badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 15px;
            font-size: 14px;
            font-weight: bold;
        }
        .status-active {
            background-color: #28a745;
            color: white;
        }
        .status-inactive {
            background-color: #dc3545;
            color: white;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #f8f9fa;
            font-weight: bold;
            color: #495057;
        }
        tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        .no-data {
            text-align: center;
            padding: 20px;
            color: #6c757d;
            font-style: italic;
            background-color: #f8f9fa;
            border-radius: 5px;
        }
        @media print {
            body {
                margin: 0;
            }
            .header {
                margin-bottom: 20px;
            }
            .section {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Agriculture System</h1>
        <div class="subtitle">Farm Report: {{ $farm->name }}</div>
        <div class="subtitle">Generated on: {{ date('F j, Y \a\t g:i A') }}</div>
    </div>

    <!-- Farm Information Section -->
    <div class="section">
        <div class="section-header">Farm Information</div>
        <div class="info-grid">
            <div class="info-item">
                <div class="info-label">Farm Name</div>
                <div class="info-value">{{ $farm->name }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Status</div>
                <div class="info-value">
                    <span class="status-badge {{ $farm->is_active ? 'status-active' : 'status-inactive' }}">
                        {{ $farm->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>
            </div>
        </div>
        @if($farm->description)
        <div class="info-item">
            <div class="info-label">Description</div>
            <div class="info-value">{{ $farm->description }}</div>
        </div>
        @endif
        <div class="info-grid">
            <div class="info-item">
                <div class="info-label">Date Created</div>
                <div class="info-value">{{ $farm->created_at->format('F j, Y') }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Last Updated</div>
                <div class="info-value">{{ $farm->updated_at->format('F j, Y') }}</div>
            </div>
        </div>
    </div>

    <!-- Users Section -->
    <div class="section">
        <div class="section-header">Users ({{ $farm->users->count() }})</div>
        @if($farm->users->count() > 0)
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Visibility</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($farm->users as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ ucfirst($user->pivot->role ?? 'N/A') }}</td>
                            <td>{{ $user->pivot->is_visible ? 'Visible' : 'Hidden' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="no-data">No users assigned to this farm.</div>
        @endif
    </div>
</body>
</html>
