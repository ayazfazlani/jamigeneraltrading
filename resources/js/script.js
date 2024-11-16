// DOM elements
const stockInForm = document.getElementById('stockInForm');
const stockOutForm = document.getElementById('stockOutForm');
const fetchTransactionsButton = document.getElementById('fetchTransactions');
const transactionsList = document.getElementById('transactionsList');

// API Base URL (adjust based on your server URL)
const apiUrl = '/api';

// Handle Stock In
stockInForm.addEventListener('submit', function(e) {
    e.preventDefault();

    const item_id = document.getElementById('item_id_in').value;
    const quantity = document.getElementById('quantity_in').value;
    const cost = document.getElementById('cost_in').value;

    const data = {
        item_id: item_id,
        quantity: quantity,
        cost: cost
    };

    fetch(`${apiUrl}/stock/in`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(data),
    })
    .then(response => response.json())
    .then(data => {
        alert('Stock added successfully!');
        console.log(data);
    })
    .catch(error => {
        alert('Error adding stock');
        console.error(error);
    });
});

// Handle Stock Out
stockOutForm.addEventListener('submit', function(e) {
    e.preventDefault();

    const item_id = document.getElementById('item_id_out').value;
    const quantity = document.getElementById('quantity_out').value;
    const price = document.getElementById('price_out').value;

    const data = {
        item_id: item_id,
        quantity: quantity,
        price: price
    };

    fetch(`${apiUrl}/stock/out`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(data),
    })
    .then(response => response.json())
    .then(data => {
        alert('Stock removed successfully!');
        console.log(data);
    })
    .catch(error => {
        alert('Error removing stock');
        console.error(error);
    });
});

// Fetch Transactions
fetchTransactionsButton.addEventListener('click', function() {
    fetch(`${apiUrl}/transactions`)
    .then(response => response.json())
    .then(data => {
        transactionsList.innerHTML = '';
        data.forEach(transaction => {
            const li = document.createElement('li');
            li.textContent = `Transaction ID: ${transaction.id} - ${transaction.type} - Quantity: ${transaction.quantity} - Total Price: ${transaction.total_price}`;
            transactionsList.appendChild(li);
        });
    })
    .catch(error => {
        alert('Error fetching transactions');
        console.error(error);
    });
});


document.addEventListener("livewire:load", () => {
    initializeScripts();
});

document.addEventListener("livewire:render", () => {
    initializeScripts();
});

// Function to initialize JavaScript logic
function initializeScripts() {
    console.log("JavaScript reinitialized.");
    // Add your custom JavaScript logic here
}
