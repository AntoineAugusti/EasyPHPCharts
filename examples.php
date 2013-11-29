<?php
require "Chart.php";
?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Charts</title>
	<link rel="stylesheet" href="chart.css">
	<style>
		*{margin: 0; padding: 0;}
		@import url(http://fonts.googleapis.com/css?family=Roboto);
		body{background: #FFF; font-family: 'Roboto', sans-serif;font-weight: 400}
		#content{background: #FFF; width: 1000px; padding: 20px; margin: 0 auto}
		h2{color: #4081BD; margin-bottom: 20px; font-weight: 400}
		.clearBoth:after{width: 300px; border: 1px solid #EEE; margin: 50px 0; display: block;}
		.containerChartLegend{width: 480px;padding-left: 20px}
	</style>
	<script src="ChartJS.min.js"></script>
</head>
<body>
	<div id="content">
		<?php
		/*
		//	A basic example of a pie chart
		*/
		$PieChart = new Chart('pie', 'examplePie');
		$PieChart->set('data', array(2, 10, 16, 30, 42));
		$PieChart->set('legend', array('Work', 'Eat', 'Sleep', 'Listen to music', 'Code'));
		$PieChart->set('displayLegend', true);
		echo $PieChart->returnFullHTML();

		/*
		//	An example of a doughnut chart with legend in percentages
		*/
		$DoughnutChart = new Chart('doughnut', 'exampleDoughnut');
		$DoughnutChart->set('data', array(2, 10, 16, 30, 42));
		$DoughnutChart->set('legend', array('Work', 'Eat', 'Sleep', 'Listen to music', 'Code'));
		$DoughnutChart->set('displayLegend', true);
		$DoughnutChart->set('legendIsPercentage', true);
		echo $DoughnutChart->returnFullHTML();

		/*
		//	An example of a bar chart with multiple datasets
		*/
		$BarChart = new Chart('bar', 'examplebar');
		$BarChart->set('data', array(array(2, 10, 16, 30, 42), array(42, 30, 16, 10, 2)));
		$BarChart->set('legend', array('01/01', '01/02', '01/03', '01/04', '01/05'));
		// We don't to use the x-axis for the legend so we specify the name of each dataset
		$BarChart->set('legendData', array('Annie', 'Marc'));
		$BarChart->set('displayLegend', true);
		echo $BarChart->returnFullHTML();

		/*
		//	An example of a radar chart
		*/
		$RadarChart = new Chart('radar', 'exampleradar');
		$RadarChart->set('data', array(20, 55, 16, 30, 42));
		$RadarChart->set('legend', array('A', 'B', 'C', 'D', 'E'));
		echo $RadarChart->returnFullHTML();

		/*
		//	An example of a polar chart
		*/
		$PolarChart = new Chart('polar', 'examplepolar');
		$PolarChart->set('data', array(20, 55, 16, 30, 42));
		$PolarChart->set('legend', array('A', 'B', 'C', 'D', 'E'));
		echo $PolarChart->returnFullHTML();
		?>
	</div>
</body>
</html>