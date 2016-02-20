# Laasti/Directions

A HTTP message router using nikic's FastRoute

## Features

* Use any callable as a controller, or use a Interop container value
* Use strategies to change how your routes are handled
* Add middlewares (callables) to your routes if your strategy supports it (requires laasti/peels)
* Personalize routes with attributes to help passing parameters to your controllers. Attributes are automatically distributes to the request's attributes


## Todo

* League container service provider
* RouterAwareTrait with inflector
* Name routes
* Group routes
* Generate URLs from routes
* Routes across multiple domains
* Add your own parsing formats ie hex

## Installation

```
composer require laasti/directions
```

## Usage

TODO

## Contributing

1. Fork it!
2. Create your feature branch: `git checkout -b my-new-feature`
3. Commit your changes: `git commit -am 'Add some feature'`
4. Push to the branch: `git push origin my-new-feature`
5. Submit a pull request :D

## History

See Github's releases

## Credits

Author: Sonia Marquette (@nebulousGirl)

## License

Released under the MIT License. See LICENSE.txt file.



