<?php

class Form {
	protected $conf = array(
		'fields' => array(),
	);
	
	public static $HTML_STRING_VALUES = array('input.text', 'input.password', 'input.hidden', 'textarea', 'select');
	
	public function __construct($formName) {
		$this->formName = $formName;
		
		if (isset($_POST[$this->formName]) && is_array($_POST[$this->formName])) {
			$this->post = $_POST[$this->formName];
		} else {
			$this->post = array();
		}
		
		$this->f = array();
		$this->errors = array();
		
		foreach ($this->conf['fields'] as $key => &$field) {
			// Human readable name of field, used in error messages, etc.
			if (!isset($field['human'])) $field['human'] = ucfirst($key);
			if (!is_string($field['human'])) exit("'human' of field {$key} must be a string");
			
			// Html element to use: <input type="text" />, <input type="password" />, or <textarea>...</textarea>
			if (!isset($field['html'])) $field['html'] = 'input.text';
			if (!is_string($field['html'])) exit("'html' of field {$key} must be a string");
			if (!in_array($field['html'], self::$HTML_STRING_VALUES)) {
				exit("'html' of field {$key} must be one of: ".join(", ", self::$HTML_STRING_VALUES));
			}
			
			// Extra html attributes for tag $field['html']
			// For example: `size="40"`, or `cols="40" rows="16"`
			if (!isset($field['html_extra'])) $field['html_extra'] = '';
			if (!is_string($field['html_extra'])) exit("'html_extra' of field {$key} must be a string");
			
			// Is the field required?
			if (!isset($field['required'])) $field['required'] = false;
			if (!in_array($field['required'], array(true, false))) exit("Wrong value for 'required' of field {$key}");
			if (!isset($field['requiredErr'])) $field['requiredErr'] = "%s field cannot be empty";
			if (!is_string($field['requiredErr'])) exit("Non-string value for 'requiredErr' of field {$key}");
			
			// Type of field.
			// Current the only supported type is 'string'
			/// TODO: Add support for 'array' type
			if (!isset($field['type'])) $field['type'] = 'string';
			if (!in_array($field['type'], array('string',))) exit("Wrong field type for field {$key}");
			
			if ($field['type'] == 'string') {
				// Minimum length of field to pass validation, 0 means no minimum length
				if (!isset($field['minLen'])) $field['minLen'] = 0;
				if (!is_int($field['minLen'])) exit("Wrong value for 'minLen' of field {$key}");
				if (!isset($field['minLenErr'])) $field['minLenErr'] = "%s field must contain at least %d characters";
				if (!is_string($field['minLenErr'])) exit("Non-string value for 'minLenErr' of field {$key}");
				
				// Maximum length of field to pass validation, 0 means no maximum length
				if (!isset($field['maxLen'])) $field['maxLen'] = 0;
				if (!is_int($field['maxLen'])) exit("Wrong value for 'maxLen' of field {$key}");
				if (!isset($field['maxLenErr'])) $field['maxLenErr'] = "%s field cannot contain more than %d characters";
				if (!is_string($field['maxLenErr'])) exit("Non-string value for 'maxLenErr' of field {$key}");
				
				if (isset($field['inArray'])) {
					if (!is_array($field['inArray'])) exit("'inArray' parameter for field {$key} must be an array");
					if (!isset($field['inArrayErr'])) $field['inArrayErr'] = "%s field must contain value from the list";
					if (!is_string($field['inArrayErr'])) exit("'inArrayErr' parameter for field {$key} must be a string");
				}
				
				// Default value for field (if $_POST doesn't specify any string value)
				if (!isset($field['def'])) $field['def'] = '';
				if (!is_string($field['def'])) exit("Wrong value for 'def' of field {$key}");
			}
		}
		unset($field);
		
		// Initialize $this->f with field values eith from $_POST or $field['def'] (default value)
		foreach ($this->conf['fields'] as $fieldName => $field) {
			if ($field['type'] == 'string') {
				$this->f[$fieldName] = (isset($this->post[$fieldName]) && is_string($this->post[$fieldName]))
					? mb_trim($this->post[$fieldName])
					: $field['def']
				;
			}
		}
	}
	
	protected function dbSave() {}
	
	public function run() {
		if (isset($_POST['submitted']) && is_string($_POST['submitted']) && $_POST['submitted'] == $this->formName) {
			if ($this->validate()) {
				$this->dbSave();
			}
		}
	}
	
	// Add error message $errMsg for field $fieldName
	public function addError($fieldName, $errMsg) {
		if (!isset($this->errors[$fieldName])) $this->errors[$fieldName] = array();
		$this->errors[$fieldName][] = $errMsg;
	}
	
	// Validate all fields, return true if valid, false if there are validation errors
	public function validate() {
		foreach ($this->conf['fields'] as $fieldName => $field) {
			if ($field['type'] == 'string') {
				
				if ($field['required']) {
					if ($this->f[$fieldName] == '') {
						$this->addError($fieldName, sprintf($field['requiredErr'], $field['human']));
					}
				}
				
				if ($field['minLen']) {
					if (mb_strlen($this->f[$fieldName]) < $field['minLen']) {
						$this->addError($fieldName, sprintf($field['minLenErr'], $field['human'], $field['minLen']));
					}
				}
				
				if ($field['maxLen']) {
					if (mb_strlen($this->f[$fieldName]) > $field['maxLen']) {
						$this->addError($fieldName, sprintf($field['maxLenErr'], $field['human'], $field['maxLen']));
					}
				}
				
				if (isset($field['inArray'])) {
					if (!in_array($this->f[$fieldName], array_keys($field['inArray']))) {
						$this->addError($fieldName, sprintf($field['inArrayErr'], $field['human']));
					}
				}
				
			}
		}
		
		return count($this->errors) == 0;
	}
	
	public function displayField($fieldName) {
		if (!in_array($fieldName, array_keys($this->conf['fields']))) exit("Unknown field {$fieldName}");
		$field = $this->conf['fields'][$fieldName];
		
		if ($field['type'] == 'string') {
			if ($field['html'] == 'input.hidden') {
				?>
				<input
					type="hidden"
					name="<?php h("{$this->formName}[{$fieldName}]"); ?>"
					value="<?php h($this->f[$fieldName]); ?>"
				/>
				<?php
				return;
			}
			
			?>
<div class="field<?php if (isset($this->errors[$fieldName])) e(" error-field"); ?>">
	
	<div class="field-name<?php if ($field['required']) e(" required"); ?>">
		<?php h($field['human']); ?>:
	</div>
	
	<div class="html-input">
		<?php if (in_array($field['html'], array('input.text', 'input.password'))) { ?>
			
			<?php
			if ($field['html'] == 'input.text') {
				$inputType = 'text';
			} elseif ($field['html'] == 'input.password') {
				$inputType = 'password';
			}
			?>
			<input
				type="<?php h($inputType); ?>"
				name="<?php h("{$this->formName}[{$fieldName}]"); ?>"
				value="<?php h($this->f[$fieldName]); ?>"
				<?php e($field['html_extra']); ?>
				<?php if ($field['maxLen']) { ?>
					maxlength="<?php h($field['maxLen']); ?>"
				<?php } ?>
			/>
			
		<?php } elseif ($field['html'] == 'textarea') { ?>
			
			<textarea
				name="<?php h("{$this->formName}[{$fieldName}]"); ?>"
				<?php e($field['html_extra']); ?>
			><?php h($this->f[$fieldName]); ?></textarea>
			
		<?php } elseif ($field['html'] == 'select' && isset($field['inArray'])) { ?>
			
			<select name="<?php h("{$this->formName}[{$fieldName}]"); ?>">
				<?php foreach ($field['inArray'] as $optId => $optName) {?>
					<option
						value="<?php h($optId); ?>"
						<?php if ($this->f[$fieldName] == $optId) e(" selected=\"selected\""); ?>
					><?php h($optName); ?></option>
				<?php } ?>
			</select>
			
		<?php } else { exit("Unknown 'html' for field {$fieldName}: `{$field['html']}`"); } ?>
	</div>
	
	<?php if (isset($this->errors[$fieldName])) { ?>
		<ul class="errors">
			<?php foreach ($this->errors[$fieldName] as $err) e("<li>{$err}</li>"); ?>
		</ul>
	<?php } ?>
	
</div>
			<?php
		} else {
			exit("Non-string types aren't implemented");
		}
	}
}