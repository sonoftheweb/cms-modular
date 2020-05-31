<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Api\ApiController;
use App\Models\Product;
use Exception;
use http\Client\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Plan;
use App\Http\Resources\PlanCollection;
use App\Helpers\InstanceHelper;
use Laravel\Cashier\Subscription;

class PaymentController extends ApiController
{

    public function index(Request $request)
    {
        if ($request->has('getIntent'))
            return $this->respond($this->getIntent($request));

        if ($request->has('payment_methods')) // should be getPaymentController
            $this->getPaymentMethod($request);

        if ($request->has('getCurrentSubscription'))
            return $this->getSubscriptionData($request);

        return false;
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function store(Request $request)
    {
        if ($request->has('spm')) {
            $this->savePaymentMethod($request);
            return $this->getPaymentMethod($request);
        }

        if ($request->has('subscribe')) {
            return $this->subscribe($request);
        }

        if ($request->has('updateSubscription'))
            return $this->updateSubscription($request);

        if ($request->has('cancelSubscription'))
            return $this->cancelSubscription($request);

        return false;
    }


    /**
     * Get's all plans in DB
     *
     * @param Request $request
     * @return mixed
     */
    public function getPlans(Request $request)
    {
        return $this->respond(new PlanCollection(Plan::all()));
    }

    /**
     * Get payment method for current user's instance
     *
     * @param Request $request
     * @return mixed
     */
    public function getPaymentMethod(Request $request)
    {
        $paymentMethods = [];
        foreach(InstanceHelper::getInstance()->paymentMethods() as $method){
            $paymentMethods[] = [
                'id' => $method->id,
                'brand' => $method->card->brand,
                'last_four' => $method->card->last4,
                'exp_month' => $method->card->exp_month,
                'exp_year' => $method->card->exp_year,
            ];
        }

        $data = [
            'count' => count($paymentMethods),
            'pm' => $paymentMethods
        ];

        return $this->respond($data);
    }

    /**
     * Creates a new intent for saving payment method
     *
     * @param Request $request
     * @return mixed
     */
    private function getIntent(Request $request)
    {
        return [
            'intent' => InstanceHelper::getInstance()->createSetupIntent()
        ];
    }

    /**
     * Save payment method to current user's instance
     *
     * @param Request $request
     * @return void
     */
    private function savePaymentMethod(Request $request)
    {
        InstanceHelper::getInstance()->addPaymentMethod($request->pm);
    }

    /**
     * Subscribe an instance to a plan
     *
     * @param Request $request
     * @return string
     */
    private function subscribe(Request $request)
    {
        try {
            list($paymentMethod, $product_id, $plan_id, $seats) = [$request->pm, $request->prod_id, $request->plan_id, $request->seat_count];

            $instance = InstanceHelper::getInstance();

            //get plan by nickname
            $plan = Plan::where('stripe_plan_identifier', $plan_id)->first()->toArray();
            $product = Product::where('stripe_product_identifier', $product_id)->first()->toArray();

            if (!$instance->subscribed('default')){
                $instance->newSubscription('default', $plan['stripe_plan_identifier'])->withMetadata([
                    'product' => $product['name'],
                    'account_name' => $instance->instance_name,
                    'account_email' => $instance->direct_email,
                    'account_seats' => $seats
                ])->quantity($seats)->create($paymentMethod['id']);
            }
            else {
                $instance->subscription('default')->swap($plan->id);
            }

            $instance->seats = $seats;
            $instance->save();

            return $this->respondWithSuccessMessage(200, 'You have been successfully subscribed to your selected plan.');

        } catch (Exception $e) {
            return $this->respondWithError($e);
        }

    }

    /**
     * Pulls in the current instance's subscription
     *
     * @param Request $request with all params and response
     *
     * @return mixed
     */
    public function getSubscriptionData(Request $request)
    {
        return $this->respond([
            'all_subscriptions' => array_filter(InstanceHelper::getInstance()->getSubscriptions(), function ($subs) {
                return $subs['status'] !== 'active';
            }),
            'subscription' => InstanceHelper::getInstance()->getSubscription(),
            'test' => InstanceHelper::getInstance()->subscribed('Gold Plan')
        ]);
    }

    /**
     * Cancel subs for this account
     *
     * @param Request $request with all params and response
     *
     * @return JsonResponse
     */
    public function cancelSubscription(Request $request)
    {
        $instance = InstanceHelper::getInstance();
        if ($request->has('cancel_now') && $request->cancel_now) {
            // this is just for the "I no do again" people.
            $instance->subscription('default')->cancelNow();
        } else {
            $instance->subscription('default')->cancel(); // this is resume able
        }

        $resp = [
            'status' => 'success',
            'message' => 'You have successfully un-subscribed from the plan.'
        ];
        return $this->respond($resp);
    }


    /**
     * Update the subscription for this account
     *
     * @param Request $request with all params and response
     *
     * @return JsonResponse
     */
    public function updateSubscription(Request $request)
    {
        // check if there are vacant seats to be removed
        $instance = InstanceHelper::getInstance();
        $instanceUsers = $instance->adminUsers();

        if ($request->seats < $instanceUsers->count()) {
            $usersToFree = $instanceUsers->count() - $request->seats;
            $verb = ($usersToFree > 1) ? ' are ' : ' is ';
            $resp = [
                'status' => 'warning',
                'message' => 'There' .
                $verb . $usersToFree . ' occupying seats to be removed. Please remove the users before you are able to free seats. Make sure that you pass the user\'s resources to another user before you delete them.'
            ];
            return $this->respond($resp);
        }

        $instance->subscription('default')
            ->skipTrial()
            ->updateQuantity($request->seats);

        $instance->update(['seats' => $request->seats]);

        $resp = [
            'status' => 'success',
            'message' => 'You have successfully altered your seat count.'
        ];

        return $this->respond($resp);
    }
}
