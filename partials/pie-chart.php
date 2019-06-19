<div class="pie-chart">
    <canvas id="pie-chart" height="408"></canvas>
    <script>

        <?php $data_points = db_pie_chart_euro(); ?>

        new Chart(document.getElementById("pie-chart"), {
            type: 'pie',
            data: {
                labels: <?php echo json_encode($data_points[0]);?>,
                datasets: [{
                    label: "Category",
                    backgroundColor: <?php echo json_encode($data_points[1]);?>,
                    data: <?php echo json_encode($data_points[2]);?>
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                title: {
                    display: true,
                    text: '<?php echo "Totaal €". $data_points[3]. ",- uitgegeven aan voedsel";?>',
                    fontSize: 16
                }
            }
        });
    </script>
</div>