<?php


use App\Http\Controllers\ProductController;
use App\Models\ProductModel;
use App\Service\ProductService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\JsonResponse;
use PHPUnit\Framework\MockObject\Exception;
use Tests\TestCase;

/**
 * Tests for ProductController class.
 * Focus on testing the store functionality to ensure proper request handling
 * and service behavior.
 */
class ProductControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * @return void
     * @throws Exception
     * @throws ReflectionException
     */
    public function test___construct_initializes_product_service()
    {
        // Arrange
        $mockProductService = $this->createMock(ProductService::class);

        // Act
        $controller = new ProductController($mockProductService);

        // Assert
        $this->assertInstanceOf(ProductController::class, $controller);
        $this->assertSame($mockProductService, $this->getProperty($controller, 'product'));
    }

    /**
     * Test that the store method successfully saves a new product with valid data.
     *
     * @return void
     * @throws \Exception
     */
    public function test_store_saves_product_with_valid_data()
    {
        // Arrange
        $payload = [
            'name' => 'Bota cano longo feminina',
            'description' => 'Bota cano longo feminina',
            'price_in_cents' => 50000,
            'quantity_in_stock' => 100,
        ];

        // Act
        $response = $this->postJson(route('product.create'), $payload);

        // Assert
        $response->assertStatus(201);
        $this->assertDatabaseHas('product', $payload);
        $response->assertJsonStructure([
            'erro',
            'data' => [
                'name',
                'description',
                'price_in_cents',
                'quantity_in_stock',
                'created_at',
                'updated_at',
                'id'
            ]
        ]);
    }

    /**
     * Test that the destroy method successfully deletes a product with a valid ID.
     *
     * @return void
     */
    public function test_destroy_deletes_product_with_valid_id()
    {
        // Arrange
        $product = ProductModel::factory()->create();

        // Act
        $response = $this->deleteJson(route('product.destroy', ['id' => $product->id]));

        // Assert
        $response->assertStatus(200);
        $this->assertDatabaseHas('product', [
            'id' => $product->id,
            'name' => $product->name,
            'description' => $product->description,
            'price_in_cents' => $product->price_in_cents,
            'quantity_in_stock' => $product->quantity_in_stock
        ]);

    }

    /**
     * Test that the destroy method returns a not found error for an invalid ID.
     *
     * @return void
     */
    public function test_destroy_returns_not_found_for_invalid_id()
    {
        // Act
        $response = $this->deleteJson(route('product.destroy', ['id' => 999]));

        // Assert
        $response->assertStatus(404);
        $response->assertJson(['message' => 'Produto não encontrado.']);
    }

    /**
     * Test that the update method returns validation errors for invalid data.
     *
     * @return void
     */
    public function test_update_returns_error_for_invalid_data()
    {
        // Arrange
        $product = ProductModel::factory()->create();
        $payload = [
            'name' => 'updated',
            'description' => 'updated description',
            'price_in_cents' => null,
            'quantity_in_stock' => null,
        ];

        // Act
        $response = $this->putJson(route('product.update', ['id' => $product->id]), $payload);

        // Assert
        $response->assertStatus(422);
    }

    /**
     * Test that the update method returns a not found error for an invalid ID.
     *
     * @return void
     * @throws \Exception
     */
    public function test_update_returns_not_found_for_invalid_id()
    {
        // Arrange
        $payload = [
            'name' => 'Valid Name',
            'description' => 'Valid Description',
            'price_in_cents' => 1000,
            'quantity_in_stock' => 100,
        ];

        // Act
        $response = $this->putJson(route('product.update', ['id' => 999]), $payload);

        // Assert
        $response->assertStatus(404);
        $response->assertJson(['message' => 'Produto não encontrado.']);
    }

    /**
     * Test that the store method returns validation error response for invalid data.
     *
     * @return void
     */
    public function test_store_returns_validation_error_for_invalid_data()
    {
        // Arrange
        $payload = [
            'name' => '', // Invalid as name is required
            'description' => '',
            'price_in_cents' => null,
            'quantity_in_stock' => null,
        ];

        $response = $this->postJson(route('product.create'), $payload);

        // Assert
        $response->assertStatus(422);
    }

    /**
     * @param object $object
     * @param string $property
     * @return mixed
     * @throws ReflectionException
     */
    protected function getProperty(object $object, string $property)
    {
        $reflection = new \ReflectionClass($object);
        $propertyReflection = $reflection->getProperty($property);
        $propertyReflection->setAccessible(true);

        return $propertyReflection->getValue($object);
    }

    /**
     * @return void
     * @throws Exception
     */
    public function test_index_returns_non_empty_response()
    {
        // Arrange
        $products = ProductModel::factory()->count(3)->create();

        $response = $this->getJson(route('product.index'));

        // Assert
        $response->assertStatus(200);
        $responseData = $response->getData(true);
        $this->assertIsArray($responseData);

        foreach ($products as $product) {
            $this->assertDatabaseHas('product', [
                'id' => $product->id,
                'name' => $product->name,
                'description' => $product->description,
                'price_in_cents' => $product->price_in_cents,
                'quantity_in_stock' => $product->quantity_in_stock
            ]);

            $this->assertTrue(collect($responseData)->contains(function ($item) use ($product) {
                return $item['id'] === $product->id &&
                    $item['name'] === $product->name &&
                    $item['description'] === $product->description &&
                    $item['price_in_cents'] === $product->price_in_cents &&
                    $item['quantity_in_stock'] === $product->quantity_in_stock;
            }));
        }
    }

    /**
     * @return void
     */
    public function test_index_returns_empty_message()
    {
        $response = $this->getJson(route('product.index'));

        // Assert
        $response->assertStatus(200);
        $response->assertJson(['message' => 'Nenhum produto foi encontrado.']);
    }


    /**
     * Test that the active method successfully activates a product with a valid ID.
     *
     * @return void
     */
    public function test_active_activates_product_with_valid_id()
    {
        // Arrange
        $product = ProductModel::factory()->create(['deleted_at' => now()]);

        // Act
        $response = $this->putJson(route('product.active', ['id' => $product->id]));

        // Assert
        $response->assertStatus(200);
        $this->assertDatabaseHas('product', [
            'id' => $product->id,
            'deleted_at' => null,
        ]);
        $response->assertJson(['message' => 'Produto ativado com sucesso.']);
    }

    /**
     * Test that the active method returns a not found error for an invalid ID.
     *
     * @return void
     */
    public function test_active_returns_not_found_for_invalid_id()
    {
        // Act
        $response = $this->putJson(route('product.active', ['id' => 999]));

        // Assert
        $response->assertStatus(404);
        $response->assertJson(['message' => 'Produto não encontrado.']);
    }

    /**
     * Test that the active method returns an error when trying to activate an already active product.
     *
     * @return void
     */
    public function test_active_returns_error_when_already_active()
    {
        // Arrange
        $product = ProductModel::factory()->create();

        // Act
        $response = $this->putJson(route('product.active', ['id' => $product->id]));

        // Assert
        $response->assertStatus(200);
        $response->assertJson(['message' => 'O produto já está ativo.']);
    }
}