<?php

use App\Helpers\StripeHelper;
use App\Models\Plan;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Stripe\Product as StripeProduct;

class ProductPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     * @throws \Stripe\Exception\ApiErrorException
     */
    public function run()
    {
        StripeHelper::stripeConnect();
        $products = StripeProduct::all();
        $products = $products->toArray();

        foreach ($products['data'] as $product) {
            Product::create([
                'stripe_product_identifier' => $product['id'],
                'name' => $product['name'],
                'statement_descriptor' => $product['statement_descriptor'],
                'unit_label' => $product['unit_label'],
                'active' => $product['active'],
                'created' => $product['created'],
                'metadata' => json_encode($product['metadata']),
            ]);
        }

        $plans = StripeHelper::getPlans(true);
        foreach ($plans as $plan) {
            Plan::create($plan);
        }
    }
}
