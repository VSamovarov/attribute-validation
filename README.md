Ось оновлена версія `README.md` українською мовою з описом функціоналу, прикладами використання та інструкціями з
інтеграції без дублювання коду ServiceProvider і фасаду:

---

# 🎯 Валідація DTO через атрибути для Laravel

Модуль валідації на основі PHP-атрибутів для проєктів на Laravel. Дозволяє описувати правила валідації безпосередньо в
об'єктах DTO, використовуючи нативний валідатор Laravel.

## 📦 Встановлення

Переконайтеся, що ваш проєкт використовує PHP 8.1 або вище.

Скопіюйте модуль до папки `app/Common/AttributeValidation/`, зареєструйте Service Provider і Facade (інструкція нижче).

---

## ⚙️ Інтеграція в проєкт

### 1. Service Provider

Модуль вже містить `AttributeValidationServiceProvider`. Щоб активувати його, додайте до масиву `providers` у файлі
`config/app.php`:

```php
'providers' => [
    // ...
    Sam\AttributeValidation\AttributeValidationServiceProvider::class,
],
```

### 2. Facade

У модулі також є Facade `AttributeValidation`. Щоб ним скористатися, додайте до масиву `aliases` у `config/app.php`:

```php
'aliases' => [
    // ...
    'AttributeValidation' => Sam\AttributeValidation\Facades\AttributeValidation::class,
],
```

---

## 🧩 Використання

### DTO з атрибутами

```php
use Sam\AttributeValidation\Attributes\Validate;

class UserDTO
{
    #[Validate(['required', 'min:3'], ['min' => 'Ім’я занадто коротке'])]
    public string $name;

    #[Validate(['required', 'email'])]
    public string $email;
}
```

### Валідація DTO

```php
use AttributeValidation;

$dto = new UserDTO();
$dto->name = 'Al';
$dto->email = 'неправильний-email';

$errors = AttributeValidation::validate($dto);

if (!empty($errors)) {
    return response()->json(['errors' => $errors], 422);
}
```

---

## 🧪 Тестування

Функціональний тест на Pest:

```php
test('validates DTO with Laravel validator attribute', function () {
    $dto = new class {
        #[Validate(['min:3', 'email'], ['min' => 'моя помилка мінімум'])]
        public ?string $email = 'd';

        #[Validate(['required', 'min:3'])]
        public ?string $name = 'Al';
    };

    $errors = AttributeValidation::validate($dto);

    expect($errors)->toHaveKey('email')
        ->and($errors['email'][0])->toContain('моя помилка мінімум')
        ->and($errors['email'][1])->toContain('validation.email')
        ->and($errors)->toHaveKey('name');
});
```

---

## ✨ Переваги

* ✅ Декларативний стиль через PHP-атрибути
* 🔁 Підтримка власних повідомлень про помилки
* 🔌 Можливість підключення додаткових валідаторів (не лише Laravel)
* 🧱 Гнучка архітектура завдяки інтерфейсам `ValidationRule` і `PropertyValidator`

---

## 🔧 Розширення

Можна реалізувати власні атрибути або валідатори — наприклад, для валідації на основі бізнес-логіки чи зовнішніх
сервісів.

---

## 📂 Структура модуля

```
app/
└── Common/
    └── AttributeValidation/
        ├── Attributes/
        │   └── Validate.php
        ├── Contracts/
        │   ├── PropertyValidator.php
        │   └── ValidationRule.php
        ├── Validator/
        │   └── LaravelPropertyValidator.php
        ├── MetadataExtractor.php
        ├── AttributeValidator.php
        ├── ValidatedPropertyMetadata.php
        └── Facades/
            └── AttributeValidation.php
```

---

## ✅ Готово до використання

Після реєстрації сервіс-провайдера та фасаду ви можете викликати:

```php
AttributeValidation::validate($dto);
```

у будь-якому місці вашого застосунку — включно з контролерами, сервісами, middleware, або тестами.

---

Хочете зробити цей функціонал окремим Composer-пакетом? Легко! Архітектура вже готова для цього.
