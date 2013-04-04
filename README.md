# silverstripe-googleanalytics

A thin SilverStripe helper for the php-ga library

# Installation (with composer)

```bash
$ composer require heyday/silverstripe-googleanalytics:~0.1
```
# Usage

## Config
```php
SSGoogleAnalytics::setDomain('heyday.co.nz');
SSGoogleAnalytics::setTrackingCode('UA-11111111-1');
SSGoogleAnalytics::setLoggingCallback(function ($request, $response) {
	mail(
	    'stevie@heyday.co.nz',
	    'Analytics log - dev',
	    $request,
	    "Content-type: text/html\nFrom: dev@heyday.net.nz"
	);
});
```
## Track Page View

```php
use UnitedPrototype\GoogleAnalytics;

$analytics = new SSGoogleAnalytics();
$pageView = new GoogleAnalytics\Page('heyday.net.nz');
$analytics->trackPageview($pageView);
```
## Track Event

```php
use UnitedPrototype\GoogleAnalytics;

$analytics = new SSGoogleAnalytics();
$event = new GoogleAnalytics\Event('Category', 'Action', 'Label', 'Value', 'NonInteraction');
$analytics->trackEvent($event);
```
## Track Transaction
```php
use UnitedPrototype\GoogleAnalytics;

$analytics = new SSGoogleAnalytics();
$transactionAnalytics = new GoogleAnalytics\Transaction();
$transactionAnalytics->setOrderId($orderID);
$transactionAnalytics->setAffiliation($affiliation);
$transactionAnalytics->setTotal($total);
$transactionAnalytics->setTax($taxTotal);
$transactionAnalytics->setShipping($shippingTotal);
$transactionAnalytics->setCity($city);
$transactionAnalytics->setRegion($region);
$transactionAnalytics->setCountry($country);

$items = array();
foreach ($products as $product) {               
    $items[$product->ProductCode] = new GoogleAnalytics\Item();
    $items[$product->ProductCode]->setOrderId($orderID); 
    $items[$product->ProductCode]->setSku($product->ProductCode);
    $items[$product->ProductCode]->setName($product->Title);
    $items[$product->ProductCode]->setVariation($product->Category);
    $items[$product->ProductCode]->setPrice($product->Price);
    $items[$product->ProductCode]->setQuantity($product->Quantity);
    $items[$product->ProductCode]->validate();
    $transactionAnalytics->addItem($items[$product->ProductCode]);               
}

$analytics->trackTransaction($transactionAnalytics);
```

# Unit testing
```bash
$ composer install --dev
$ vendor/bin/phpunit
```