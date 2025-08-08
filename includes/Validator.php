<?php

class Validator {
    private $errors = [];
    
    public function validate($data, $rules) {
        $this->errors = [];
        
        foreach ($rules as $field => $ruleString) {
            $fieldRules = explode('|', $ruleString);
            
            foreach ($fieldRules as $rule) {
                $this->applyRule($field, $data[$field] ?? '', $rule);
            }
        }
        
        return empty($this->errors);
    }
    
    private function applyRule($field, $value, $rule) {
        switch ($rule) {
            case 'required':
                if (empty($value)) {
                    $this->errors[$field][] = ucfirst($field) . ' is required.';
                }
                break;
                
            case 'email':
                if (!empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->errors[$field][] = ucfirst($field) . ' must be a valid email address.';
                }
                break;
                
            case 'string':
                if (!empty($value) && !is_string($value)) {
                    $this->errors[$field][] = ucfirst($field) . ' must be a string.';
                }
                break;
                
            default:
                if (strpos($rule, 'max:') === 0) {
                    $max = (int) substr($rule, 4);
                    if (strlen($value) > $max) {
                        $this->errors[$field][] = ucfirst($field) . " must not exceed {$max} characters.";
                    }
                } elseif (strpos($rule, 'min:') === 0) {
                    $min = (int) substr($rule, 4);
                    if (strlen($value) < $min) {
                        $this->errors[$field][] = ucfirst($field) . " must be at least {$min} characters.";
                    }
                } elseif (strpos($rule, 'unique:') === 0) {
                    $table = substr($rule, 7);
                    $user = new User();
                    if ($user->exists($value)) {
                        $this->errors[$field][] = ucfirst($field) . ' is already taken.';
                    }
                }
                break;
        }
    }
    
    public function errors() {
        return $this->errors;
    }
    
    public function hasErrors() {
        return !empty($this->errors);
    }
} 