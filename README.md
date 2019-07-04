> The headline must be the project name, followed by relevant badges (TravisCI builds, codecoverage, or other relevant badges)


# Project Name [![Build Status](https://travis-ci.com/ArnsboMedia/systemet.svg?token=n7HfC44DzNaKKMLUT1pH&branch=master)](https://travis-ci.com/ArnsboMedia/systemet) [![codecov](https://codecov.io/gh/ArnsboMedia/systemet/branch/master/graph/badge.svg?token=qW7SrPfHta)](https://codecov.io/gh/ArnsboMedia/systemet) ![Other](https://img.shields.io/badge/Other%20Relevant-Badges-blue.svg)

> (IMPORTANT) Write a summary of the purpose of the project. Any team member, without any knowledge, should be able to understand what this project is about, based on this summary.


[Project Name] is a microservice, responsible for monitoring the uptime of our third party dependencies, such as AWS and Google APIs.

## Requirements

> List required dependencies, platform, OS, etc. required to run the project. Include version numbers, if the project depends on other than latest version.


- [PHP](http://php.net/) 7.2.14 (with gd, imagick, memcache and mysql extensions)
- [Ruby](https://www.ruby-lang.org/en/news/2014/11/13/ruby-1-9-3-p551-is-released/) 1.9.3
- [foreman](http://theforeman.org/)

## Installation

> This step should only included the command already present below. *If* there are any addional steps (such as user specific ENV variables, or similar) please inlude them in this section. Any developer should be able to setup the project without them having any knowdlege about it beforehand.


```shell
$ scripts/setup
```

## Usage

> Below, there must be min. one, syntax highlighted, command: the `scripts/server` command. If the project can be booted in different ways or with some custom flags/arguments, please include these commands as well. Other key commands or subcommands, not covered by `scripts/`, should also be include in this section.


```shell
$ scripts/server
```

Boot application in `dev` environment, but connected to staging database:
```shell
$ foreman start -e .dev-env -f Procfile.staging
```

Compile static `prod` build:
```shell
$ make build:dev
```

## Test

> The default `scripts/test`, which test entire suite, must be include in this section. Addional testing commands can be included, if relevant.

```shell
$ scripts/test
```
