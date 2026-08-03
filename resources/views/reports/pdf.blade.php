<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title ?? 'Student Flight Hours & Lesson Progress Report' }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333;
            margin: 20px;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 25px;
            border-bottom: 2px solid #0d6efd;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0 0 5px 0;
            font-size: 20px;
            color: #0d6efd;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .header p {
            margin: 2px 0;
            color: #6c757d;
            font-size: 12px;
        }
        .meta-info {
            display: table;
            width: 100%;
            margin-bottom: 15px;
            font-size: 11px;
            color: #495057;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .table th {
            background-color: #0d6efd;
            color: #ffffff;
            font-weight: bold;
            text-align: left;
            padding: 8px 10px;
            font-size: 11px;
            text-transform: uppercase;
        }
        .table td {
            padding: 8px 10px;
            border-bottom: 1px solid #dee2e6;
            font-size: 11px;
        }
        .table tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 10px;
            color: #6c757d;
            border-top: 1px solid #dee2e6;
            padding-top: 8px;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>Student Flight Hours Report</h1>
        <p>Progress of Lessons & Accumulated Flight Hours</p>
    </div>

    <div class="meta-info">
        <strong>Report Date:</strong> {{ date('F d, Y') }} | 
        <strong>Scope:</strong> {{ $scope ?? 'All Training Providers' }}
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>Student</th>
                <th>Training Provider</th>
                <th>Total Flight Hours</th>
                <th>Instructor</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reportData as $row)
                <tr>
                    <td><strong>{{ $row->student_name }}</strong></td>
                    <td>{{ $row->provider_name }}</td>
                    <td>{{ $row->total_flight_hours }}</td>
                    <td>{{ $row->instructor_name }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" style="text-align: center; color: #6c757d;">No student records found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Generated automatically by Aviation Management System on {{ date('Y-m-d H:i:s') }}
    </div>

</body>
</html>
