<div class="bar-chart">
    <canvas id="bar-chart" height="408"></canvas>
    <script>

        <?php

        $data_points = db_bar_chart(NULL);

        $data_disposed = db_bar_chart('disposed');

        ?>


        new Chart(document.getElementById("bar-chart"), {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($data_points[0]) ?>,
                datasets: [
                    {
                        label: "Waarde gekocht voedsel in €",
                        backgroundColor: '#29B32E',
                        data: <?php echo json_encode($data_points[1]) ?>
                    },
                    {
                        label: "Waarde weggegooid voedsel in €",
                        backgroundColor: '#FF0000',
                        data: <?php echo json_encode($data_disposed[1])?>
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                legend: { display: false },
                title: {
                    display: true,
                    text: 'Waarde verhouding van gekocht en weggegooid voedsel',
                    fontSize: 16
                }
            }
        });
    </script>
</div>