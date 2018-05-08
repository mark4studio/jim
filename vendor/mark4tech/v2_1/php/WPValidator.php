<?php
/**
Jim - MVC wordpress plugin development framework
Version:           2.1
Copyright (C) 2018  Naveen

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.

 */
 
namespace mark4tech\v2_1;
class WPValidator{
	public $errors;
	public $rules;
	public $labels;
	public $fields = array();
	public function __construct($rules=array(), $labels = array()){
		$this->rules = $rules;
		$this->labels = $labels;
	}
	public function validate($input){
		$this->errors = array();
		foreach($this->rules as $fields=>$rule){
			$fields = explode(',', $fields);
			foreach($fields as $field){

				$field = trim($field);
				if(is_string($rule)) $rule = array($rule);
				$rule['value'] = isset($input[$field]) || null;
				$rule['name'] = $field;
				if(method_exists($this, $rule[0])){
					call_user_func(array($this, $rule[0]), $rule);
				}
			}
		}
		return empty($this->errors);
	}
	public function addError($name, $message){
		if(!isset($this->errors[$name]) || !is_array(@$this->errors[$name])) $this->errors[$name] = array();
		array_push($this->errors[$name], $message);
	}
	public function printError($name){
		if(@$this->errors[$name][0]){
			echo '<p class="ch-error">'. $this->errors[$name][0] .'</p>';
		}
	}
	public function hasError($name){
		return (isset($this->errors[$name][0]) && count($this->errors[$name]) > 0);
	}
	public function required($data){
		$errMsg = null;
		$result = isset($data['value']) && $data['value'] != null;
		if($result && is_string($data['value'])){
			$result = trim($data['value']) !='';
		}
		elseif($result && is_array($data['value'])){
			$result = !empty($data['value']);
		}
		if($result && is_numeric(@$data['minLength'])){
			$result = $result && strlen(trim($data['value'])) >= (int)$data['minLength'];
			if($result === false) $errMsg = isset($data['tooShort']) ? $data['tooShort'] : 'Please lengthen the text value for \''. $this->getLabel($data['name']) . '\' to '. $data['minLength'] . ' characters or more.';
		}
		if($result && is_numeric(@$data['maxLength'])){
			$result = $result && strlen(trim($data['value'])) <= (int)$data['maxLength'];
			if($result === false) $errMsg = isset($data['tooLong']) ? $data['tooLong'] : 'Please shorten the text value for \''. $this->getLabel($data['name']) . '\' to '. $data['maxLength'] . ' characters or less.';
		}
		if($result===false){
			if(!$errMsg) $errMsg = isset($data['message']) ? $data['message'] : 'A valid value for \''. $this->getLabel($data['name']) . '\' is required.';
			$this->addError($data['name'], $errMsg);
		}
		return $result;
	}
	public function numeric($data){
		if((@$data['value'] == null  || trim($data['value']) == '')) return true;
		$result = (@$data['integerOnly'] === true ? (preg_match('/^\d+$/', trim($data['value']))===1) : is_numeric($data['value']));
		$message = (@$data['integerOnly']===true ? 'An integer ' : 'A numeric ') . 'value for \''. $this->getLabel($data['name']) . '\'';
		if($result){
			$data['value'] = (float)$data['value'];
			if(is_numeric(@$data['min']) && is_numeric(@$data['max'])){
				$min = (float)$data['min'];
				$max = (float)$data['max'];
				$result = $result && ($data['value'] >= $min && $data['value'] <= $max);
				$message .= ' between '. $data['min'] .' and ' . $data['max'];
			}
			elseif(is_numeric(@$data['min'])){
				$min = (float)$data['min'];
				$result = $result && ($data['value'] >= $min);
				$message .= ' (min:'. $min .')';
			}
			elseif(is_numeric(@$data['max'])){
				$max = (float)$data['max'];
				$result = $result && ($data['value'] <= $max);
				$message .= ' (max:'. $max .')';
			}
		}
		if($result===false){
			$errMsg = isset($data['message']) ? $data['message'] : $message.' is required.';
			$this->addError($data['name'], $errMsg);
		}
		return $result;
	}
	public function email($data){
		if((@$data['value'] == null  || trim($data['value']) == '')) return true;
		$result = preg_match('/^([\w\.\-_]+)?\w+@[\w-_]+(\.\w+){1,}$/i', $data['value']);
		if($result !== 1){
			$errMsg = isset($data['message']) ? $data['message'] : 'The value you entered for \''. $this->getLabel($data['name']) . '\' is not a valid email address.';
			$this->addError($data['name'], $errMsg);
		}
		return $result;
	}
	public function url($data){
		if((@$data['value'] == null  || trim($data['value']) == '')) return true;
		$result = preg_match('/[(http(s)?):\/\/(www\.)?a-zA-Z0-9@:%._\+~#=]{2,256}\.[a-z]{2,6}\b([-a-zA-Z0-9@:%_\+.~#?&//=]*)/i', $data['value']);
		if($result !== 1){
			$errMsg = isset($data['message']) ? $data['message'] : 'The value you entered for \''. $this->getLabel($data['name']) . '\' is not a valid URL.';
			$this->addError($data['name'], $errMsg);
		}
		return $result;
	}
	public function regex($data){
		if((@$data['value'] == null  || trim($data['value']) == '')) return true;
		$result = preg_match($data['pattern'], $data['value']);
		if($result !== 1){
			$errMsg = isset($data['message']) ? $data['message'] : 'The value you entered for \''. $this->getLabel($data['name']) . '\' does not match the expected pattern.';
			$this->addError($data['name'], $errMsg);
		}
		return $result;
	}
	private function getLabel($field){
		if(isset($this->labels[$field])){
			return $this->labels[$field];
		}
		return Utils::normalizeString($field);
	}
}