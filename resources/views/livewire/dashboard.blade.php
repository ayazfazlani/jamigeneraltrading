<div class="container mx-auto p-6 z-0 flex-1 bg-white rounded-lg shadow">
    <div class="flex justify-between items-center mb-4 text-gray-500">
        <h1 class="text-2xl font-bold">Reports</h1>
        <p>{{ \Carbon\Carbon::now()->toFormattedDateString() }}</p>
    </div>
    <hr class="my-4" />

    <div class="grid grid-cols-3 gap-4 mb-4 text-gray-500">
        <div class="text-center">
            <p>Total Inventory</p>
            <h2 class="text-2xl font-bold">{{ $summary['totalInventory'] }}</h2>
        </div>
        <div class="text-center">
            <p>Stock In</p>
            <h2 class="text-2xl font-bold">{{ $summary['stockIn'] }}</h2>
        </div>
        <div class="text-center">
            <p>Stock Out</p>
            <h2 class="text-2xl font-bold">{{ $summary['stockOut'] }}</h2>
        </div>
    </div>

    <hr class="my-4" />
    <p class="text-lg font-semibold text-gray-500">Yesterday</p>

    <div class="grid grid-cols-3 gap-4">
        <!-- Total Inventory Line Chart -->
        <div class="border rounded-lg p-4 h-60">
            <h3 class="text-xl font-semibold text-gray-500">Total Inventory Level</h3>
            <div id="totalInventoryChart">
                <script>
                    const totalInventoryData = @json($totalInventoryData);
                    const totalInventoryChart = new Chart(document.getElementById("totalInventoryChart"), {
                        type: "line",
                        data: {
                            labels: totalInventoryData.map(item => item.name),
                            datasets: [{
                                label: "Total Inventory",
                                data: totalInventoryData.map(item => item.quantity),
                                borderColor: "blue",
                                fill: false
                            }]
                        },
                        options: {
                            responsive: true,
                            scales: {
                                x: { title: { display: true, text: 'Inventory Item' }},
                                y: { title: { display: true, text: 'Quantity' }}
                            }
                        }
                    });
                </script>
            </div>
        </div>

        <!-- Stock In Line Chart -->
        <div class="border rounded-lg p-4 h-60">
            <h3 class="text-xl font-semibold text-gray-500">Stock In</h3>
            <div id="stockInChart">
                <script>
                    const stockInData = @json($stockInData);
                    const stockInChart = new Chart(document.getElementById("stockInChart"), {
                        type: "line",
                        data: {
                            labels: stockInData.map(item => item.name),
                            datasets: [{
                                label: "Stock In",
                                data: stockInData.map(item => item.quantity),
                                borderColor: "green",
                                fill: false
                            }]
                        },
                        options: {
                            responsive: true,
                            scales: {
                                x: { title: { display: true, text: 'Stock In Item' }},
                                y: { title: { display: true, text: 'Quantity' }}
                            }
                        }
                    });
                </script>
            </div>
        </div>

        <!-- Stock Out Line Chart -->
        <div class="border rounded-lg p-4 h-60">
            <h3 class="text-xl font-semibold text-gray-500">Stock Out</h3>
            <div id="stockOutChart">
                <script>
                    const stockOutData = @json($stockOutData);
                    const stockOutChart = new Chart(document.getElementById("stockOutChart"), {
                        type: "line",
                        data: {
                            labels: stockOutData.map(item => item.name),
                            datasets: [{
                                label: "Stock Out",
                                data: stockOutData.map(item => item.quantity),
                                borderColor: "red",
                                fill: false
                            }]
                        },
                        options: {
                            responsive: true,
                            scales: {
                                x: { title: { display: true, text: 'Stock Out Item' }},
                                y: { title: { display: true, text: 'Quantity' }}
                            }
                        }
                    });
                </script>
            </div>
        </div>
    </div>
</div>

<!-- Include Chart.js (or any other charting library) -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

