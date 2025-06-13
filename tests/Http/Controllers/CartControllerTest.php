<?php

namespace Tests\Http\Controllers;

use App\Http\Controllers\CartController;
use App\Http\Requests\CartCheckoutRequest;
use App\Http\Requests\CartRequest;
use App\Models\CartModel;
use App\Models\PaymentTypeModel;
use App\Models\ProductModel;
use App\Service\CartService;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\JsonResponse;
use PHPUnit\Framework\MockObject\Exception;
use ReflectionException;
use Tests\TestCase;

class CartControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * Test that the CartController constructor correctly assigns the CartService dependency.
     *
     * @return void
     * @throws Exception|ReflectionException
     */
    public function test_construct_initializes_cart_service()
    {
        // Arrange
        $mockCartService = $this->createMock(CartService::class);

        // Act
        $cartController = new CartController($mockCartService);

        // Assert
        $this->assertSame($mockCartService, $this->getProtectedProperty($cartController, 'cart'));
    }

    /**
     * Test that the index method returns all items in the cart.
     *
     * @return void
     * @throws Exception
     */
    public function test_index_returns_all_cart_items()
    {
        // Arrange
        CartModel::factory()->count(3)->create();
        $cartItems = CartModel::all()->toArray();
        $cartServiceMock = $this->createMock(CartService::class);
        $cartServiceMock->method('find')->willReturn($cartItems);

        $cartController = new CartController($cartServiceMock);

        // Act
        $response = $cartController->index();

        // Assert
        $responseData = $response->getData(true);
        $this->assertIsArray($responseData);
        $this->assertCount(3, $responseData);
    }

    /**
     * Test that the index method returns an empty array when the cart is empty.
     *
     * @return void
     * @throws Exception
     */
    public function test_index_returns_empty_cart_when_no_items_exist()
    {
        // Arrange
        $cartServiceMock = $this->createMock(CartService::class);
        $cartServiceMock->method('find')->willReturn([]);

        $cartController = new CartController($cartServiceMock);

        // Act
        $response = $cartController->index();

        // Assert
        $responseData = $response->getData(true);
        $this->assertIsArray($responseData);
        $this->assertEmpty($responseData);
    }

    /**
     * Helper to access protected or private properties.
     *
     * @param object $object
     * @param string $property
     * @return mixed
     * @throws ReflectionException
     */
    private function getProtectedProperty(object $object, string $property): mixed
    {
        $reflection = new \ReflectionClass($object);
        $propertyObject = $reflection->getProperty($property);

        return $propertyObject->getValue($object);
    }

    /**
     * Test that the store method returns the response from CartService.
     *
     * @return void
     * @throws Exception
     */
    public function test_store_returns_expected_response()
    {
        // Arrange
        $mockCartService = $this->createMock(CartService::class);
        $mockCartService->method('addProduct')->willReturn(new JsonResponse([
            'success' => true,
            'message' => 'Produto adicionado no carrinho com sucesso',
        ]));

        $product = ProductModel::factory()->create();
        $validData = [
            'product_id' => $product->id,
            'quantity' => $this->faker->numberBetween(1, 10),
        ];

        // Crie uma instância real do CartRequest
        $request = CartRequest::create('/cart', 'POST', $validData);

        $cartController = new CartController($mockCartService);

        // Act
        $response = $cartController->store($request);

        // Assert the response type
        $this->assertInstanceOf(JsonResponse::class, $response);

        // Decode the response data
        $responseData = $response->getData(true);

        // Assert the JSON response structure and values
        $this->assertIsArray($responseData);

        $this->assertTrue($responseData['success']);
        $this->assertSame('Produto adicionado no carrinho com sucesso', $responseData['message']);
    }

    /**
     * Test that the store method uses the addProduct method on CartService.
     *
     * @return void
     * @throws BindingResolutionException
     */
    public function test_store_adds_product_to_cart()
    {
        // Arrange
        // Cria um produto no banco de dados usando a fábrica
        $product = ProductModel::factory()->create();

        // Define os dados válidos para a requisição
        $validData = [
            'product_id' => $product->id,
            'quantity' => $this->faker->numberBetween(1, 10),
        ];

        $request = CartRequest::create('/cart', 'POST', $validData);
        $cartService = app()->make(CartService::class);
        $cartController = new CartController($cartService);
        $response = $cartController->store($request);

        // Assert: Verifique o tipo de resposta
        $this->assertInstanceOf(JsonResponse::class, $response);

        // Decode e valide o conteúdo da resposta JSON
        $responseData = $response->getData(true);
        $this->assertIsArray($responseData);
        $this->assertFalse($responseData['erro']);
        $this->assertSame('Produto adicionado no carrinho com sucesso.', $responseData['message']);

        // Assert: Verifique se o produto foi adicionado ao banco de dados
        $this->assertDatabaseHas('cart', $validData);
    }

    /**
     * Test that the update method updates the product in the cart with valid data.
     *
     * @return void
     * @throws BindingResolutionException
     */
    public function test_update_updates_product_in_cart()
    {
        $product = ProductModel::factory()->create();
        $validData = [
            'product_id' => $product->id,
            'quantity' => $this->faker->numberBetween(11, 20),
        ];
        CartModel::factory()->create(['product_id' => $product->id]);

        $request = CartRequest::create('/cart', 'PUT', $validData);
        $cartService = app()->make(CartService::class);
        $cartController = new CartController($cartService);
        $response = $cartController->update($request);

        // Assert the response type
        $this->assertInstanceOf(JsonResponse::class, $response);

        // Decode the response data
        $responseData = $response->getData(true);

        // Assert the JSON response structure and values
        $this->assertIsArray($responseData);
        $this->assertFalse($responseData['erro']);
        $expectedMessage = "Quantidade do produto {$product->name} foi atualizada no carrinho com sucesso.";
        $this->assertSame($expectedMessage, $responseData['message']);
        $this->assertDatabaseHas('cart', $validData);
    }

    /**
     * Test that the update method returns an error response with invalid data.
     *
     * @return void
     * @throws BindingResolutionException
     */
    public function test_update_returns_error_with_invalid_data()
    {
        $invalidData = [
            'product_id' => null,
            'quantity' => -1,
        ];

        CartModel::factory()->create();

        $request = CartRequest::create('/cart', 'PUT', $invalidData);
        $cartService = app()->make(CartService::class);
        $cartController = new CartController($cartService);
        $response = $cartController->update($request);

        // Assert the response type
        $this->assertInstanceOf(JsonResponse::class, $response);

        // Decode the response data
        $responseData = $response->getData(true);

        // Assert the JSON response structure and values
        $this->assertIsArray($responseData);
        $this->assertTrue($responseData['erro']);
        $this->assertSame('O produto não está no carrinho.', $responseData['message']);
    }

    /**
     * Test that the destroy method clears the cart and returns a success response.
     *
     * @return void
     * @throws BindingResolutionException
     */
    public function test_destroy_clears_cart_and_returns_success_response()
    {
        CartModel::factory()->create();

        CartRequest::create('/cart', 'DELETE');
        $cartService = app()->make(CartService::class);
        $cartController = new CartController($cartService);
        $response = $cartController->destroy();

        // Assert the response type
        $this->assertInstanceOf(JsonResponse::class, $response);

        // Decode the response data
        $responseData = $response->getData(true);

        // Assert the JSON response structure and values
        $this->assertIsArray($responseData);
        $this->assertFalse($responseData['erro']);
        $this->assertSame('Carrinho deletado com sucesso.', $responseData['message']);
        // Assert cart table is empty
        $this->assertDatabaseEmpty('cart');
    }


    /**
     * Test that the checkout method processes the request successfully with valid data.
     *
     * @return void
     * @throws BindingResolutionException
     */
    public function test_checkout_processes_request_successfully()
    {
        $payment = PaymentTypeModel::factory()->create(['installments' => 12]);
        CartModel::factory()->create();
        $validData = [
            "payment_type_id" => $payment->id,
            "card_holder_name" => "Lennon Cecere",
            "card_number" => "6521542198656532",
            "card_expiry_date" => "12/30",
            "card_cvv" => "654",
            "installments" => fake()->numberBetween(2, 12),
        ];

        $request = CartCheckoutRequest::create('/cart', 'POST', $validData);
        $cartService = app()->make(CartService::class);
        $cartController = new CartController($cartService);
        $response = $cartController->checkout($request);

        // Assert the response type
        $this->assertInstanceOf(JsonResponse::class, $response);

        // Decode and assert data
        $responseData = $response->getData(true);
        $this->assertTrue($responseData['success']);
        $expectedMessage = "Juros de 1% ao mês aplicado para pagamento parcelado em {$validData['installments']} vezes.";
        $this->assertSame($expectedMessage, $responseData['message']);
    }

}