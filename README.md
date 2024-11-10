# TYPO3 Basics - testing-framework integration demo project

## Introduction

This repository contains a basic demo TYPO3 project installation
along with a generic `ddev` integration and acts as a playground
to guide, demonstrate and train the basic integration of `unit-`
and `functional-tests` using the [typo3/testing-framework](https://github.com/typo3/testing-framework). It's
open and free to be used for trainings of upcoming TYPO3 project
developers and team collegues, enhance company or the official
TYPO3 documentation.

Part of [TYPO3 UserGroup Bodensee Talk (2024-06-05)](https://github.com/sbuerk/tf-basics-usergroup-bodensee-slides).

> NOTE: This project is not meant to be used as a skeleton for
> a real life project and comes with a automatic boostrap and
> configuration with example data, using the `TYPO3 styleguide`
> extension.

The included documentation and guide does not contain instruction
or help to use it without `ddev`.


## Overview and repository usage

### Pre-requisits

The included project instance is based on TYPO3 v12 (LTS) and
includes a `ddev` configuration for a easy startup. The guide
to integrate testing uses `ddev` commands based on this setup
to make it easy to follow and instruction.

To use `ddev` following is required:

* [ddev and requirements: installation]()
* bash 3+
* docker (desktop) for ddev (or a compatible alternative)
* For the simplified `Build/Scripts/runTests.sh` example based
  on the TYPO3 core implementation either docker or podman.

Please ensure you have all pre-requisite installed. Install at
least `ddev` following the installation instruction for your
operating system.

Included and provided shell scripts are based on bash, thus
ensure you have it available (at least for advanced topics).

### Structure

This repository contains a usual TYPO3 v12 instance (project)
composer installation along with two local path extension in
`packages/` acting as demonstration for test integration.

Both extension contain dummy unit and functional tests.

### Start up the engine (repository)

Simply clone this repository and do a ddev start. The provided
setup installes the instance automatically on the first start.

```bash
git clone git@github.com:sbuerk/tf-basics-project.git \
  && cd ./tf-basics-project \
  && ddev start
```

During the first startup you will be asked to enter a username
and password to create a admin backend user.

Do destroy / reset the installation, which is recommended to
start with a clean state again use:

**NOTE: This is destructive and do not preseve any data.**

```bash
ddev stop -ROU \
  && git clean -xdf -e .idea/
```

**Delete repository project from ddev, so folder can be deleted**

```bash 
ddev stop -ROU \
  && ddev delete -O --yes \
  && git clean -xdf -e .idea/  
```

## GUIDE: How to integrate `unit-` and `functional` testing in a project

### 1. Introduction and preparation

> NOTE: If you want to retry the integration, you should switch to a
> custom branch first which allows you to switch back and restart the
> process.

To be able to retry the guide, it's recommended to create a custom 
branch first:

```bash
git checkout -b integrate-testing
```

which creates a new branching from the current main state.

### 2. Create project testing infrastructure

#### 2.1. Add required dependencies

The project is based on TYPO3 v12 without the need to support v11,
thus installing `typo3/testing-framework` v8 along with `phpunit 10`
is reasonable. These dependencies can be installed using `composer`:

```bash
ddev composer require --dev \
  "typo3/testing-framework":"^8.0.9" \
  "phpunit/phpunit":"^10.5"
```

#### 2.2. Prepare phpunit configuration for `unit` and `functional` tests

##### 2.2.1. Overview

`typo3/testing-framework` ships phpunit configuration and bootstrap files
as templates (starting points) to integrate testing in custom project and
extensions.

These template files can be simply copied. It's recommended to modify the
test location paths for the included test-suites - which allows to omit a
location to find tests - and have unit and functional tests separated.

For projects, a global (root) place with tests against all extension may
be a usual setup, specially for the beginning. However, depending on the
knowledge and company/project policies they may be more contained in the
local path extensions (`packages/*`) to have more extensio based testing
in place and using the site-package extension or a project-base package
extension to provide the global over all testing - or use both variants.

> NOTE: Due to the configurability of PHP-Unit a lot of possible folder
> structures and places containing tests are possible and out of scope
> to demonstrate all of them - just follow the PHPUnit test-suite config
> to adjust for such scenarios.

**Example structure for `packages/` based tests (2.2.2):**

```
./packages/
./packages/extension_one/Tests/Unit/
./packages/extension_one/Tests/Functional/
./packages/extension_two/Tests/Unit/
./packages/extension_two/Tests/Functional/
./packages/project_main_package/Tests/Unit/
./packages/project_main_package/Tests/Functional/
```

**Example structure for `Tests/` (root folder) based tests (2.2.3):**

```
./packages/
./packages/extension_one/
./packages/extension_two/

./Tests/Unit/
./Tests/Functional
```

**Example structure for both variants combined (2.2.4):***

```
./packages/
./packages/extension_one/Tests/Unit/
./packages/extension_one/Tests/Functional/
./packages/extension_two/Tests/Unit/
./packages/extension_two/Tests/Functional/
./packages/project_main_package/Tests/Unit/
./packages/project_main_package/Tests/Functional/
./Tests/Unit/
./Tests/Functional
```

##### 2.2.2. `packages/*` extensions
That means, that we need to adjust the paths in the template files to be

* `packages/*/Tests/Unit/` for unit tests and
* `packages/*/Tests/Functional/` for functional tests

**Copy template files to project (packages folder)**

```bash
ddev exec mkdir -p Build/phpunit \
  && ddev exec \cp -Rvf \
       vendor/typo3/testing-framework/Resources/Core/Build/* \
       Build/phpunit/
```

**Adjust paths in the phpunit configuration files**

Either open the `Build/php-unit/*Tests.xml` files in a editor
and adjust the paths to:

* `UnitTests.xml` => `../../packages/*/Tests/Unit/`
* `FunctionalTests.xml` => `../../packages/*/Tests/Functional/`

or you could simply use following commands:

```bash
ddev exec \
  "sed -i 's/..\/..\/..\/..\/..\/..\/typo3\/sysext\//..\/..\/packages\//g' Build/phpunit/UnitTests.xml" \
&& ddev exec \
  "sed -i 's/..\/..\/..\/..\/..\/..\/typo3\/sysext\//..\/..\/packages\//g' Build/phpunit/FunctionalTests.xml"
```

##### 2.2.3. `Tests/` (root package tests)

That means, that we need to adjust the paths in the template files to be

* `Tests/Unit/` for unit tests and
* `Tests/Functional/` for functional tests

**Copy template files to project (packages folder)**

```bash
ddev exec mkdir -p Build/phpunit \
  && ddev exec \cp -Rvf \
       vendor/typo3/testing-framework/Resources/Core/Build/* \
       Build/phpunit/
```

**Adjust paths in the phpunit configuration files**

Either open the `Build/php-unit/*Tests.xml` files in a editor
and adjust the paths to:

* `UnitTests.xml` => `../../packages/*/Tests/Unit/`
* `FunctionalTests.xml` => `../../packages/*/Tests/Functional/`

or you could simply use following commands:

```bash
ddev exec \
  "sed -i 's/..\/..\/..\/..\/..\/..\/typo3\/sysext\/\*\//..\/..\//g' Build/phpunit/UnitTests.xml" \
&& ddev exec \
  "sed -i 's/..\/..\/..\/..\/..\/..\/typo3\/sysext\/\*\//..\/..\//g' Build/phpunit/FunctionalTests.xml"
```

##### 2.2.4. `Tests/` (root package tests) and `packages/*/` extension tests combined

That means, that we need to adjust the paths in the template files to be contain two directories for each testsuite

* `Tests/Unit/` and `packages/*/Tests/Unit` for unit tests and
* `Tests/Functional/` and `packages/*/Tests/Functional/` for functional tests

**Copy template files to project (packages folder)**

```bash
ddev exec mkdir -p Build/phpunit \
  && ddev exec \cp -Rvf \
       vendor/typo3/testing-framework/Resources/Core/Build/* \
       Build/phpunit/
```

**Adjust paths in the phpunit configuration files**

Either open the `Build/php-unit/*Tests.xml` files in a editor
and adjust the paths to:

* `UnitTests.xml` => `../../packages/*/Tests/Unit/` and `../../Tests/Unit/`
* `FunctionalTests.xml` => `../../packages/*/Tests/Functional/` and `../../Tests/Functional/`

@todo single line command - edit config files manually for now, see below

> NOTE: In case the example glob is used this would also execute tests from `vendor/` packages containing matching
> test folders, if installed from source (preferred-source), for example system extensions or 3rd party extensions.

To ensure that no tests are executed from source packages installed into `vendor/`, instead of using the sed commands
from above two directory lines must be used in the configuration files. For example, instead of

```xml
    <testsuite name="Unit tests">
        <directory>../../*/Tests/Unit/</directory>
    </testsuite>
```

use 

```xml
    <testsuites>
        <testsuite name="Unit tests">
            <!--
                This path either needs an adaption in extensions, or an extension's
                test location path needs to be given to phpunit.
            -->
            <directory>../../packages/*/Tests/Unit/</directory>
            <directory>../../Tests/Unit/</directory>
        </testsuite>
    </testsuites>
```

and for functional 

```xml
    <testsuites>
        <testsuite name="Functional tests">
            <!--
                This path either needs an adaption in extensions, or an extension's
                test location path needs to be given to phpunit.
            -->
            <directory>../../packages/*/Tests/Functional/</directory>
            <directory>../../Tests/Functional/</directory>
        </testsuite>
    </testsuites>
```

#### 2.3. Execute tests

Basically, we have now a working setup for unit and functional tests. Let's try
it out now to verify it (this demo project containes dummy tests):

**Execute unit tests of all local path packages**

```bash
ddev exec phpunit -c Build/phpunit/UnitTests.xml
```

**Execute functional tests using sqlite database**

```bash
ddev exec \
  typo3DatabaseDriver=pdo_sqlite \
  php vendor/bin/phpunit -c Build/phpunit/FunctionalTests.xml
```

Literally, we are done now. Basic unit and functional test integration accomplished
in a usable state. Are we ?

Usually, you want to use at least the same database as used in the project (server)
for local development and test execution. For that, you could provide a couple of
environment variable for the `phpunit` call using the `FunctionlTests.xml` config to
tell the `testing-framework ` which database to use. This example project uses MariaDB
10.5 and the command to invoke the tests against the ddev provided database server
would be:

```bash
ddev exec \
  typo3DatabaseDriver='mysqli' \
  typo3DatabaseHost='db' \
  typo3DatabasePort=3306 \
  typo3DatabaseUsername='root' \
  typo3DatabasePassword='root' \
  typo3DatabaseName='func' \
  php vendor/bin/phpunit -c Build/phpunit/FunctionalTests.xml
```

### 2.4. `typo3/testing-framework` environment variables

The previous steps revealed, that there are environment variables
which can be used to configure which database server to use with
functional tests.

> NOTE: The server must be provided, otherwise the functional test
> instances created cannot connect to the server.

List of environment variables:

* `typo3DatabaseDriver`: This defines, which database driver should be used:
	* `pdo_sqlite` for sqlite (no other environment variables required)
	* `mysqli` to use the PHP mysqli extension to connect to the database,
          suitable for `MariaDB` and `MySQL` server connections
	* `pdo_mysql` to use the PHP PDO mysql extension to connect to the
          database, suitable for `MariaDB` and `MySQL`
	* `pdo_pqsql` to use the PHP PQSSQL extension to connect to a PostgreSQL
          server
* `typo3DatabaseHost`: The hostname or IP-Address for the database server.
* `typo3DatabasePort`: Database server port. 
* `typo3DatabaseUsername`: The username to use for the database connection.
* `typo3DatabasePassword`: The password to use for the database connection.
* `typo3DatabaseName`: Database name prefix to be used to create function test
   instance databases (one for each `FunctionalTest` case).

> NOTE: When using a database server (not sqlite) the provided user credentials
> needs to have the rights to create database: `CREATE SCHEMA ...`.

@todo to be continued

