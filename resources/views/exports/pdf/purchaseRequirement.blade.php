@extends('layout.pdf')

@section('title', 'Purchase Requirement Report')

@section('content')

{{-- 1. HEADER --}}
<div class="header-container">
    <table class="header-table">
        <tr>
            {{-- Logo --}}
            <td width="60" valign="top">
                <img src="{{ public_path('image/logo.jpg') }}" style="height: 50px; width: auto;">
            </td>
            {{-- Organization Info --}}
            <td valign="middle" style="padding-left: 15px;">
                <h1 class="org-name">PKKM</h1>
                <h2 class="doc-title">Purchase Requirement Report</h2>
            </td>
            {{-- Timestamp (Right Aligned) --}}
            <td align="right" valign="bottom" style="font-size: 10px; color: #777;">
                Generated: {{ now()->format('d M Y, h:i A') }}
            </td>
        </tr>
    </table>
</div>

{{-- 2. REPORT FILTERS (Summary) --}}
<table class="meta-table" width="100%">
    <tr>
        {{-- SCENARIO 1: Specific Date Selected --}}
        @if(isset($date) && $date)
        <td class="meta-label">Date:</td>
        <td>
            {{ \Carbon\Carbon::parse($date)->format('d F Y') }}
            <span style="color: #666; font-size: 10px;">({{ \Carbon\Carbon::parse($date)->format('l') }})</span>
        </td>

        {{-- SCENARIO 2: Month/Year Selected (or All Time) --}}
        @else
        <td class="meta-label">Month:</td>
        <td width="100">
            {{ $month ? \Carbon\Carbon::create(null, $month)->format('F') : 'All Months' }}
        </td>

        <td class="meta-label">Year:</td>
        <td width="100">
            {{ $year ? $year : 'All Years' }}
        </td>
        @endif

        {{-- ALWAYS SHOW: Total Count (Right Aligned) --}}
        <td align="right">
            Total Records: <strong>{{ count($reportData) }}</strong>
        </td>
    </tr>
</table>

<table class="data-table">
    <thead>
        <tr>
            <th>Item ID</th>
            <th>Item Name</th>
            <th class="text-center">Quantity</th>
        </tr>
    </thead>
    <tbody>
        @forelse($reportData as $row)
        <tr>
            <td>{{ $row->item_id }}</td>
            <td>{{ $row->item->name }}</td>
            <td>{{ $row->total_quantity }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="6" align="center" style="padding: 30px; color: #888;">
                No records found matching the selected criteria.
            </td>
        </tr>
        @endforelse
    </tbody>
</table>

{{-- 4. FOOTER --}}
<div class="footer">
    <table width="100%">
        <tr>
            <td align="left">CONFIDENTIAL DOCUMENT - FOR INTERNAL USE ONLY</td>
            <td align="right">PKKM SYSTEM GENERATED REPORT</td>
        </tr>
    </table>
</div>
@endsection