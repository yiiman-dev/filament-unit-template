<?php
namespace Tests\Feature;
class FeatureExampleTest extends \Tests\TestCase{
    use \Illuminate\Foundation\Testing\RefreshDatabase;
    public function test_example()
    {
        it('returns a successful response', function () {
            $response = $this->get('/');

            $response->assertStatus(200);
        });
    }
}

