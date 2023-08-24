// Example transaction data (you can replace this with actual data)
const transactions = [];

// Function to display transactions in the table
function displayTransactions() {
    const tableBody = document.getElementById('transactionTableBody');
    tableBody.innerHTML = '';

    transactions.forEach(transaction => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${transaction.date}</td>
            <td>${transaction.description}</td>
            <td>$${transaction.amount.toFixed(2)}</td>
        `;
        tableBody.appendChild(row);
    });
}

// Function to add a new transaction
function addTransaction(event) {
    event.preventDefault();

    const dateInput = document.getElementById('date');
    const descriptionInput = document.getElementById('description');
    const amountInput = document.getElementById('amount');

    const date = dateInput.value;
    const description = descriptionInput.value;
    const amount = parseFloat(amountInput.value);

    if (!date || !description || isNaN(amount)) {
        alert('Please enter valid transaction details.');
        return;
    }

    transactions.push({ date, description, amount });
    displayTransactions();

    dateInput.value = '';
    descriptionInput.value = '';
    amountInput.value = '';
}

// Attach event listener to the form
const addTransactionForm = document.getElementById('addTransactionForm');
addTransactionForm.addEventListener('submit', addTransaction);

// Initial display of transactions
displayTransactions();

// API endpoint URL
const apiUrl = 'https://www.expensify.com/api?command=Authenticate';

// API key
const apiKey = 'd7c3119c6cdab02d68d9';

const headers = {
    'Content-Type': 'application/json',
    'Authorization': `d7c3119c6cdab02d68d9 ${apiKey}`
};

const requestData = {
    // Provides any necessary data for the authenticate endpoint
};

fetch(apiUrl, {
    method: 'POST',
    headers: headers,
    body: JSON.stringify(requestData)
})
    .then(response => response.json())
    .then(data => {
        console.log('API Response:', data);
        // Handle the response data here
    })
    .catch(error => {
        console.error('API Error:', error);
        // method that attempts to display an error message to the user
        const errorMessage = document.createElement('p');
        errorMessage.textContent = 'An error occurred while processing your request. Please try again later.';
        errorMessage.style.color = 'red';
        document.body.appendChild(errorMessage);

  

