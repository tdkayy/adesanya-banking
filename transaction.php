// Assuming you have received the $transaction_response from the API

// Decode the JSON response
$transactions = json_decode($transaction_response, true);

if (is_array($transactions) && isset($transactions['transactions'])) {
    $transactionList = $transactions['transactions'];

    echo '<table>';
    echo '<tr>';
    echo '<th>Date</th>';
    echo '<th>Amount</th>';
    echo '<th>Merchant</th>';
    // Add more columns as needed
    echo '</tr>';

    foreach ($transactionList as $transaction) {
        echo '<tr>';
        echo '<td>' . $transaction['date'] . '</td>';
        echo '<td>' . $transaction['amount'] . '</td>';
        echo '<td>' . $transaction['merchant'] . '</td>';
        // Add more columns as needed
        echo '</tr>';
    }

    echo '</table>';
} else {
    echo 'No transactions found.';
}
