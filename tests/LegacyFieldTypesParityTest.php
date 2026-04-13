<?php
declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Tests;

use Iriven\PhpFormGenerator\Application\FormGenerator;
use Iriven\PhpFormGenerator\Domain\Field\AudioType;
use Iriven\PhpFormGenerator\Domain\Field\ButtonType;
use Iriven\PhpFormGenerator\Domain\Field\CaptchaType;
use Iriven\PhpFormGenerator\Domain\Field\CheckboxType;
use Iriven\PhpFormGenerator\Domain\Field\ChoiceType;
use Iriven\PhpFormGenerator\Domain\Field\ColorType;
use Iriven\PhpFormGenerator\Domain\Field\CountryType;
use Iriven\PhpFormGenerator\Domain\Field\DatalistType;
use Iriven\PhpFormGenerator\Domain\Field\DateType;
use Iriven\PhpFormGenerator\Domain\Field\DatetimeLocalType;
use Iriven\PhpFormGenerator\Domain\Field\DatetimeType;
use Iriven\PhpFormGenerator\Domain\Field\EditorType;
use Iriven\PhpFormGenerator\Domain\Field\EmailType;
use Iriven\PhpFormGenerator\Domain\Field\FileType;
use Iriven\PhpFormGenerator\Domain\Field\HiddenType;
use Iriven\PhpFormGenerator\Domain\Field\ImageType;
use Iriven\PhpFormGenerator\Domain\Field\MonthType;
use Iriven\PhpFormGenerator\Domain\Field\NumberType;
use Iriven\PhpFormGenerator\Domain\Field\PasswordType;
use Iriven\PhpFormGenerator\Domain\Field\PhoneType;
use Iriven\PhpFormGenerator\Domain\Field\RadioType;
use Iriven\PhpFormGenerator\Domain\Field\RangeType;
use Iriven\PhpFormGenerator\Domain\Field\ResetType;
use Iriven\PhpFormGenerator\Domain\Field\SearchType;
use Iriven\PhpFormGenerator\Domain\Field\SelectType;
use Iriven\PhpFormGenerator\Domain\Field\SubmitType;
use Iriven\PhpFormGenerator\Domain\Field\TextType;
use Iriven\PhpFormGenerator\Domain\Field\TextareaType;
use Iriven\PhpFormGenerator\Domain\Field\TimeType;
use Iriven\PhpFormGenerator\Domain\Field\UrlType;
use Iriven\PhpFormGenerator\Domain\Field\VideoType;
use Iriven\PhpFormGenerator\Domain\Field\WeekType;
use Iriven\PhpFormGenerator\Domain\Field\YesNoType;
use PHPUnit\Framework\TestCase;

final class LegacyFieldTypesParityTest extends TestCase
{
    public function testLegacyFieldTypesAllExist(): void
    {
        $classes = [
            AudioType::class, ButtonType::class, CaptchaType::class, CheckboxType::class, ColorType::class,
            CountryType::class, DatalistType::class, DateType::class, DatetimeType::class, DatetimeLocalType::class,
            EditorType::class, EmailType::class, FileType::class, HiddenType::class, ImageType::class,
            MonthType::class, NumberType::class, PasswordType::class, PhoneType::class, RadioType::class,
            RangeType::class, ResetType::class, SearchType::class, SelectType::class, SubmitType::class,
            TextType::class, TextareaType::class, TimeType::class, UrlType::class, VideoType::class,
            WeekType::class, YesNoType::class,
        ];

        foreach ($classes as $class) {
            self::assertTrue(class_exists($class), $class . ' should exist.');
        }
    }

    public function testCountryTypeExtendsChoiceType(): void
    {
        self::assertTrue(is_subclass_of(CountryType::class, ChoiceType::class));
    }

    public function testFacadeKeepsLegacyMethodsAndRenderableTypes(): void
    {
        $html = (new FormGenerator())
            ->open('legacy')
            ->addFieldset(['legend' => 'Legacy'])
            ->addText('text')
            ->addEmail('email')
            ->addPassword('password')
            ->addPhone('phone')
            ->addDate('date')
            ->addDatetime('datetime')
            ->addDatetimeLocal('datetime_local')
            ->addMonth('month')
            ->addWeek('week')
            ->addTime('time')
            ->addColor('color')
            ->addNumber('number')
            ->addRange('range')
            ->addSearch('search')
            ->addUrl('url')
            ->addTextarea('message')
            ->addEditor('editor')
            ->addCheckbox('agree')
            ->addRadio('status', ['draft' => 'Draft', 'live' => 'Live'])
            ->addSelect('country_code', ['FR' => 'France', 'BE' => 'Belgium'])
            ->addCountries('country')
            ->addYesNo('published')
            ->addDatalist('city', ['Paris', 'Lyon'])
            ->addFile('attachment')
            ->addImage('image')
            ->addAudio('audio')
            ->addVideo('video')
            ->addHidden('token')
            ->addButton('cancel')
            ->addReset('clear')
            ->addSubmit('save')
            ->endFieldset()
            ->render();

        self::assertStringContainsString('type="datetime"', $html);
        self::assertStringContainsString('type="datetime-local"', $html);
        self::assertStringContainsString('accept="image/*"', $html);
        self::assertStringContainsString('accept="audio/*"', $html);
        self::assertStringContainsString('accept="video/*"', $html);
        self::assertStringContainsString('<datalist', $html);
        self::assertStringContainsString('<fieldset', $html);
    }
}
