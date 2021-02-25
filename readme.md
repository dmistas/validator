#### Класс для валидации данных

##### Использование

-  для создания экземпляра, нужно передать в конструктор объект PDO:
<pre>
$pdo = new PDO("mysql:host=_my_host;dbname=my_db", "username", "password"));
$validate = new Validate($pdo);
</pre>

- передаем в объект $validate ассоциативные массивы проверяемый и с правилами валидации, <br> 
- названия ключей в передаваемом массиве должны совпадать с названиями ключей для проверки (например $_POST['username'] и 'username')    
<pre>
$validation = $validate->check($_POST, [
            'username' => [
                'required' => true,
                'min' => 2,
                'max' => 15,
            ],
            'email' => [
                'required' => true,
                'email' => true,
                'unique' => 'users'
            ],
            'password' => [
                'required' => true,
                'min' => 3,
            ],
            'password_again' => [
                'required' => true,
                'matches' => 'password',
            ],
        ]);
if ($validation) {
    do something
}        
</pre>


- существующие правила валидации: <br>

'required' => true - обязательное поле;<br>
'min' => 2 - минимальное количество символов;<br>
'max' => 15 - максимальное количество символов;<br>
'matches' => 'key_of_source_array' - значение должно совпадать со значением ключа в передаваемом массиве, в примере с полем "password";<br>
'unique' => 'table' - уникальное значение поля в таблице 'table', поиск по имени ключа правила, в примере email;<br>

- новые правила валидации можно добавлять в методе check()