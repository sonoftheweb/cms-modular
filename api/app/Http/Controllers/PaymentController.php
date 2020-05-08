<?php

namespace App\Http\Controllers;

use App\Helpers\InstanceHelper;
use App\Http\Resources\PlanCollection;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
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

            if(!$instance->subscribed('default')){
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
}
