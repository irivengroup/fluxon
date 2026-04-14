<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Tests;

use Iriven\PhpFormGenerator\Application\FormGenerator;
use Iriven\PhpFormGenerator\Presentation\Html\HtmlRenderer;
use PHPUnit\Framework\TestCase;

final class LegacyApiCompatibilityMatrixTest extends TestCase
{
    public function testLegacyBuilderMethodsProduceRenderableFields(): void
    {
        $generator = new FormGenerator('legacy');
        $generator
            ->open()
            ->addFieldset(['legend' => 'Legacy matrix'])
            ->addAudio('audio')
            ->addButton('button', ['label' => 'Button'])
            ->addCaptcha('captcha')
            ->addCheckbox('checkbox')
            ->addColor('color')
            ->addCountries('country')
            ->addDatalist('browser', ['choices' => ['Firefox', 'Chrome']])
            ->addDate('date')
            ->addDateTime('issuedAt')
            ->addDatetime('issuedAtLegacy')
            ->addDatetimeLocal('issuedAtLocal')
            ->addEditor('editor')
            ->addEmail('email')
            ->addFile('file')
            ->addHidden('hidden')
            ->addImage('image')
            ->addMonth('month')
            ->addNumber('number')
            ->addPassword('password')
            ->addPhone('phone')
            ->addRadio('radio', ['choices' => ['a' => 'A', 'b' => 'B']])
            ->addRange('range')
            ->addReset('reset', ['label' => 'Reset'])
            ->addSearch('search')
            ->addSelect('select', ['choices' => ['x' => 'X', 'y' => 'Y']])
            ->addSubmit('submit', ['label' => 'Save'])
            ->addText('text')
            ->addTextArea('textareaLegacy')
            ->addTextarea('textarea')
            ->addTime('time')
            ->addUrl('url')
            ->addVideo('video')
            ->addWeek('week')
            ->addYesNo('yesno')
            ->endFieldset();

        $form = $generator->getForm();
        $html = (new HtmlRenderer())->renderForm($form->createView());

        foreach ([
            'legacy[audio]',
            'legacy[button]',
            'legacy[captcha]',
            'legacy[checkbox]',
            'legacy[color]',
            'legacy[country]',
            'legacy[browser]',
            'legacy[date]',
            'legacy[issuedAt]',
            'legacy[issuedAtLegacy]',
            'legacy[issuedAtLocal]',
            'legacy[editor]',
            'legacy[email]',
            'legacy[file]',
            'legacy[hidden]',
            'legacy[image]',
            'legacy[month]',
            'legacy[number]',
            'legacy[password]',
            'legacy[phone]',
            'legacy[radio]',
            'legacy[range]',
            'legacy[reset]',
            'legacy[search]',
            'legacy[select]',
            'legacy[submit]',
            'legacy[text]',
            'legacy[textareaLegacy]',
            'legacy[textarea]',
            'legacy[time]',
            'legacy[url]',
            'legacy[video]',
            'legacy[week]',
            'legacy[yesno]',
        ] as $fieldName) {
            self::assertStringContainsString($fieldName, $html);
        }
    }
}
