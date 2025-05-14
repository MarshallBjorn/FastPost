@extends('layouts.admin')

@section('content')
    <div class="w-9/12 mx-auto mb-10">
        <h1 class="font-bold text-2xl mt-4 mb-1">Packages plots.</h1>
        <div class="grid grid-cols-2 w-full p-2 border-2 border-dotted gap-4">

            <div class="max-h-[12rem]">
                <h2>Package Status Distribution</h2>
                <canvas id="statusChart"></canvas>
            </div>

            <div>
                <h2>Packages Sent Per Day</h2>
                <canvas id="packagesPerDayChart"></canvas>
            </div>

            <div>

                <h2>Start Postmat Usage</h2>
                <canvas id="startPostmatChart"></canvas>
            </div>
            <div>

                <h2>Destination Postmat Usage</h2>
                <canvas id="destPostmatChart"></canvas>
            </div>
            <div>
                <h2>Staff Per Warehouse</h2>
                <canvas id="staffChart"></canvas>

            </div>
            <div>
                <h2>Stashes Per Postmat</h2>
                <canvas id="stashChart"></canvas>
            </div>
            <div>
                <h2>Delivery Events Per Day</h2>
                <canvas id="actualizationChart"></canvas>

            </div>
            <div>
                <h2>Packages Handled Per Courier</h2>
                <canvas id="courierChart"></canvas>
            </div>

        </div>

        <script>
            Chart.defaults.font.family = "'Courier Prime', monospace";

            function generateColors(count) {
                const baseColors = ['#36A2EB', '#FF6384', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40', '#8E44AD', '#27AE60',
                    '#2ECC71'
                ];
                const colors = [];

                for (let i = 0; i < count; i++) {
                    colors.push(baseColors[i % baseColors.length]);
                }

                return colors;
            }

            function createChart(id, type, labels, data, label = '', color = '#36A2EB') {
                const dataset = {
                    label: label,
                    data: data,
                    borderWidth: 1,
                    fill: type === 'line' ? false : true,
                };

                if (type === 'pie' || type === 'doughnut') {
                    // Use different colors for each segment
                    dataset.backgroundColor = generateColors(data.length);
                    dataset.borderColor = '#fff'; // optional: adds white border between segments
                } else {
                    dataset.backgroundColor = color;
                    dataset.borderColor = color;
                }

                new Chart(document.getElementById(id), {
                    type: type,
                    data: {
                        labels: labels,
                        datasets: [dataset]
                    },
                });
            }

            createChart('statusChart', 'pie', {!! json_encode($packageCountsByStatus->keys()) !!}, {!! json_encode($packageCountsByStatus->values()) !!});
            createChart('packagesPerDayChart', 'line', {!! json_encode($packagesPerDay->pluck('date')) !!}, {!! json_encode($packagesPerDay->pluck('total')) !!}, 'Packages Sent');
            createChart('startPostmatChart', 'bar', {!! json_encode($startPostmatCounts->keys()) !!}, {!! json_encode($startPostmatCounts->values()) !!}, 'Packages Started');
            createChart('destPostmatChart', 'bar', {!! json_encode($destPostmatCounts->keys()) !!}, {!! json_encode($destPostmatCounts->values()) !!}, 'Packages Destined');
            createChart('staffChart', 'bar', {!! json_encode($staffByWarehouse->keys()) !!}, {!! json_encode($staffByWarehouse->values()) !!}, 'Staff Count');
            createChart('stashChart', 'bar', {!! json_encode($stashesByPostmat->keys()) !!}, {!! json_encode($stashesByPostmat->values()) !!}, 'Stashes');
            createChart('actualizationChart', 'line', {!! json_encode($actualizationsPerDay->pluck('date')) !!}, {!! json_encode($actualizationsPerDay->pluck('total')) !!}, 'Delivery Events');
            createChart('courierChart', 'bar', {!! json_encode($couriers->keys()) !!}, {!! json_encode($couriers->values()) !!}, 'Packages Handled');
        </script>

    </div>
@endsection
