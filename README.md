# processor-escape-character

[![Build Status](https://travis-ci.org/keboola/processor-escape-character.svg?branch=master)](https://travis-ci.org/keboola/processor-escape-character)

Takes all tables in `/data/in/tables` and removes the defined escape character from the files and stores them to `/data/out/tables`.

 - Applies to sliced files too
 - Copies manifest file

## Example

`escaped_by` set to `\` 

| Source  | Result |
| ------------- | ------------- |
| `a\"b` | `a"b` |
| `a\\b` | `a\b` |
| `a\\\b` | `a\b` |
| `a\\\\b` | `a\\b` |
| `a\\b` | `a\b` |
| `a\"b` | `a"b` |
| `a\b` | `ab` |

## Prerequisites

All CSV files must have a manifest file with `delimiter` and `enclosure` properties.

## Usage

Required parameters:

- `escaped_by` -- Escaping character 

### Sample configuration

```
{  
    "definition": {
        "component": "keboola.processor-escape-character",
        "escaped_by": "\\"
    }
}
```

## Development
 
Clone this repository and init the workspace with following command:

```
git clone https://github.com/keboola/processor-escape-character
cd processor-escape-character
docker-compose build
docker-compose run php composer install
```

Run the test suite using this command:

```
docker-compose run php php ./tests/run.php
```
 
## Integration
 - Build is started after push on [Travis CI](https://travis-ci.org/keboola/processor-escape-character)
 - [Build steps](https://github.com/keboola/processor-escape-character/blob/master/.travis.yml)
   - build image
   - execute tests against new image
   - publish image to ECR if release is tagged


