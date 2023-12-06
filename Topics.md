# Topics

- Algemeen
- [Typing & Scoping]((https://www.php.net/manual/en/language.types.declarations.php))
- NULL handling
- New operators
- Array handling
- New functions
- Classes & Functions
- Attributes
- Other stuff

## Algemeen

- [Generators](https://www.php.net/manual/en/language.generators.overview.php)
- [Traits](https://www.php.net/manual/en/language.oop5.traits.php)

## Typing & Scoping

- 5.5
    - `const`
- 7.0
    - Function return type declarations: `string`, `ìnt`, `float`, `bool`, `array` (type ...$types)
    - Function parameter type declarations: `string`, `ìnt`, `float`, `bool`, `array`
    - Constant arrays: `define('FOO] , [1, 2, 3])`
- 7.1
    - Nullable function parameter and return types: `function d(?int $foo): ?string`
    - Scoped class constants
    - Void functions: `function f(): void`
    - Iterable pseudo-type: `function f(iterable $i)`
- 7.2
    - New object type: `object`
- 7.4
    - Typed class properties
    - Limited return type covariance and argument type contravariance
- 8.0
    - [Named arguments](https://www.php.net/manual/en/functions.arguments.php#functions.named-arguments)
    - [Union types](https://www.php.net/manual/en/language.types.type-system.php#language.types.type-system.composite.union): `function f(Foo|Bar $param)`
    - Support for mixed type: `mixed`
- 8.1
    - [Intersection types](https://www.php.net/manual/en/language.types.type-system.php#language.types.type-system.composite.intersection): `function f(Foo&Bar $param)`
    - [Enumerations](https://www.php.net/manual/en/language.enumerations.php)
    - Never return type: `function f(): never { exit; }`
    - [Readonly properties](https://www.php.net/manual/en/language.oop5.properties.php#language.oop5.properties.readonly-properties)
    - Final class constants: `final public const foo = "bar";`
- 8.2
    - Stand-alone types `null`, `false` and `true`
    - Intersection and union types can be combined
    - [Readonly classes](https://www.php.net/manual/en/language.oop5.basic.php#language.oop5.basic.class.readonly)
- 8.3
    - Anonymous classes may now be marked as readonly.
    - Readonly properties can now be reinitialized during cloning.
    - Class, interface, trait, and enum constants now support type declarations.

## NULL handling

- 7.0
    - Null coalescing operator: `??`
- 7.1
    - Nullable function parameter and return types: `function d(?int $foo): ?string`
- 7.4
    - Null coalescing assignment operator: `??=`
- 8.0
    - Null safe operator: `?->`
    - Support for mixed type: `mixed`

## New operators
- 

- 5.0
    - `...` for variadic function parameters and array unpacking.
    - `**` Exponentation operator
- 7.0
    - Spaceship operator:  `<=>`

## Array handling

- 7.1
    - Array destructuring (`list()` shorthand): `[$foo, $bar] = [1, 2]`
    - Keys in `list()` and array destructuring: `['foo' => $item1, 'bar' => $item2] = ['foo' => 1, 'bar' => 2]`
- 7.4
    - Unpacking inside arrays: `[1, 2, ...[3, 4], 5]`
- 8.1
    - Array Unpacking with String Keys: `[1, 2, [...['foo' => 3, 'bar' => 4], 5]`

## String handling

- 7.0
    - preg_replace_callback_array()
- 7.3
    - More flexible Heredoc and Nowdoc.
- 8.0
    - `str_contains()`
    - `str_starts_with()`
    - `str_ends_with()`
- 8.3
    - `mb_str_pad()`

## Classes & Functions

- 7.0
    - Anonymous classes: `new class [implements Interface] {}`
- 7.4
    - Arrow functions.
- 8.0
    - [Constructor property promotion](https://www.php.net/manual/en/language.oop5.decon.php#language.oop5.decon.constructor.promotion)
    - `get_class($object)` shorthand: `$object::class`
- 8.1
    - [First Class Callable Syntax](https://www.php.net/manual/en/functions.first_class_callable_syntax.php): `$fn = callable(...)`
    - `new` in Initializers: default value of a parameter, static variable, global constant initializers, and as
      attribute arguments.
- 8.3
    - Anonymous classes may now be marked as readonly.
    - Static variable initializers can now contain arbitrary expressions.

## Attributes

- 8.0
    - Support fort [Attributes](https://www.php.net/manual/en/language.attributes.php)
- 8.2
    - `#[\SensitiveParameter]`
    - `#[\AllowDynamicProperties]`
- 8.3
    - `#[\Override]`

## Other stuff

- 7.0
    - Group use declarations: `use some\namespace\{ClassA, ClassB, ClassC as C};`
- 7.1
    - Multi catch exception handling: `catch (Exception1 | Exception2 | .... $e)`
- 7.2
    - PDO prepared statement
      debugging: [PDOStatement::debugDumpParams() ](https://www.php.net/manual/en/pdostatement.debugdumpparams.php)
- 8.0
    - Match expressions: `match(expression) {...}`
- 8.3
    - Class constants can now be accessed dynamically using the C::{$name} syntax.

# PHP new features

- [5.6](https://www.php.net/manual/en/migration56.new-features.php)
- [7.0](https://www.php.net/manual/en/migration70.new-features.php)
- [7.1](https://www.php.net/manual/en/migration71.new-features.php)
- [7.2](https://www.php.net/manual/en/migration72.new-features.php)
- [7.3](https://www.php.net/manual/en/migration73.new-features.php)
- [7.4](https://www.php.net/manual/en/migration74.new-features.php)
- [8.0](https://www.php.net/manual/en/migration80.new-features.php)
- [8.1](https://www.php.net/manual/en/migration81.new-features.php)
    - https://stitcher.io/blog/new-in-php-81
- [8.2](https://www.php.net/manual/en/migration82.new-features.php)
    - https://stitcher.io/blog/new-in-php-82
- [8.3](https://www.php.net/manual/en/migration83.new-features.php)
    - https://stitcher.io/blog/new-in-php-83
