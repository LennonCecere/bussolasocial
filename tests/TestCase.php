<?php

namespace Tests;

use App\Http\Middleware\VerifyCsrfToken;
use Exception;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        parent::setUp(); // Sempre chame o setUp() original primeiro

        $this->testSqliteIsBeingUsed();

        if (env('APP_ENV') !== 'testing') {
            throw new Exception('O ambiente não está configurado como "testing". Certifique-se de que phpunit.xml e .env.testing estão corretos.');
        }

        // Ignorar validação de token CSRF durante testes
        $this->withoutMiddleware(VerifyCsrfToken::class);
    }

    public function testSqliteIsBeingUsed()
    {
        $this->assertEquals(config('database.default'), 'sqlite');
        $this->assertEquals(config('database.connections.sqlite.database'), ':memory:');
    }
}