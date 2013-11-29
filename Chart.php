<?php
/**
* PHP class to display charts from http://www.chartjs.org/
**/
class Chart
{
	/**
	* @var int Canvas' width, without legend = 1000
	**/
	const CANVAS_WIDTH_WITHOUT_LEGEND = 1000;
	/**
	* @var int Canvas' height, without légende = 300
	**/
	const CANVAS_HEIGHT_WITHOUT_LEGEND = 300;
	/**
	* @var int Canvas' width, with legend = 500
	**/
	const CANVAS_WIDTH_WITH_LEGEND = 500;
	/**
	* @var int Canvas' width, with legend = 300
	**/
	const CANVAS_HEIGHT_WITH_LEGEND = 300;
	/**
	* @var string Title of the legend = " block
	**/
	const TITLE_LEGEND_BLOCK = "Legend";

	/**
	* @var string $type Type of the chart. Could be : 'doughnut', 'polar', 'bar', 'pie', 'line', 'radar'
	**/
	private $type;
	/**
	* @var string $divName The ID of the div where the chart will go
	**/
	private $divName;
	/**
	* @var array $data Our data. Multidimensionnal array or not.
	**/
	private $data;
	/**
	* @var array $legend Data for the abscisse axis.
	**/
	private $legend;
	/**
	* @var array $legendData If you don't want to use data from the abscisse axis for the legend, you should specify your legend here.
	**/
	private $legendData;
	/**
	* @var string $options Chart's options. See http://www.chartjs.org for available options 
	**/
	private $options;
	/**
	* @var array $colors Multiple values of hexadecimal colors (example : #FFFFFF). They will be used for your charts. If you do not specify them, a default color array will be used.
	**/
	private $colors;
	/**
	* @var boolean $displayLegend Do we need to display a legend for the chart or not?
	*/
	private $displayLegend = false;
	/**
	* @var boolean $legendIsPercentage Indicates whether the legend should be displayed as a percentage
	**/
	private $legendIsPercentage = false;
	
	/**
	* @var int $maxColorKey The maximal key value of the colors array
	**/
	static $maxColorKey = 0;
	/**
	* @var int $numberCurrentColorKey The last key value of the colors array that we have used
	**/
	static $numberCurrentColorKey = 0;
	/**
	* @var boolean $hasLoopedThroughColors Do we have already looped through the colors array?
	*/
	static $hasLoopedThroughColors = false;
	/**
	* @var int $beginningKeyForData The key number with which we began our chart
	*/
	static $beginningKeyForData = 0;

	/**
	* Build a new chart object
	* @author Antoine AUGUSTI
	* @param string $type Type of the chart. Available type are: 'doughnut', 'polar', 'bar', 'pie', 'line', 'radar'
	* @param string $divName The ID of the div where the chart will go
	* @param string $options Options you want to use this chart. If null, the default options for the type of graphic will be applied.
	* @param array $colors Hexadecimal colors you want to use to represent the data in the chart. The order of the colors must match the order of the data that will be given later. If not specified, the default color codes are used.
	**/
	public function __construct($type, $divName, $options=null, $colors=null)
	{
		if (in_array($type, array('doughnut', 'polar', 'bar', 'pie', 'line', 'radar')))
			self::set('type', $type);
		else
			die();

		// Colors
		if (is_null($colors))
			self::set('colors', array("#F7464A", "#E2EAE9", "#D4CCC5", "#949FB1", "#4D5360", "#FF6600", "#4081BD", "#64992C", "#956188", "#DC6D7F", "#415E9B", "#C50000"));
		else
			self::set('colors', $colors);

		self::set('divName', $divName);
		
		// Default options
		if ($type != 'bar' AND is_null($options))
			self::set('options', 'null');
		if ($type == 'bar' AND is_null($options))
			self::set('options', '{scaleFontColor : "#767C8D",scaleGridLineColor : "rgba(0,0,0,.2)"}');
	}

	/**
	* Return the JS code for the chart
	* @author Antoine AUGUSTI
	* @return string The JS code for the chart
	**/
	public function returnJS()
	{
		/*
		//		doughnut, pie or polar
		*/
		if (in_array($this->type, array('doughnut', 'pie', 'polar')) AND !empty($this->data) AND !empty($this->legend))
		{
			$js = 'var data = [';

			foreach ($this->data as $datum)
				$js .= '{ value:'.$datum.', color:"'.self::getNextColor().'"},';

			$js .= '];';
		}

		/*
		//		bar, line or radar
		*/
		if (in_array($this->type, array('bar', 'line', 'radar')) AND !empty($this->data) AND !empty($this->legend))
		{
			$js = 'var data = {labels:[';

			foreach ($this->legend as $legend)
				$js .= '"'.$legend.'",';

			$js .= '], datasets:[';

			// If we have multiple datasets
			if (self::isMultidimensionalArray($this->data))
			{
				$error = false;
				
				// Let's check the dimension of each dataset
				foreach ($this->data as $dataset) 
				{
					if (count($dataset) != count($this->legend))
						$error = true;
				}

				if (count($this->colors) < count($this->data))
				{
					echo 'Not enough colors.';
					die();
				}

				if ($error)
				{
					echo "Wrong dimension for data.";
					die();
				}
				// We generate the JS code for each dataset
				else
				{
					$i = 0;
					foreach ($this->data as $dataset) 
					{
						$RGBcolor = self::HexadecimalColorToRGB(self::getNextColor());
						$js .= self::generateJSDataset($dataset, $RGBcolor);
						
						$i++;
					}
				}
			}
			// Single dataset
			else
			{
				if (count($this->data) != count($this->legend))
				{
					echo 'Wrong dimension for data.';
					die();
				}

				if (self::isHexadecimalColor($this->colors[0]))
					$RGBcolor = self::HexadecimalColorToRGB($this->colors[0]);
				else
				{
					echo 'The first color is not an hexadecimal color.';
					die();
				}

				$js .= self::generateJSDataset($this->data, $RGBcolor);
			}

			$js .= ']};';
		}

		$js .= '
		graphique'.$this->divName.'();

		function graphique'.$this->divName.'(){
			var ctx = document.getElementById("'.$this->divName.'").getContext("2d");
			new Chart(ctx).'.self::chartNameJS().'(data,'.$this->options.');
		};';

		return $js;
	}

	/**
	* Return the HTML code for the legend of the chart. If your chart is a bar chart, you can specify a different legend from the x-axis by using using 'legendData' 
	* @author Antoine AUGUSTI
	* @return string The HTML code for the legend of the chart
	**/
	public function returnLegend()
	{
		// Go to the beginning of the colors array
		self::$numberCurrentColorKey = self::$beginningKeyForData;
		// We do not have looped through the colors yet
		self::$hasLoopedThroughColors = false;

		if ($this->legendIsPercentage)
			$percentage = ' %';
		else
			$percentage = '';

		if (in_array($this->type, array('doughnut', 'pie', 'polar')) AND !empty($this->legend) AND !empty($this->data))
		{
			$html = '<ul class="chartLegend">';
			$i = 0;

			foreach ($this->data as $datum) 
			{
				$color = self::getNextColor();
				
				// We do not display zeros'
				if ($datum != 0)
					$html .= '<li><div class="colorBlock" style="background-color:'.$color.'"></div>'.$this->legend[$i].'<span class="floatRight">'.$datum.$percentage.'</span></li>';
				
				$i++;
			}

			$html .= '</ul>';

			return $html;
		}
		elseif ($this->type == 'bar' AND (!empty($this->legend) OR !empty($this->legendData)) AND !empty($this->data))
		{
			if (!empty($this->legendData))
				$legends = $this->legendData;
			else
				$legends = $this->legend;

			$html = '<ul class="chartLegend">';

			foreach ($legends as $legend)
			{
				$color = self::getNextColor();
				$html .= '<li><div class="colorBlock" style="background-color:'.$color.'"></div>'.$legend.$percentage.'</li>';
			}

			$html .= '</ul>';

			return $html;
		}
		else
		{
			echo 'Error in returnLegend().';
			die();
		}
	}

	/**
	* Return the full HTML code for our chart
	* @author Antoine AUGUSTI
	* @return string The full HTML code for our chart
	**/
	public function returnFullHTML()
	{
		$jsCode = self::returnJS();

		if (!$this->displayLegend)
			$html = '<canvas class="chart" id="'.$this->divName.'" width="'.self::CANVAS_WIDTH_WITHOUT_LEGEND.'" height="'.self::CANVAS_HEIGHT_WITHOUT_LEGEND.'"></canvas>';
		else
		{
			$html = '<div class="chartContainer"><canvas class="chart" id="'.$this->divName.'" width="'.self::CANVAS_WIDTH_WITH_LEGEND.'" height="'.self::CANVAS_HEIGHT_WITH_LEGEND.'"></canvas></div>';
			$html .= '<div class="containerChartLegend"><h2>'.self::TITLE_LEGEND_BLOCK.'</h2>'.self::returnLegend().'</div>';
		}

		$html .= '<div class="clearBoth"></div><script>'.$jsCode.'</script>';
		
		return $html;
	}

	/**
	* Convert an hexadecimal color to a RGB color
	* @author Antoine AUGUSTI
	* @param string $color The hexadecimal color code. Example: '#FFFFFF'
	* @return string The RGB color code. Format: "[0-255],[0-255],[0-255]"
	**/
	private function HexadecimalColorToRGB($color)
	{
		if (self::isHexadecimalColor($color))
		{
			$hex_R = substr($color, 1, 2);
			$hex_G = substr($color, 3, 2);
			$hex_B = substr($color, 5, 2);
			
			return hexdec($hex_R).",".hexdec($hex_G).",".hexdec($hex_B);
		}
		else
		{
			echo $color." is not an hexadecimal color code.";
			die();
		}
	}

	/**
	* Return the next color code from the colors array. If we are at the end of the array, go back to the first color.
	* @author Antoine AUGUSTI
	* @return string Le code couleur suivant au format héxadécimal
	**/
	private function getNextColor()
	{
		$numberNextColorKey = self::$numberCurrentColorKey + 1;

		// If we haven't looped through the colors yet and the next color key is 1, we must use color 0
		if ($numberNextColorKey > self::$maxColorKey OR ($numberNextColorKey == 1 AND !self::$hasLoopedThroughColors))
		{
			// We have looped through the colors array
			self::$hasLoopedThroughColors = true;

			$numberNextColorKey = 0;
		}

		self::$numberCurrentColorKey = $numberNextColorKey;

		return $this->colors[$numberNextColorKey];
	}

	/**
	* Generate the JS code for a bar, line or radar chart.
	* @author Antoine AUGUSTI
	* @param array $data Data for this dataset
	* @param string $color The color associated with this dataset
	* @return string The JS code for the chart
	**/
	private function generateJSDataset($data, $color)
	{
		if (in_array($this->type, array('bar', 'line', 'radar')) AND !self::isMultidimensionalArray($data) AND !self::isHexadecimalColor($color))
		{
			// Colors for the dataset
			$js = '{
				fillColor: "rgba('.$color.',0.7)",
				strokeColor: "rgba('.$color.',1)",
				pointColor: "rgba('.$color.',1)",
				pointStrokeColor:"#FFF",
				data:[';

			// Data for the dataset
			foreach ($data as $datum)
				$js .= $datum.',';

			$js .= ']},';

			return $js;
		}
		else
		{
			echo 'Error in the data in generateJSDataset.';
			die();
		}
	}

	/**
	* Check if a string represents an hexadecimal color or not
	* @author Antoine AUGUSTI
	* @param string $color The hexadecimal color code. Example: '#FFFFFF'
	* @return boolean true if the string represents an hexadecimal color, false otherwise
	**/
	private function isHexadecimalColor($color)
	{
		return preg_match("/^[#]([0-9a-fA-F]{6})$/", $color);
	}

	/**
	* Check if an array is multidimensional or not
	* @author Antoine AUGUSTI
	* @param array $array The array to test
	* @return boolean true if the array is a multidimensional array, false otherwise
	**/
	private function isMultidimensionalArray($array)
	{
		return count($array) != count($array, COUNT_RECURSIVE);
	}

	/**
	* Return the name of the chart for the JS code
	* @author Antoine AUGUSTI
	* @return string The name of the chart for the JS code
	**/
	private function chartNameJS()
	{
		switch ($this->type) 
		{
			case 'doughnut':
				return 'Doughnut';
				break;

			case 'polar':
				return 'PolarArea';
				break;

			case 'bar':
				return 'Bar';
				break;

			case 'pie':
				return 'Pie';
				break;

			case 'line':
				return 'Line';
				break;

			case 'radar':
				return 'Radar';
				break;
			
			default:
				return false;
				break;
		}
	}

	/**
	* Return a value of the object
	* @author Antoine AUGUSTI
	* @param string $key Name of the key
	* @return mixed The value
	**/
	private function get($key)
	{
		if (isset($this->$key))
			return $this->$key;
		else
			return false;
	}

	/**
	* Set an attribute of the object
	* @author Antoine AUGUSTI
	* @param string $key Name of the key
	* @param mixed $value The value
	**/
	public function set($key, $value)
	{
		if (in_array($key, array('data', 'legend', 'legendData')) AND is_array($value) AND !empty($value))
			$this->$key = $value;

		if (in_array($key, array('displayLegend', 'legendIsPercentage')) AND !is_bool($value))
		{
			echo $key.' must be a boolean.';
			die();
		}

		if ($key == 'colors')
		{
			if (is_array($value) AND !self::isMultidimensionalArray($value))
			{
				foreach ($value as $color) 
				{
					if (!self::isHexadecimalColor($color))
					{
						echo $color.' is not a valid color.';
						die();
					}
				}

				$this->colors = $value;
				self::$maxColorKey = count($value) - 1;
				self::$beginningKeyForData = self::$numberCurrentColorKey;
			}
		}
		elseif (!in_array($key, array('data', 'legend')))
			$this->$key = $value;
	}
}