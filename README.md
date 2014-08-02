EasyPHPCharts
=============
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat)](LICENSE.md)

A simple PHP class for http://www.chartjs.org charts. Draw a beautiful chart with 5 lines of PHP!

Documentation
-------
You can find documentation at [antoineaugusti.github.io/EasyPHPCharts](http://antoineaugusti.github.io/EasyPHPCharts
). Take also a look at the file [``examples/examples.php``](https://github.com/AntoineAugusti/EasyPHPCharts/blob/master/examples/examples.php).

Examples
-------
```php
/*
//	A basic example of a pie chart
*/
$pieChart = new Chart('pie', 'examplePie');
$pieChart->set('data', array(2, 10, 16, 30, 42));
$pieChart->set('legend', array('Work', 'Eat', 'Sleep', 'Listen to music', 'Code'));
$pieChart->set('displayLegend', true);
echo $pieChart->returnFullHTML();

/*
//	An example of a doughnut chart with legend in percentages
*/
$doughnutChart = new Chart('doughnut', 'exampleDoughnut');
$doughnutChart->set('data', array(2, 10, 16, 30, 42));
$doughnutChart->set('legend', array('Work', 'Eat', 'Sleep', 'Listen to music', 'Code'));
$doughnutChart->set('displayLegend', true);
$doughnutChart->set('legendIsPercentage', true);
echo $doughnutChart->returnFullHTML();

/*
//	An example of a bar chart with multiple datasets
*/
$barChart = new Chart('bar', 'examplebar');
$barChart->set('data', array(array(2, 10, 16, 30, 42), array(42, 30, 16, 10, 2)));
$barChart->set('legend', array('01/01', '01/02', '01/03', '01/04', '01/05'));
// We don't to use the x-axis for the legend so we specify the name of each dataset
$barChart->set('legendData', array('Annie', 'Marc'));
$barChart->set('displayLegend', true);
echo $barChart->returnFullHTML();

/*
//	An example of a radar chart
*/
$radarChart = new Chart('radar', 'exampleradar');
$radarChart->set('data', array(20, 55, 16, 30, 42));
$radarChart->set('legend', array('A', 'B', 'C', 'D', 'E'));
echo $radarChart->returnFullHTML();

/*
//	An example of a polar chart
*/
$polarChart = new Chart('polar', 'examplepolar');
$polarChart->set('data', array(20, 55, 16, 30, 42));
$polarChart->set('legend', array('A', 'B', 'C', 'D', 'E'));
echo $polarChart->returnFullHTML();
```

CSS
-------
Some CSS classes will be added to the HTML produced. An example CSS file:

```css
.chartContainer 
{
	float: left;
	width: 500px;
}
.containerChartLegend
{
	float: right;
	width: 500px;
}
canvas.chart
{
	margin: 20px auto;
	display: block;
}
canvas.chart:after
{
	clear: both;
}
.colorBlock
{
	display: inline-block;
	width: 45px;
	height: 15px;
	vertical-align: middle;
	margin-right: 15px;
}
ul.chartLegend
{
	list-style: none;
	margin-bottom: 30px;
}
ul.chartLegend li
{
	margin-bottom: 5px;
}
.floatRight
{
	float: right;
}
.clearBoth
{
	clear: both;
}
```