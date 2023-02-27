# HVL-Assistant \ HVL-Data-Validator

The HVLDataValidator class is a PHP class for data validation, with a flexible and customizable rule set. It can validate different types of data and provides useful error messages. The class can also be extended to add more validation rules and custom error messages.

## Available Rules

- greater_than\[int\]
- greater_than_or_equal_to\[int\]
- less_than\[int\]
- less_than_or_equal_to\[int\]
- max_length\[int\]
- min_length\[int\]
- regex_match\[/pattern/\]
- required
- valid_email

## Formating

### Rules

**Required:**

Every field name must equal an array with 'rules' key equals a string. Must use a pipe (|) divider for multiple rules.

**Examples:**

1. This example validates 1 rule for the field 'name'. The field must be at least 10 characters long.

	```php
	$rules = [
		'name' => [
			'rules' => 'min_length[10]'
		]
	];
	```

2. This example validates 2 rules for the field 'name'. The field must be at least 10 characters long and less than 100 characters long.

	```php
	$rules = [
		'name' => [
			'rules' => 'min_length[10]|max_length[100]'
		]
	];
	```

**Optional:**

Aside from 'rules', you can also add 'label' and 'error'.
The 'label' key should be equal to a user friendly string.
The 'error' key should be equal to an array with keys to override the default error messages.

**Example:**

```php
$rules = [
	'name' => [
		'label' => 'Full Name',
		'rules' => 'min_length[10]|max_length[100]',
		'errors' => [
			'max_length' => 'Your name is too long. Blame your parents!'
		]
	]
];
```

### Data

The validator expects an associative array, not a multi-dimensional array.

**IMPORTANT:** If the value of the input data is not an integer or string, it will validate false. Only fields that have a rule set will be validated. Others will be ignored.

**Example:**

```php
$data = [
	'name' => 'John',
	'age' => 25
];
```

### Results

If all data is valid, you will receive an empty array. If some data is invalid, you will receive an array of fields that failed validation with the rule that did not pass and the error message.

**Example:**

In the following example, the 'name' field value was longer than 100 characters.

```php
$results = [
	'name' = [
		'rule' => 'max_length',
		'error' => 'This field has a maximum length of 100 characters.'
	]
];
```