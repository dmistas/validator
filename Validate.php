<?php

class Validate
{
    private $passed = false, $errors = [], $pdo = null;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Проверка соответствия $source правилам валидации $items
     *
     * @param array $source
     * @param array $items
     *
     * @return boolean
     */
    public function check(array $source, array $items = [])
    {
        foreach ($items as $item => $rules) {
            foreach ($rules as $rule => $rule_value) {
                $value = $source[$item];

                if ($rule == 'required' && empty($value)) {
                    $this->addError("{$item} is required");
                } else if (!empty($value)) {
                    switch ($rule) {
                        case 'min':
                            if (strlen($value) < $rule_value) {
                                $this->addError("{$item} must be a minimum of {$rule_value} characters.");
                            }
                            break;
                        case 'max':
                            if (strlen($value) > $rule_value) {
                                $this->addError("{$item} must be a maximum of {$rule_value} characters.");
                            }
                            break;
                        case 'matches':
                            if ($value != $source[$rule_value]) {
                                $this->addError("{$rule_value} must match {$item}");
                            }
                            break;
                        case 'email':
                            if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                                $this->addError("{$item} incorrect");
                            }
                            break;
                        case 'unique':
                            $sql = "SELECT * FROM {$rule_value} WHERE $item = :$item";
                            $statement = $this->pdo->prepare($sql);
                            $statement ->execute([$item=>$value]);
                            $count = $statement -> rowCount();
                            if ($count) {
                                $this->addError("{$item} already exists.");
                            }
                            break;
                    }
                }
            }
        }
        if (empty($this->getErrors())) {
            $this->passed = true;
        }
        return $this->isPassed();
    }

    /**
     * Добавление ошибки в массив $errors
     *
     * @param $error
     */
    private function addError($error)
    {
        $this->errors[] = $error;
    }

    /**
     * Возвращает массив с ошибками валидации
     *
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Возвращает true если проверка пройдена или false
     *
     * @return bool
     */
    public function isPassed(): bool
    {
        return $this->passed;
    }
}
