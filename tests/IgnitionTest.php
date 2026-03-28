<?php declare(strict_types=1);

use Cline\Bearer\Exceptions\AbstractInvalidConfigurationException;
use Facade\IgnitionContracts\ProvidesSolution;
use Facade\IgnitionContracts\Solution;

it('provides an ignition solution', function (): void {
    $exception = new class() extends AbstractInvalidConfigurationException
    {
        public function __construct() {}
    };

    expect($exception)->toBeInstanceOf(ProvidesSolution::class);

    $solution = $exception->getSolution();

    expect($solution)->toBeInstanceOf(Solution::class);
    expect($solution->getSolutionTitle())->not->toBe('');
    expect($solution->getDocumentationLinks())->toHaveKey('Package documentation');
});
