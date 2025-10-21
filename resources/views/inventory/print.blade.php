<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Report</title>
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
        .filters {
            margin-bottom: 20px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }
        .filters h3 {
            margin-top: 0;
            color: #495057;
        }
        .filters-applied {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
        }
        .filter-item {
            background-color: #e9ecef;
            padding: 5px 10px;
            border-radius: 3px;
            font-size: 14px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
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
        .stock-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: bold;
            text-align: center;
        }
        .stock-no {
            background-color: #dc3545;
            color: white;
        }
        .stock-low {
            background-color: #ffc107;
            color: #212529;
        }
        .stock-ok {
            background-color: #28a745;
            color: white;
        }
        .no-data {
            text-align: center;
            padding: 40px;
            color: #6c757d;
            font-style: italic;
        }
        @media print {
            body {
                margin: 0;
            }
            .header {
                margin-bottom: 20px;
            }
            .filters {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Agriculture System</h1>
        <div class="subtitle">Inventory Report</div>
        <div class="subtitle">Generated on: {{ date('F j, Y \a\t g:i A') }}</div>
    </div>

    @if(count($items) > 0)
        <div class="filters">
            <h3>Applied Filters:</h3>
            <div class="filters-applied">
                @foreach($filters as $key => $value)
                    @if($value && $key !== 'deleted')
                        <span class="filter-item">{{ ucfirst(str_replace('_', ' ', $key)) }}: {{ $value }}</span>
                    @endif
                @endforeach
                @if(isset($filters['deleted']) && $filters['deleted'])
                    <span class="filter-item">Including Deleted Items</span>
                @endif
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Item Name</th>
                    <th>Current Stock</th>
                    <th>Supplier</th>
                    <th>Unit</th>
                    <th>Date Added</th>
                    <th>Expiration</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $item)
                    <tr>
                        <td>{{ $item->name }}</td>
                        <td>
                            <span class="stock-badge
                                @if($item->current_stock <= 0) stock-no
                                @elseif($item->current_stock <= $item->minimum_stock) stock-low
                                @else stock-ok
                                @endif">
                                @if($item->current_stock <= 0)
                                    No Stock
                                @elseif($item->current_stock <= $item->minimum_stock)
                                    Low Stock
                                @else
                                    In Stock
                                @endif
                            </span>
                        </td>
                        <td>{{ $item->supplier->name ?? 'N/A' }}</td>
                        <td>{{ $item->unit->name ?? 'N/A' }}</td>
                        <td>{{ $item->created_at->format('M j, Y') }}</td>
                        <td>
                            @if($item->expiration_date)
                                <span style="color:
                                    @if($item->expiration_date < now()) #dc3545
                                    @elseif($item->expiration_date < now()->addDays(30)) #ffc107
                                    @else #28a745
                                    @endif">
                                    {{ $item->expiration_date->format('M j, Y') }}
                                </span>
                            @else
                                N/A
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="no-data">
            <h3>No inventory items found matching the selected filters.</h3>
        </div>
    @endif
</body>
</html>
