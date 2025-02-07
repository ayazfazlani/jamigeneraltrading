<div class=" mx-auto p-6 z-0 flex-1 bg-white  shadow">
    <div class="flex justify-between items-center mb-4 text-gray-500">
        <h1 class="text-2xl font-semibold">Reports - Dashboard</h1>
        <p>{{ \Carbon\Carbon::now()->toFormattedDateString() }}</p>
    </div>
    <hr class="my-4" />

    <div class="grid grid-cols-3 gap-4 mb-4 text-gray-500">
        <div class="text-center">
            <p>Total</p>
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
    <p class="text-lg font-semibold text-gray-500">Total Inventory Level</p>

    <div class="border rounded-lg p-4 h-60">
        <h3 class="text-xl font-semibold text-gray-500">Total Inventory Chart</h3>
        <canvas id="totalInventoryChart"></canvas>
        @script
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
                        x: { title: { display: true, text: 'Item ID' }},
                        y: { title: { display: true, text: 'Quantity' }}
                    }
                }
            });
        </script>
        @endscript
    </div>
</div>