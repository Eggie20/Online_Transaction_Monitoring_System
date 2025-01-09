<?php
function exportToExcel($pdo) {
    $transactions = getAllTransactions($pdo);
    
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="Transaction_Report_' . date('Y-m-d') . '.xls"');
    header('Cache-Control: max-age=0');
    
    // Start HTML table with styles for better Excel formatting
    echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <!--[if gte mso 9]>
        <xml>
            <x:ExcelWorkbook>
                <x:ExcelWorksheets>
                    <x:ExcelWorksheet>
                        <x:Name>Transaction Report</x:Name>
                        <x:WorksheetOptions>
                            <x:DisplayGridlines/>
                        </x:WorksheetOptions>
                    </x:ExcelWorksheet>
                </x:ExcelWorksheets>
            </x:ExcelWorkbook>
        </xml>
        <![endif]-->
        <style>
            td { 
                border: 0.5pt solid #ccc;
                padding: 5px;
                mso-number-format:"\\@";
            }
            .header {
                background-color: #4B88E5;
                color: white;
                font-weight: bold;
            }
            .number { mso-number-format:"0"; }
            .amount { mso-number-format:"₱#,##0.00"; }
        </style>
    </head>
    <body>';
    
    echo '<table border="1">';
    
    // Headers
    echo '<tr>
        <td class="header">Name</td>
        <td class="header">Transaction Method</td>
        <td class="header">Transaction Number</td>
        <td class="header">Amount</td>
        <td class="header">Date</td>
        <td class="header">Time</td>
    </tr>';
    
    foreach($transactions as $row) {
        echo '<tr>';
        echo '<td>' . htmlspecialchars($row['name']) . '</td>';
        echo '<td>' . ucfirst(htmlspecialchars($row['transaction_method'])) . '</td>';
        echo '<td style="mso-number-format:\'@\';">' . htmlspecialchars($row['transaction_number']) . '</td>';
        echo '<td class="amount">' . $row['amount'] . '</td>';
        echo '<td>' . date('M d, Y', strtotime($row['transaction_date'])) . '</td>';
        echo '<td>' . date('h:i A', strtotime($row['transaction_time'])) . '</td>';
        echo '</tr>';
    }
    
    echo '</table>';
    echo '</body></html>';
    exit;
}
?>
