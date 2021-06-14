<?php

namespace Tests\Feature\Customer;

use App\Models\Customer;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CustomerTest extends TestCase
{
    use DatabaseTransactions;

    const BASE_URI = '/api';
    protected string $uri;

    protected function setUp(): void
    {
        parent::setUp();

        $this->uri = self::BASE_URI . "/customers";
    }

    /** @test */
    public function user_can_list_all_customers()
    {
        $customers = Customer::factory()->count(rand(1, 10))->create();
        $response = $this->json('GET', $this->uri);

        $response->assertOk()
            ->assertSee('data');

        foreach ($customers as $customer) {
            $customer['revenue'] = number_format($customer['revenue'], 2, '.', '');

            $response->assertJsonFragment($customer->toArray());
        }
    }

    /** @test */
    public function user_can_store_a_customer()
    {
        $newCustomerData = Customer::factory()->raw();

        $response = $this->json('POST', $this->uri, $newCustomerData);

        $response->assertOk()
            ->assertJsonFragment($newCustomerData);

        $newCustomerData['id'] = $response->json('data')['id'];

        $this->assertDatabaseHas('customers', $newCustomerData);
    }

    /** @test */
    public function user_can_update_a_customer()
    {
        $customer = Customer::factory()->create();

        $newCustomerData = Customer::factory()->raw();

        $response = $this->json('PUT', $this->uri . '/' . $customer->id, $newCustomerData);

        $newCustomerData['id'] = $customer->id;

        $response->assertOk();

        $this->assertDatabaseHas('customers', $newCustomerData);
    }

    /** @test */
    public function user_can_delete_a_customer()
    {
        $customer = Customer::factory()->create();

        $response = $this->json('DELETE', $this->uri . '/' . $customer->id);

        $response->assertOk();

        $this->assertSoftDeleted('customers', ['id' => $customer->id]);
    }
}
