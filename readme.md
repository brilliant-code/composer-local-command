## Installation

```bash
composer global require osbre/composer-local-packages:dev-master
```

## Usage

Inside your project, run this command

```bash
composer local ../path-to-local-package
```

The composer.json file will now look like this:

```json
{
    "require": {
        ...
        "local-package/for-development": "dev-master"
    },
    "repositories": [
        {
            "type": "path",
            "version": "dev-master",
            "url": "/Users/you/local-package-for-development"
        }
    ],
    "autoload": {
        ...
    },
    ...
}
```

## TODO

- Write tests
- Add two commands `composer local require {path}`, and `composer local remove {path}`
- Add third parameter - custom package version

## Contributing

Pull-requests are welcome :D
