# Transformer specification

## Input AST

- null
- boolean
- number
- string
- array
- object

## Core

- id
- literal
- pipe

## Instructions

### Instruction for **null**

- is-null
- default

### Instruction for **boolean**

- and
- or
- not
- if

### Instructions for **number**

arithmetic 

- add
- sub
- mult
- div
- mod

comparison

- eq
- ne
- lt
- le
- gt
- ge

### Instructions for **string**

- match
- prefix
- suffix
- contains
- len
- substr

### Instruction for **array**

- filter
- map
- reduce

### Instruction for **object**

- keyval
- setval
- merge