<?php
/**
 *  Unofficial Google Weather API wrapper
 *  @author ATmega/Foxhoundz
 *  @version 0.6
 *  @license GPL version 3
 *  @link http://code.google.com/p/php-google-weather-wrapper/wiki/Documentation
 */
 
 
  class weather {
      private $_location;
      private $_url = 'http://www.google.com/ig/api?weather=';
      private $_isParsed = false;
      private $_wData;
      public $lastError;

      public function __construct( $location) {
          // Set location
          $this->_location = $location;
          
          // urlencode doesn't seem to work so manually add the + for whitespace
          $this->_url = preg_replace('/\s{1}/', '+',$this->_url .= $location);
          $this->parse_xml($this->get_xml());
      }

      public function get_temp($type = "f") {

          // User specificed celsius, return celsius
          if ($type == "c")
              return $this->_wData['current']['temp_c'];

          // return fahrenheit
          return $this->_wData['current']['temp_f'];
      }

      public function get_condition() {

          // provide current conditions only
          return $this->_wData['current']['condition'];
      }

      public function get_forecast_for_day($day) {

          
          return (isset($this->_wData['forecast'][$day])) ? $this->_wData['forecast'][$day] :false;
      }
      
      public function get_forecast_assoc() {

          return $this->_wData['forecast'];
      }

      public function get_cond_assoc() {

          return $this->_wData['current'];
      }

      public static function to_celsius($f) {
          // Convert Fahrenheit to Celsius.
          return floor(((int)$f - 32) * (5 / 9));
      }

      private function get_xml() {
          // Download raw XML to be parsed.
          $ch = curl_init($this->_url);
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
          $rawXML = curl_exec($ch);
          curl_close($ch);
          if (!$rawXML) throw new Exception("Unable to rerieve XML");
          return $rawXML;
      }

      private function parse_xml($xData) {
          libxml_use_internal_errors(true);
          try {
            $weather = new SimpleXMLElement($xData);
          } catch (Exception $err) {
              // Set current exception message last getMessage()
              throw new Exception($err->getMessage());  
          }
          // Select the current_conditions node ($cNode)
          if (!isset($weather->weather[0]->current_conditions)) {
              throw new Exception("Unable to find data for the specified location");
          }
		  
          $cNode = $weather->weather[0]->current_conditions;

          // ========= Set up our current conditions array ====================

          // Tempreature - temp_f Fahrenheit, temp_c celsius - set as floats.
          $this->_wData['current']['temp_f'] = $cNode->temp_f->attributes()->data;
          $this->_wData['current']['temp_c'] = weather::to_celsius($this->_wData['current']['temp_f']);

          // Condition
          $this->_wData['current']['condition'] = $cNode->condition->attributes()->data;

          // Condition Icon - icon url is not absolute, append google.com
          $this->_wData['current']['icon'] = "http://www.google.com" . $cNode->icon->attributes()->data;

          // Wind Condition
          $this->_wData['current']['wind'] = $cNode->wind_condition->attributes()->data;

          // ============= Set up our forecast array =============
          $fNode = $weather->weather[0]->forecast_conditions;

          // Iterate through each day of the week and create an assoc array.
          foreach ($fNode as $forecast) {
              // Get the day.
             $day = (string)$forecast->day_of_week->attributes()->data;
              // Insert an array of info for that day
              $this->_wData['forecast'][$day] = array (
                  "day" => $day,
                  "high" => $forecast->high->attributes()->data,
                  "low" => $forecast->low->attributes()->data,
                  "icon" => "http://www.google.com" . $forecast->icon->attributes()->data,
                  "condition" => $forecast->condition->attributes()->data
				);
          } //foreach ($fNode as $forecast)
          // Let the class know wData is ready for use.
      } //private function parse_xml($xData)   
 }
 $weather = new weather("Dallas");
// Get temperature in Fahrenheit
$temp_fahren = $weather->get_temp('f'); // Will assume 'f' if no parameter given

// Get temperature in celsius
$temp_celsius = $weather->get_temp('c');

// Output to user
echo "It's currently " . $temp_celsius ."C or " . $temp_fahren ."F."; 
?>