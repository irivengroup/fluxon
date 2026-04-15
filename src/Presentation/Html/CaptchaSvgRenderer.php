<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Presentation\Html;

final class CaptchaSvgRenderer
{
    public function render(string $code, string $id): string
    {
        $width = 170;
        $height = 56;

        return sprintf(
            '<svg id="%s" xmlns="http://www.w3.org/2000/svg" width="%d" height="%d" viewBox="0 0 %d %d" role="img" aria-label="Captcha challenge"><rect width="100%%" height="100%%" rx="6" fill="#f3f4f6" />%s<g font-family="monospace" font-weight="700" letter-spacing="2">%s</g></svg>',
            htmlspecialchars($id, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'),
            $width,
            $height,
            $width,
            $height,
            $this->renderNoise($width, $height),
            $this->renderCharacters($code)
        );
    }

    private function renderCharacters(string $code): string
    {
        $chars = preg_split('//u', $code, -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $parts = [];

        foreach ($chars as $index => $char) {
            $parts[] = $this->renderCharacter((string) $char, $index);
        }

        return implode('', $parts);
    }

    private function renderCharacter(string $char, int $index): string
    {
        $x = 18 + ($index * 18);
        $y = random_int(32, 42);
        $rotate = random_int(-22, 22);
        $fontSize = random_int(20, 28);

        return sprintf(
            '<text x="%d" y="%d" font-size="%d" transform="rotate(%d %d %d)" fill="#1f2937">%s</text>',
            $x,
            $y,
            $fontSize,
            $rotate,
            $x,
            $y,
            htmlspecialchars($char, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')
        );
    }

    private function renderNoise(int $width, int $height): string
    {
        return $this->renderNoiseLines($width, $height)
            . $this->renderNoiseCurves($width, $height)
            . $this->renderNoiseDots($width, $height);
    }

    private function renderNoiseLines(int $width, int $height): string
    {
        $noise = '';
        for ($i = 0; $i < 8; $i++) {
            $noise .= sprintf(
                '<line x1="%d" y1="%d" x2="%d" y2="%d" stroke="#9ca3af" stroke-width="1" opacity="0.65" />',
                random_int(0, $width),
                random_int(0, $height),
                random_int(0, $width),
                random_int(0, $height)
            );
        }

        return $noise;
    }

    private function renderNoiseCurves(int $width, int $height): string
    {
        $noise = '';
        for ($i = 0; $i < 3; $i++) {
            $noise .= sprintf(
                '<path d="M %d %d Q %d %d %d %d T %d %d" stroke="#cbd5e1" stroke-width="1" fill="none" opacity="0.55" />',
                random_int(0, 25),
                random_int(5, $height - 5),
                random_int(30, 60),
                random_int(0, $height),
                random_int(70, 100),
                random_int(0, $height),
                random_int(115, min(160, $width)),
                random_int(5, $height - 5)
            );
        }

        return $noise;
    }

    private function renderNoiseDots(int $width, int $height): string
    {
        $noise = '';
        for ($i = 0; $i < 24; $i++) {
            $noise .= sprintf(
                '<circle cx="%d" cy="%d" r="%d" fill="#d1d5db" opacity="0.45" />',
                random_int(0, $width),
                random_int(0, $height),
                random_int(1, 2)
            );
        }

        return $noise;
    }
}
