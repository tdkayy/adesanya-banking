// Reusable function for making API requests
async function makeApiRequest(url, method, data) {
    const headers = {
        'Content-Type': 'application/json',
        'Authorization': 'd7c3119c6cdab02d68d9', // API key
    };

    try {
        const response = await fetch(url, {
            method: method,
            headers: headers,
            body: JSON.stringify(data),
        });

        if (!response.ok) {
            throw new Error(`API request failed with status: ${response.status}`);
        }

        return await response.json();
    } catch (error) {
        console.error('API Error:', error);
        throw error;
    }
}

// Function to get transactions from API
async function getTransactions(authToken) {
    const apiUrl = 'https://www.expensify.com/api?command=Get';
    const requestData = {
        type: 'getTransactions',
        authToken: authToken,
    };

    try {
        const response = await makeApiRequest(apiUrl, 'POST', requestData);
        return response.transactionList || [];
    } catch (error) {
        console.error('Error retrieving transactions:', error);
        return [];
    }
}

// Function to create a new transaction
async function createTransaction(authToken, date, merchant, amount) {
    const apiUrl = 'https://www.expensify.com/api?command=CreateTransaction'; // Replace with actual endpoint
    const transactionData = {
        type: 'createTransaction',
        authToken: authToken,
        created: date,
        merchant: merchant,
        amount: amount,
    };

    try {
        const response = await makeApiRequest(apiUrl, 'POST', transactionData);
        return response.transactionID || null;
    } catch (error) {
        console.error('Error creating transaction:', error);
        return null;
    }
}

// Function to display transactions in the table
function displayTransactions(transactions) {
    const table = document.getElementById('transaction-table');
    table.innerHTML = ''; // Clear existing table content

    // Create table header
    const headerRow = table.insertRow();
    const headers = ['Date', 'Merchant', 'Amount'];
    headers.forEach(headerText => {
        const th = document.createElement('th');
        th.textContent = headerText;
        headerRow.appendChild(th);
    });

    // Populate table with transaction data
    transactions.forEach(transaction => {
        const row = table.insertRow();
        const dateCell = row.insertCell(0);
        const merchantCell = row.insertCell(1);
        const amountCell = row.insertCell(2);

        dateCell.textContent = transaction.date;
        merchantCell.textContent = transaction.merchant;
        amountCell.textContent = transaction.amount;
    });
}

// Function to add a new transaction
async function addTransaction(event) {
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

    const authToken = 'yourAuthToken'; // Replace with actual authToken
    const transactionID = await createTransaction(authToken, date, description, amount);
    if (transactionID) {
        const newTransaction = { date, merchant: description, amount };
        transactions.push(newTransaction);
        displayTransactions(transactions);
    }

    dateInput.value = '';
    descriptionInput.value = '';
    amountInput.value = '';
}

// Attach event listener to the form
const addTransactionForm = document.getElementById('addTransactionForm');
addTransactionForm.addEventListener('submit', addTransaction);

// Initial display of transactions
const transactions = []; // Example transaction data (replace with actual data)
displayTransactions(transactions);