<?php
spl_autoload_register(function (string $class): void {
    $prefix = 'Iriven\\PhpFormGenerator\\';
    if (!str_starts_with($class, $prefix)) {
        return;
    }
    $relative = substr($class, strlen($prefix));
    $file = __DIR__ . '/src/' . str_replace('\\', '/', $relative) . '.php';
    if (is_file($file)) {
        require_once $file;
    }
});
use Iriven\PhpFormGenerator\FormGenerator;
use Iriven\PhpFormGenerator\Infrastructure\ArrayRequestDataProvider;
$form = new FormGenerator(new ArrayRequestDataProvider([], ['email' => 'x@y.z'], 'POST'));
echo $form->open(['method' => 'post'])->addEmail('Email')->addCountries('Country')->addCheckbox('Active', [], ['value' => '1'])->addFile('Doc')->close();
