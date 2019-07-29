> The headline must be the project name, followed by relevant badges (TravisCI builds, codecoverage, or other relevant badges)


# Opengraph parser [![Build status](https://travis-ci.org/ArnsboMedia/opengraph_parser.svg)](https://travis-ci.org/ArnsboMedia/opengraph_parser) [![Code Climate](https://codeclimate.com/github/ArnsboMedia/opengraph_parser/badges/gpa.svg)](https://codeclimate.com/github/ArnsboMedia/opengraph_parser) [![Test Coverage](https://codeclimate.com/github/ArnsboMedia/opengraph_parser/badges/coverage.svg)](https://codeclimate.com/github/ArnsboMedia/opengraph_parser)

This parser is meant to parse opengraph content from a page.

## Goals

 - easy to use for novice developers
 - easy to extend for experienced developers
 - accurate parsing of og meta tags according to spec
 - 100% code coverage with tests

## Requirements
- [PHP](http://php.net/) >= 5.5 
- [Composer](https://getcomposer.org)

## Installation
To use the package add the following to your composer.json
```
"arnsbomedia/opengraph_parser": "0.0.9",
```

Alternatively setup the code itself
```shell
$ scripts/setup
```

## Test
```shell
$ scripts/test
```

## Help
 - please write tests for all your contributions
