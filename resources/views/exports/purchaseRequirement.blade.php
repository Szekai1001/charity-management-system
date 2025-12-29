<table>
    <thead>
        <tr>
            <td colspan="3" style="font-size: 18px; font-weight: bold; text-align: center; height: 30px; vertical-align: middle;">
                Purchase Requirement Report
            </td>
        </tr>

        @if($year || $month)
        <tr>
            <td colspan="6" style="text-align: center; font-style: italic; color: #555555;">

                @if($month) Month: <strong>{{ $month }}</strong> @endif

                {{-- Only show the separator | if both Month AND Year exist --}}
                @if($month && $year) &nbsp;|&nbsp; @endif

                @if($year) Year: <strong>{{ $year }}</strong> @endif


            </td>
        </tr>
        @endif

        <tr>
            <td colspan="6"></td>
        </tr>

        <tr>
            <th style="font-weight: bold; border: 1px solid #000000; background-color: #dbe5f1; text-align: center; width: 15px;">Item ID</th>
            <th style="font-weight: bold; border: 1px solid #000000; background-color: #dbe5f1; text-align: left; width: 30px;">Item Name</th>
            <th style="font-weight: bold; border: 1px solid #000000; background-color: #dbe5f1; text-align: center; width: 15px;">Quantity</th>
            
        </tr>
    </thead>

    <tbody>
        @foreach($purchaseRequirements as $row)
        <tr>
            <td style="border: 1px solid #000000; text-align: center;">
                {{ $row->item_id }}
            </td>

            <td style="border: 1px solid #000000; text-align: left;">
                {{ $row->item->name }}
            </td>

            <td style="border: 1px solid #000000; text-align: center;">
                {{ $row->total_quantity }}
            </td>
        </tr>
        @endforeach
    </tbody>
</table>