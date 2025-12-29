<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'PDF Report')</title>

    <!-- Bootstrap CSS (for PDF generation, include inline or via CDN) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Simplified Professional Styles --}}
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #333;
            font-size: 12px;
            line-height: 1.4;
        }

        /* Header Section */
        .header-container {
            width: 100%;
            border-bottom: 2px solid #333;
            /* Strong underline for header */
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .header-table {
            width: 100%;
        }

        .org-name {
            font-size: 18px;
            font-weight: bold;
            text-transform: uppercase;
            margin: 0;
            color: #000;
        }

        .doc-title {
            font-size: 14px;
            font-weight: bold;
            color: #555;
            margin: 2px 0 0 0;
            text-transform: uppercase;
        }

        /* Meta Data (Date, Filters) */
        .meta-table {
            width: 100%;
            margin-bottom: 15px;
            font-size: 11px;
            color: #555;
        }

        .meta-label {
            font-weight: bold;
            color: #333;
            width: 60px;
        }

        /* Main Data Table */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }

        .data-table th {
            border-bottom: 1px solid #000;
            text-align: left;
            padding: 8px 5px;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 10px;
            color: #000;
            background-color: #f8f9fa;
            /* Very subtle grey header */
        }

        .data-table td {
            border-bottom: 1px solid #ddd;
            /* Light separator lines */
            padding: 8px 5px;
            vertical-align: middle;
        }

        /* Status Colors (Text only) */
        .status-text {
            font-weight: bold;
            text-transform: uppercase;
            font-size: 10px;
        }

        .present {
            color: #198754;
        }

        /* Corporate Green */
        .absent {
            color: #dc3545;
        }

        /* Corporate Red */
        .excused {
            color: #fd7e14;
        }

        /* Corporate Orange */

        /* Footer */
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            font-size: 9px;
            color: #999;
            border-top: 1px solid #eee;
            padding-top: 5px;
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        @yield('content')
    </div>
</body>

</html>