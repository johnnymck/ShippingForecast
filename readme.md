ShippingForecast.php
====================
A straight-forward shipping forecast API written in pure PHP which scrapes up-to-date info from the BBC shipping forecast webpage.

Source based on [Ted Nyman's shipping forecast project](https://github.com/tnm/shipping-forecast). Mega props to that guy! :)

Installation
------------
`composer install johnnymck/shippingforecast`

Usage
-----
```php
use johnnymck\ShippingForecast;

$forecast = new ShippingForecast();
$cromarty = $forecast->get('Cromarty'); // location names must be capitalised

echo $cromarty['time']; // eg,  'The general synopsis at 1800'...
print_r($cromarty['content']['warning']); // eg, []
echo $cromarty['content']['visibility']; // eg, 'Good, occasionally poor'...
```

It's worth noting that `ShippingForecast::get($foo)` and `ShippingForecast::getAll()` both yeild an assoc array containing `['time']`, the time as a string when last updated by the BBC, and `['content']` which contains the forecast-proper, including a `['warning']` array to yeild any prevalent warning information. See `examples/` for further information.

Fun Stuff!
----------
Running `php examples/readforecast.php` and piping the output into a text-to-speech application (such as `say` on Macintosh) will read the latest forecast update without you having to bother tuning your wireless sets to BBC R4, 3 times a day. What a time to be alive!

`./examples/forecast [zone here]` will return the forecast in plaintext in your desired zone