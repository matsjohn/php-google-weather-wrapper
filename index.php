<?php
/**
 * @author Foxhoundz
 * @version 0.3
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
      }
      
      public function get_wData() {
        $this->parse_xml($this->get_xml());
        if ($this->_isParsed) return true;
      }

      public function get_temp($type = "f") {
          if (!$this->_isParsed)
              return false;

          // User specificed celsius, return celsius
          if ($type == "c")
              return $this->_wData['current']['temp_c'];

          // return fahrenheit
          return $this->_wData['current']['temp_f'];
      }

      public function get_condition() {
          if (!$this->_isParsed)
                  return false;
          // provide current conditions only
          return $this->_wData['current']['condition'];
      }

      public function get_forecast_for_day($day) {
          if (!$this->_isParsed)
              return false;
          return $this->_wData['forecast'][$day];
      }
      public function get_forecast_assoc() {
          if (!$this->_isParsed)
              return false;
          return $this->_wData['forecast'];
      }

      public function get_cond_assoc() {
          if (!$this->_isParsed)
              return false;
          return $this->_wData['current'];
      }

      public function dump_wData() {
          if (!$this->_isParsed)
              return false;
          return $this->_wData;
      }

      public static function to_celsius($f) {
          // Convert Fahrenheit to Celsius.
          // I figured this would be quicker than trying to parse the XML.
          return floor(((int)$f - 32) * (5 / 9));
      }

      private function get_xml() {
          // Download raw XML to be parsed.
          $ch = curl_init($this->_url);

          // I don't know why I altered the useragent. It must have been for a good reason. Oh well.
          curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; rv:5.0.1) Gecko/20100101 Firefox/5.0.1');
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
          $rawXML = curl_exec($ch);

          if (!$rawXML)
              return false;
          curl_close($ch);
          return $rawXML;
      }

      private function parse_xml($xData) {
          libxml_use_internal_errors(true);
          try {
            $weather = new SimpleXMLElement($xData);
          } catch (Exception $err) {
              // Set $lastError to getMessage()
              $this->lastError = $err->getMessage();
             return false;
          }
          // Select the current_conditions node ($cNode)
          $cNode = $weather->weather[0]->current_conditions;

          // ========= Set up our current conditions array ====================

          // Tempreature - temp_f Fahrenheit, temp_c celsius - set as floats.
          $this->_wData['current']['temp_f'] = (float)$cNode->temp_f->attributes()->data;
          $this->_wData['current']['temp_c'] = weather::to_celsius($this->_wData['current']['temp_f']);

          // Condition
          $this->_wData['current']['condition'] = (string)$cNode->condition->attributes()->data;

          // Condition Icon - icon url is not absolute, append google.com
          $this->_wData['current']['icon'] = (string)"http://www.google.com" . $cNode->icon->attributes()->data;

          // Wind Condition
          $this->_wData['current']['wind'] = (string)$cNode->wind_condition->attributes()->data;

          // ============= Set up our forecast array =============
          $fNode = $weather->weather[0]->forecast_conditions;

          // Iterate through each day of the week and create an assoc array.
          foreach ($fNode as $forecast) {
              // Get the day.
              $day = (string)$forecast->day_of_week->attributes()->data;

              // Insert an array of info for that day
              $this->_wData['forecast'][$day] = array (
                  "high" => (float)$forecast->high->attributes()->data,
                  "low" => (float)$forecast->low->attributes()->data,
                  "icon" => (string)"http://www.google.com" . $forecast->icon->attributes()->data,
                  "condition" => (string)$forecast->condition->attributes()->data
				);
          } //foreach ($fNode as $forecast)
          // Let the class know wData is ready for use.
          $this->_isParsed = true;
      } //private function parse_xml($xData)
      
 }

?>