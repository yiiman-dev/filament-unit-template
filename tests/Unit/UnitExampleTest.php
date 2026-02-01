<?php
namespace Tests\Unit;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UnitExampleTest extends \Tests\TestCase{
    use RefreshDatabase;
    public function test_example_unit()
    {
        test('that true is true', function () {
            expect(true)->toBeTrue();
        });
    }
}


