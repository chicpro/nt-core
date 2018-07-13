<?php
require_once './_common.php';

$html->setPageTitle(_('Dashboard'));
$html->addJavaScript('https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.min.js', 'header', 10);

require_once NT_ADMIN_PATH.DIRECTORY_SEPARATOR.'header.php';

$labels        = array();
$postCounts    = array();
$commentCounts = array();
$visitCounts   = array();

for ($i = 13; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-{$i} days", NT_TIME_SERVER));

    $sql = " select count(*) as cnt from `{$nt['board_table']}` where substring(bo_date, 1, 10) = :bo_date ";
    $DB->prepare($sql);
    $DB->execute([':bo_date' => $date]);
    $postCount = $DB->fetchColumn();

    $sql = " select count(*) as cnt from `{$nt['board_comment_table']}` where substring(cm_date, 1, 10) = :cm_date ";
    $DB->prepare($sql);
    $DB->execute([':cm_date' => $date]);
    $commentCount = $DB->fetchColumn();

    $sql = " select count(*) as cnt from `{$nt['visit_table']}` where vi_date = :vi_date ";
    $DB->prepare($sql);
    $DB->execute([':vi_date' => $date]);
    $visitCount = $DB->fetchColumn();

    $labels[]        = substr($date, 5);
    $postCounts[]    = $postCount;
    $commentCounts[] = $commentCount;
    $visitCounts[]   = $visitCount;
}
?>

<div class="col mb-4">
    <h4 class="h4"><?php echo _('Posting Status'); ?></h4>

    <canvas class="mt-2 w-100" id="postChart" height="300"></canvas>
</div>

<div class="col">
    <h4 class="h4"><?php echo _('Visiting Status'); ?></h4>

    <canvas class="mt-2 w-100" id="visitChart" height="300"></canvas>
</div>

<script>
var options = {
        responsive: true,
        hoverMode: 'index',
		stacked: false,
        scales: {
            yAxes: [
                {
                    type: 'linear',
                    ticks: {
                        beginAtZero: false
                    }
                }
            ]
        }
    };

var ctx = document.getElementById("postChart");
var postChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: ["<?php echo implode('", "', $labels); ?>"],
        datasets: [
            {
                label: "<?php echo _('Posts'); ?>",
                fill: false,
                data: [<?php echo implode(',', $postCounts); ?>],
                lineTension: 0,
                backgroundColor: 'transparent',
                borderWidth: 2,
                borderColor: '#68bff9',
                pointBackgroundColor: '#68bff9',
                backgroundColor: '#68bff9'
            },
            {
                label: "<?php echo _('Comments'); ?>",
                fill: false,
                data: [<?php echo implode(',', $commentCounts); ?>],
                lineTension: 0,
                backgroundColor: 'transparent',
                borderWidth: 2,
                borderColor: '#fc7f55',
                pointBackgroundColor: '#fc7f55',
                backgroundColor: '#fc7f55'
            }
        ]
    },
    options: options
});

var ctx = document.getElementById("visitChart");
var visitChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: ["<?php echo implode('", "', $labels); ?>"],
        datasets: [
            {
                label: "<?php echo _('Visits'); ?>",
                fill: false,
                data: [<?php echo implode(',', $visitCounts); ?>],
                lineTension: 0,
                backgroundColor: 'transparent',
                borderWidth: 2,
                borderColor: '#30d185',
                pointBackgroundColor: '#30d185',
                backgroundColor: '#30d185'
            }
        ]
    },
    options: options
});
</script>


<?php
require_once NT_ADMIN_PATH.DIRECTORY_SEPARATOR.'footer.php';
?>