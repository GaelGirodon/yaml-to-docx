# YAML → Docx

**YAML → Docx** is a simple tool to generate Word documents (`.docx`)
from a template and a YAML (`.yml`/`.yaml`) file containing values.

The main goal of this tool is to allow automating Word documents generation,
in a CI/CD pipeline, a build process, etc., to easily create delivery notes,
reports and much more.

## Get started

- Install and configure [PHP](https://www.php.net/)
- Download the latest release
- Run the tool: `php yamltodocx.phar path/to/template.docx path/to/values.yml path/to/output.docx`

> Read the [usage](#usage) section and the [full example](#example)
> for more details on how to use this tool.

## Usage

### How it works

**YAML → Docx** takes two input files:

- **A Word template** (`<template>`): a classic `.docx` file
  with variables to replace (e.g. `${key}`)
- **A YAML file** (`<values>`): a YAML file with values to set
  in the template file (e.g. `key: value`)

The tool loads the template file, populates the placeholders with values
from the YAML file (e.g. `${key}` will be replaced by `value`) and saves
the generated `.docx` to the specified output file (`<output>`).

### CLI

```shell
php yamltodocx.phar <template> <values> <output>
```

| Argument     | Description                       | Example                 |
| ------------ | --------------------------------- | ----------------------- |
| `<template>` | Path to the template file         | `path/to/template.docx` |
| `<values>`   | Path to the YAML file with values | `path/to/values.yml`    |
| `<output>`   | Path to the output file           | `path/to/output.docx`   |

## Example

**1/** Create a template file (`template.docx`) with placeholders (`${key}`):

> **Title:** ${title}
>
> **Subtitle:** ${subtitle}
>
> | Name    | Description | Value    |
> | ------- | ----------- | -------- |
> | ${name} | ${desc}     | ${value} |

**2/** Provide a YAML file (`values.yml`) with values to populate in the template:

```yaml
# Basic variables
title: Lorem ipsum
subtitle: Dolor sit amet, consectetur adipiscing elit
# Array
name: # Array row selector (one of the fields)
  - name: "Name 1"
    desc: "Description 1"
    value: "Value 1"
  - name: "Name 2"
    desc: "Description 2"
    value: "Value 2"
  - name: "Name 3"
    desc: "Description 3"
    value: "Value 3"
```

**3/** Run **YAML → Docx**:

```shell
php yamltodocx.phar template.docx values.yml output.docx
```

**4/** The generated file (`output.docx`) should look like this:

> **Title:** Lorem ipsum
>
> **Subtitle:** Dolor sit amet, consectetur adipiscing elit
>
> | Name    | Description   | Value    |
> | ------- | ------------- | -------- |
> | Name 1  | Description 1 | Value 1  |
> | Name 2  | Description 2 | Value 2  |
> | Name 3  | Description 3 | Value 3  |

## Thanks

Thanks to the team (and the contributors) behind [PHPWord](https://github.com/PHPOffice/PHPWord),
a wonderful library for reading and writing Word documents.

## License

**YAML → Docx** is licensed under the GNU General Public License.
