# Manual #

Function list:
  * **constructor($location)** -Takes one parameter, either a city name or ZIP to retrieve the weather conditions.

  * **get\_temp($type)** - Returns the current temperature, default Fahrenheit. It can return in Celsius if supplied with 'c' as a parameter (See example 2)

  * **get\_condition()** - Returns an associative array of the current weather conditions. (See example 3)

  * **get\_forecast\_for\_day($day)** - Takes one parameter as the day of the week ("Mon","Tue","Wed","Thu","Fri","Sat","Sun"). Returns an associative array with the forecast for that day. Returns false if no forecast available for that day. ( See example 4)

  * **get\_forecast\_assoc()** - Returns an associative array of the four-day forecast. (see example 1).

  * **get\_cond\_assoc()** - Returns an associative array of current conditions, containing the following information:
    * _temp\_f_ - Temperature in Fahrenheit
    * _temp\_c_ - Temperature in Celcius
    * _condition_ - Current condition
    * _icon_ - Condition icon
    * _wind_ - Current wind conditions






## Example 1: Get an associative array of the 4-5 day forecast ##
```
 $weather = new weather("Dallas");

// Download the latest weather data and parse. 
echo $weather->get_condition();

// Show the entire 4-5 day forecast
var_dump($weather->get_forecast_assoc());
?>

```

Output:
```
array
  'Fri' => 
    array
      'high' => string '99' (length=2)
      'low' => string '79' (length=2)
      'icon' => string 'http://www.google.com/ig/images/weather/mostly_sunny.gif' (length=56)
      'condition' => string 'Mostly Sunny' (length=12)
  'Sat' => 
    array
      'high' => string '99' (length=2)
      'low' => string '79' (length=2)
      'icon' => string 'http://www.google.com/ig/images/weather/chance_of_storm.gif' (length=59)
      'condition' => string 'Chance of Storm' (length=15)
  'Sun' => 
    array
      'high' => string '101' (length=3)
      'low' => string '81' (length=2)
      'icon' => string 'http://www.google.com/ig/images/weather/sunny.gif' (length=49)
      'condition' => string 'Clear' (length=5)
  'Mon' => 
    array
      'high' => string '103' (length=3)
      'low' => string '81' (length=2)
      'icon' => string 'http://www.google.com/ig/images/weather/mostly_sunny.gif' (length=56)
      'condition' => string 'Mostly Sunny' (length=12)
```




## Example 2: Get current temperature ##
```
$weather = new weather("Los Angeles");

// Get temperature in Fahrenheit
$temp_fahren = $weather->get_temp('f'); // Will assume 'f' if no parameter given

// Get temperature in celsius
$temp_celsius = $weather->get_temp('c');

// Output to user
echo "It's currently " . $temp_celsius ."C or " . $temp_fahren ."F.";
```

Output:
```
"It's currently 20C or 68F."
```



## Example 3: Get current conditions ##
```
$weather = new weather("Seattle");

echo $weather->get_condition();
```
Output:
```
"Overcast"
```


## Example 4: Get forecast for a day ##
```
$weather = new weather("Seattle");

var_dump($weather->get_forecast_for_day(weather::FRIDAY));
```

Output:
```
array
  'day' => string 'Fri' (length=3)
  'high' => int 76
  'low' => int 58
  'icon' => string 'http://www.google.com/ig/images/weather/mostly_sunny.gif' (length=56)
  'condition' => string 'Mostly Sunny' (length=12)
```