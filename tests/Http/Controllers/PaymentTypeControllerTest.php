<?php

namespace Tests\Http\Controllers;

use App\Http\Controllers\PaymentTypeController;
use App\Models\PaymentTypeModel;
use App\Service\PaymentTypeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\JsonResponse;
use PHPUnit\Framework\MockObject\Exception;
use ReflectionException;
use Tests\TestCase;

/**
 * Tests for PaymentTypeController class.
 * Focus on testing the store functionality to ensure proper request handling
 * and service behavior.
 */
class PaymentTypeControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * @return void
     * @throws Exception
     * @throws ReflectionException
     */
    public function test___construct_initializes_payment_type_service()
    {
        // Arrange
        $mockPaymentTypeService = $this->createMock(PaymentTypeService::class);

        // Act
        $controller = new PaymentTypeController($mockPaymentTypeService);

        // Assert
        $this->assertInstanceOf(PaymentTypeController::class, $controller);
        $this->assertSame($mockPaymentTypeService, $this->getProperty($controller, 'paymentType'));
    }

    /**
     * Test that the store method successfully saves a new payment type with valid data.
     *
     * @return void
     * @throws \Exception
     */
    public function test_store_saves_payment_type_with_valid_data()
    {
        // Arrange
        $payload = [
            'name' => 'Boleto',
            'description' => 'Boleto Bancário',
            'installments' => 1,
        ];

        // Act
        $response = $this->postJson(route('payment_type.create'), $payload);

        // Assert
        $response->assertStatus(201);
        $this->assertDatabaseHas('payment_type', $payload);
        $response->assertJsonStructure([
            'erro',
            'data' => [
                'name',
                'description',
                'installments',
                'created_at',
                'updated_at',
                'id'
            ]
        ]);
    }

    /**
     * Test that the destroy method successfully deletes a payment type with a valid ID.
     *
     * @return void
     */
    public function test_destroy_deletes_payment_type_with_valid_id()
    {
        // Arrange
        $paymentType = PaymentTypeModel::factory()->create();

        // Act
        $response = $this->deleteJson(route('payment_type.destroy', ['id' => $paymentType->id]));

        // Assert
        $response->assertStatus(200);
        $this->assertDatabaseHas('payment_type', [
            'id' => $paymentType->id,
            'name' => $paymentType->name,
            'description' => $paymentType->description,
            'installments' => $paymentType->installments
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
        $response = $this->deleteJson(route('payment_type.destroy', ['id' => 999]));

        // Assert
        $response->assertStatus(404);
        $response->assertJson(['message' => 'Tipo de pagamento não encontrado.']);
    }

    /**
     * Test that the update method returns validation errors for invalid data.
     *
     * @return void
     */
    public function test_update_returns_error_for_invalid_data()
    {
        // Arrange
        $paymentType = PaymentTypeModel::factory()->create();
        $payload = [
            'name' => 'updated',
            'description' => 'updated description',
            'installments' => 5222,
        ];

        // Act
        $response = $this->putJson(route('payment_type.update', ['id' => $paymentType->id]), $payload);

        // Assert
        $response->assertStatus(500);
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
            'installments' => 2,
        ];

        // Act
        $response = $this->putJson(route('payment_type.update', ['id' => 999]), $payload);

        // Assert
        $response->assertStatus(404);
        $response->assertJson(['message' => 'Tipo de pagamento não encontrado.']);
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
            'installments' => '',
        ];

        $response = $this->postJson(route('payment_type.create'), $payload);

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
        $payments = PaymentTypeModel::factory()->count(5)->create();

        $response = $this->getJson(route('payment_type.index'));

        // Assert
        $response->assertStatus(200);
        $responseData = $response->getData(true);
        $this->assertIsArray($responseData);

        foreach ($payments as $payment) {
            $this->assertDatabaseHas('payment_type', [
                'id' => $payment->id,
                'name' => $payment->name,
                'description' => $payment->description,
                'installments' => $payment->installments,
            ]);

            $this->assertTrue(collect($responseData)->contains(function ($item) use ($payment) {
                return $item['id'] === $payment->id &&
                    $item['name'] === $payment->name &&
                    $item['description'] === $payment->description &&
                    $item['installments'] === $payment->installments;
            }));
        }
    }

    /**
     * @return void
     */
    public function test_index_returns_empty_message()
    {
        $response = $this->getJson(route('payment_type.index'));

        // Assert
        $response->assertStatus(200);
        $response->assertJson(['message' => 'Nenhum tipo de pagamento foi encontrado.']);
    }


    /**
     * Test that the active method successfully activates a payment type with a valid ID.
     *
     * @return void
     */
    public function test_active_activates_payment_type_with_valid_id()
    {
        // Arrange
        $paymentType = PaymentTypeModel::factory()->create(['deleted_at' => now()]);

        // Act
        $response = $this->putJson(route('payment_type.active', ['id' => $paymentType->id]));

        // Assert
        $response->assertStatus(200);
        $this->assertDatabaseHas('payment_type', [
            'id' => $paymentType->id,
            'deleted_at' => null,
        ]);
        $response->assertJson(['message' => 'Tipo de pagamento ativado com sucesso.']);
    }

    /**
     * Test that the active method returns a not found error for an invalid ID.
     *
     * @return void
     */
    public function test_active_returns_not_found_for_invalid_id()
    {
        // Act
        $response = $this->putJson(route('payment_type.active', ['id' => 999]));

        // Assert
        $response->assertStatus(404);
        $response->assertJson(['message' => 'Tipo de pagamento não encontrado.']);
    }

    /**
     * Test that the active method returns an error when trying to activate an already active payment type.
     *
     * @return void
     */
    public function test_active_returns_error_when_already_active()
    {
        // Arrange
        $paymentType = PaymentTypeModel::factory()->create();

        // Act
        $response = $this->putJson(route('payment_type.active', ['id' => $paymentType->id]));

        // Assert
        $response->assertStatus(200);
        $response->assertJson(['message' => 'O tipo de pagamento já está ativo.']);
    }
}