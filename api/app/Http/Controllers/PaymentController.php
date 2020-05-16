<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Api\ApiController;
use http\Client\Response;
use Illuminate\Http\Request;
use App\Models\Plan;
use App\Http\Resources\PlanCollection;
use App\Helpers\InstanceHelper;

class PaymentController extends ApiController
{
    /**
     * Get's all plans in DB
     *
     * @param Request $request
     * @return mixed
     */
    public function getPlans(Request $request)
    {
        return $request->response_helper->respond(new PlanCollection(Plan::all()));
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

        return $request->response_helper->respond($data);
    }

    /**
     * Creates a new intent for saving payment method
     *
     * @param Request $request
     * @return mixed
     */
    public function getIntent(Request $request)
    {
        return $request->response_helper->respond([
            'intent' => InstanceHelper::getInstance()->createSetupIntent()
        ]);
    }

    /**
     * Save payment method to current user's instance
     *
     * @param Request $request
     * @return mixed
     */
    public function savePaymentMethod(Request $request)
    {
        InstanceHelper::getInstance()->addPaymentMethod($request->pm);
        return $this->getPaymentMethod($request);
    }

    /**
     * Subscribe an instance to a plan
     *
     * @param Request $request
     * @return mixed
     */
    public function subscribe(Request $request)
    {
        try {
            list($paymentMethod, $plan, $seats) = [$request->pm, $request->plan, $request->seat_count];

            $instance = InstanceHelper::getInstance();

            //get plan by nickname
            $plan = Plan::where('nickname', $plan)->first()->toArray();

            $subscriptionBuilder = $instance->newSubscription('default', $plan['stripe_plan_identifier'])->withMetadata([
                'account_name' => $instance->instance_name,
                'account_email' => $instance->direct_email,
                'account_seats' => $seats
            ]);

            if (!$instance->subscribed('default')){
                $subscriptionBuilder->quantity($seats)->create($paymentMethod['id']);
            }
            else {
                $instance->subscription('default')->swap($plan->id);
            }

            $instance->seats = $seats;
            $instance->save();

            return $request->response_helper->respondWithSuccessMessage(200, 'You have been successfully subscribed to your selected plan.');

        } catch (\Exception $e) {
            return $request->response_helper->respondWithError($e);
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
        return $request->response_helper->respond([
            'all_subscriptions' => array_filter(InstanceHelper::getInstance()->getSubscriptions(), function ($subs) {
                return $subs['status'] !== 'active';
            }),
            'subscription' => InstanceHelper::getInstance()->getSubscription()
        ]);
    }

    /**
     * Cancle subs for this account
     *
     * @param Request $request with all params and response
     *
     * @return Response
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
        return $request->response_helper->respond($resp);
    }


    /**
     * Update the subscription for this account
     *
     * @param Request $request with all params and response
     *
     * @return Response
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
            return $request->response_helper->respond($resp);
        }

        $instance->subscription('default')
            ->updateQuantity($request->seats);

        $instance->update(['seats' => $request->seats]);

        $resp = [
            'status' => 'success',
            'message' => 'You have successfully altered your seat count.'
        ];

        return $request->response_helper->respond($resp);
    }
}
