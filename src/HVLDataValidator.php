<?php

declare(strict_types=1);

namespace HValverde\HVLAssistant;

use HValverde\HVLAssistant\HVLCore;
use HValverde\HVLAssistant\HVLString;

class HVLDataValidator
{
	const DEFAULT_ERRORS = [
		'*valid_data'				=> 'Invalid data provided.',
		'greater_than'				=> 'This field must be greater than {rule_target}.',
		'greater_than_or_equal_to'	=> 'This field must be greater than or equal to {rule_target}.',
		'less_than'					=> 'This field must be less than {rule_target}.',
		'less_than_or_equal_to'		=> 'This field must be less than or equal to {rule_target}',
		'max_length'				=> 'This field has a maximum length requirement of {rule_target} character(s).',
		'min_length'				=> 'This field has a minimum length requirement of {rule_target} character(s).',
		'regex_match'				=> 'This field does not match the validation pattern.',
		'required'					=> 'This field is required.',
		'valid_email'				=> 'This email is invalid.',
	];

	protected $rules = [];

	public function getErrorMessage(string $field_name, string $rule_name, string $rule_target = null): string
	{
		$field_rules = $this->getRules($field_name);
		$error_msg = $field_rules['errors'][$rule_name]
			?? self::DEFAULT_ERRORS[$rule_name]
			?? null;

		if (is_null($error_msg)) {
			throw new \Exception("Error message not found for rule name. '$rule_name'");
		}

		$ps_data = $this->getParserData($field_name, ['{rule_target}' => $rule_target]);

		return $this->parseMessage($error_msg, $ps_data);
	}

	public function getParserData(string $field_name, array $append_data = []): array
	{
		$field_rule = $this->getRules($field_name);
		$ps_data = array_merge(
			[
				'{field_name}' => $field_name,
				'{label}' => $field_rule['label'] ?? null
			],
			$append_data
		);

		foreach ($ps_data as $key => $value) {
			if (is_null($value)) unset($ps_data[$key]);
		}

		return $ps_data;
	}

	public function getRules(string $field_name = '', bool $throw_exception = true): array|null
	{
		if (empty($field_name)) return $this->rules;

		if (!array_key_exists($field_name, $this->rules)) {
			if ($throw_exception) {
				throw new \Exception("Field name '$field_name' not found in set rules.");
			}
			return null;
		}

		return $this->rules[$field_name];
	}

	public function parseMessage(string $message, array $ps_data = []): string
	{
		$message = str_replace(
			array_keys($ps_data),
			array_values($ps_data),
			$message
		);

		return $message;
	}

	public function setRules(array $rules, bool $append_rules = false): array
	{
		if (empty($rules)) {
			throw new \Exception("Parameter rules is empty.");
		}

		self::validateRules($rules);

		if (!$append_rules) {
			$this->rules = $rules;

			return $this->getRules();
		}

		$this->rules = array_merge($this->rules, $rules);

		return $this->getRules();
	}

	public function validateData(array $data): array
	{
		if (empty($this->getRules())) {
			throw new \Exception("Validator rules not set.");
		}

		$validation_results = [];

		foreach ($data as $field_name => $field_data) {
			if (is_null($this->getRules($field_name, false))) continue;

			$result = $this->_validateField($field_name, $field_data);

			if (count($result)) $validation_results[$field_name] = $result;
		}

		return $validation_results;
	}

	public static function convertRulesStringToArray(string $rule_string): array
	{
		$exploded_rules = explode('|', $rule_string);
		$rules_arr = [];

		foreach ($exploded_rules as $rule_str) {
			$rule_str = trim($rule_str);

			preg_match('/^(?<rule_name>\w+)(\[(?<rule_target>.+)\])?$/', $rule_str, $matches);

			if (empty($matches['rule_name'])) {
				throw new \Exception("Validation rule name not found in rule string. '$rule_str'");
			}

			$rules_arr[$matches['rule_name']] = $matches['rule_target'] ?? null;
		}

		return $rules_arr;
	}

	public static function getRunRuleMethod(string $rule_name): string
	{
		$method = HVLString::snakeCaseToPascalCase($rule_name);
		$method = 'run' . $method . 'Rule';

		return $method;
	}

	public static function runGreaterThanRule(string $input, string $target): bool
	{
		$input = floatval($input);
		$target = floatval($target);

		return $input > $target;
	}

	public static function runGreaterThanOrEqualToRule(string $input, string $target): bool
	{
		$input = floatval($input);
		$target = floatval($target);

		return $input >= $target;
	}

	public static function runLessThanRule(string $input, string $target): bool
	{
		$input = floatval($input);
		$target = floatval($target);

		return $input < $target;
	}

	public static function runLessThanOrEqualToRule(string $input, string $target): bool
	{
		$input = floatval($input);
		$target = floatval($target);

		return $input <= $target;
	}

	public static function runMaxLengthRule(string $input, string $target): bool
	{
		return strlen($input) <= (int) $target;
	}

	public static function runMinLengthRule(string $input, string $target): bool
	{
		return strlen($input) >= (int) $target;
	}

	public static function runRegexMatchRule(string $input, string $target): bool
	{
		return (bool) preg_match($target, $input);
	}

	public static function runRequiredRule(string $input): bool
	{
		return self::runMinLengthRule($input, '1');
	}

	public static function runValidEmailRule(string $input): bool
	{
		return HVLCore::validEmail($input);
	}

	public static function validateRules(array $rules): bool
	{
		foreach ($rules as $key => $value) {
			if (!array_key_exists('rules', $value)) {
				throw new \Exception("Validation rules is not set for '{$key}'.");
			}

			self::validateRuleString($value['rules']);
		}

		return true;
	}

	public static function ruleMethodExists(string $rule_name): bool
	{
		$method = self::getRunRuleMethod($rule_name);

		return method_exists(__CLASS__, $method);
	}

	public static function validateRuleString(string $rules): bool
	{
		if (empty($rules)) {
			throw new \Exception("Validation rule string is empty.");
		}

		$rules_arr = self::convertRulesStringToArray($rules);

		foreach ($rules_arr as $rule_name => $rule_target) {
			if (!self::ruleMethodExists($rule_name)) {
				throw new \Exception("Validation rule method not found. '$rule_name'");
			}

			if (!self::validateRuleValue($rule_name, $rule_target)) {
				throw new \Exception("Validation rule value not valid for rule name. '$rule_name\[$rule_target\]'");
			}
		}

		return true;
	}

	public static function validateRuleValue(string $rule_name, string $rule_target): bool
	{
		switch ($rule_name) {
			case 'greater_than':
			case 'greater_than_or_equal_to':
			case 'less_than':
			case 'less_than_or_equal_to':
			case 'max_length':
			case 'min_length':
				$rule_target = str_replace(',', '', $rule_target);
				return is_numeric($rule_target);
			case 'regex_match':
				return HVLString::validRegex($rule_target);
			case 'required':
			case 'valid_email':
				return true;
			default:
				return false;
		}
	}

	protected function _validateField(string $field_name, mixed $field_data): array
	{
		if (is_null($field_data)) $field_data = '';

		if (!is_numeric($field_data) && !is_string($field_data)) {
			return [
				'rule' => '*valid_data',
				'error' => $this->getErrorMessage($field_name, '*valid_data')
			];
		}

		$field_rule = $this->getRules($field_name);
		$rules_arr = self::convertRulesStringToArray($field_rule['rules']);

		foreach ($rules_arr as $rule_name => $rule_target) {
			$method = $this->getRunRuleMethod($rule_name);
			$callback = [__CLASS__, $method];
			$args = [
				(string) $field_data,
				(string) $rule_target
			];

			if (call_user_func_array($callback, $args)) continue;

			return [
				'rule' => $rule_name,
				'error' => $this->getErrorMessage($field_name, $rule_name, $rule_target)
			];
		}

		return [];
	}
}
