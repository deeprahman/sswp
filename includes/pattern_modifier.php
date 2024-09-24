<?php

class ApacheFileMatchRegexGenerator
{
    private $defaultPatterns;
    private $customPatternModifier;

    public function __construct(
        array $defaultPatterns = [],
        ?callable $customPatternModifier = null
    ) {
        $this->defaultPatterns = array_merge([
            'jpeg' => 'jpe?g',
            'doc' => 'docx?',
        ], $defaultPatterns);
        $this->customPatternModifier = $customPatternModifier;
    }

    public function generateRegex(array $extensions): string
    {
        $modifiedExtensions = array_map([$this, 'modifyPattern'], $extensions);
        return '/\.(' . implode('|', $modifiedExtensions) . ')$/i';
    }

    private function modifyPattern(string $extension): string
    {
        if ($this->customPatternModifier) {
            return call_user_func($this->customPatternModifier, $extension);
        }

        return $this->defaultPatterns[$extension] ?? $extension;
    }

    public function setCustomPatternModifier(callable $modifier): void
    {
        $this->customPatternModifier = $modifier;
    }

    public function addDefaultPattern(string $extension, string $pattern): void
    {
        $this->defaultPatterns[$extension] = $pattern;
    }

    public function getDefaultPatterns(): array
    {
        return $this->defaultPatterns;
    }
}

// Example usage:

// Using default patterns
$generator = new ApacheFileMatchRegexGenerator();
$extensions = ['php', 'jpeg', 'pdf', 'doc'];
$defaultPattern = $generator->generateRegex($extensions);
echo "Default pattern: $defaultPattern\n";

// Using custom default patterns
$customDefaults = [
    'pdf' => 'pdf|PDF',
    'txt' => 'te?xt'
];
$generator = new ApacheFileMatchRegexGenerator($customDefaults);
$customDefaultPattern = $generator->generateRegex(['pdf', 'txt', 'doc']);
echo "Custom default pattern: $customDefaultPattern\n";

// Using custom modifier
$customModifier = function($ext) {
    if ($ext === 'html') return 'html?';
    return $ext;
};
$generator->setCustomPatternModifier($customModifier);
$customPattern = $generator->generateRegex(['html', 'php']);
echo "Custom modifier pattern: $customPattern\n";

// Checking default patterns
print_r($generator->getDefaultPatterns());
