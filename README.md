Library for simple use OData with POData library
================================================

What is this? <a name="what"></a>
-------------

Implementation of DataService and QueryProvider classes, what helps to make easy start with [OData](http://www.odata.org/).

### Who is using it?

- It's instrument for PHP developers, who want use OData with minimum efforts.


Installation <a name="installation"></a>
------------

[PHP 5.4 or higher](http://www.php.net/downloads.php) is required to use it.

Installation is recommended to be done via [composer][]. Add the following to the `require` and `repositories` sections in `composer.json` of Yii2 project:

```json
    "require": {
       "mnvx/SimplePOData": ">=0.9.1"
    },
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/mnvx/POData"
        },
        {
            "type": "vcs",
            "url": "https://github.com/mnvx/SimplePOData"
        }
    ]
```

Run `composer update` afterwards.

[composer]: https://getcomposer.org/ "The PHP package manager"


Usage <a name="usage"></a>
-----

### TODO: to be documented

Try to open links:

 - http://<youproject>/odata.svc/$metadata

 - http://<youproject>/odata.svc/Products?$format=json&$skip=1&$top=14&$inlinecount=allpages&$filter=code eq 'book'

 - http://<youproject>/odata.svc/Products/$count?&$filter=code eq 'book'

 - http://<youproject>/odata.svc/Products(2465)

For more details about URL format, see [OData documentation](http://www.odata.org/documentation/odata-version-2-0/uri-conventions/).

### Am I free to use this?

This library is open source and licensed under the [MIT License][]. This means that you can do whatever you want
with it as long as you mention my name and include the [license file][license]. Check the [license][] for details.

[MIT License]: http://opensource.org/licenses/MIT

[license]: https://github.com/mnvx/SimplePOData/blob/master/LICENSE

Contact
-------

Feel free to contact me using [email](mailto:mnvx@yandex.ru).
