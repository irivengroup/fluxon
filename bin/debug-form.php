<?php
declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use Iriven\PhpFormGenerator\Application\FormFactory;
use Iriven\PhpFormGenerator\Application\Type\ContactType;
use Iriven\PhpFormGenerator\Infrastructure\Http\ArrayRequest;
use Iriven\PhpFormGenerator\Presentation\Html\HtmlRenderer;
use Iriven\PhpFormGenerator\Presentation\Html\Theme\Bootstrap5Theme;

$factory = new FormFactory();
$form = $factory->create(ContactType::class, null, 'contact', [
    'method' => 'POST',
    'csrf_protection' => true,
]);

$form->handleRequest(new ArrayRequest('GET'));
echo (new HtmlRenderer(new Bootstrap5Theme()))->render($form);
